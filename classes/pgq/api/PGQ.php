<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/pgq/api/PGQEvent.php';

/*
 * If PHP new about modules or namespaces, this would be a PGQ module.
 *
 * It's an abstract PGQ class containing only static methods which you
 * simple use like modules and functions in other languages:
 *
 *  $batch_id = PGQ::next_batch();
 */

// No sense is given nor necessary for those constants, as soon as
// there's no colision.
define('PGQ_EVENT_OK',     1);
define('PGQ_EVENT_FAILED', 2);
define('PGQ_EVENT_RETRY',  5);
define('PGQ_ABORT_BATCH', 11);

abstract class PGQ
{
    /**
   * Queue creation.
   */
  public static function create_queue($log, $pgcon, $qname)
  {
      $sql = sprintf("SELECT pgq.create_queue('%s');", pg_escape_string($qname));
      $log->verbose('create_queue: %s', $sql);

      $r = pg_query($pgcon, $sql);
      if ($r === false) {
          $log->fatal("Could not create queue '%s'", $qname);

          return false;
      }
      $result = (pg_fetch_result($r, 0, 0) == 1);

      if (!$result) {
          $log->fatal('PGQConsumer: could not create queue.');
      }

      return $result;
  }

  /**
   * Queue drop.
   */
  public static function drop_queue($log, $pgcon, $qname)
  {
      $sql = sprintf("SELECT pgq.drop_queue('%s');", pg_escape_string($qname));
      $log->verbose('drop_queue: %s', $sql);

      $r = pg_query($pgcon, $sql);
      if ($r === false) {
          $log->fatal("Could not drop queue '%s'", $qname);

          return false;
      }

      return pg_fetch_result($r, 0, 0) == 1;
  }

  /**
   * Queue exists?
   */
  public static function queue_exists($log, $pgcon, $qname)
  {
      $sql = sprintf('SELECT * FROM pgq.get_queue_info()');

      $log->verbose('%s', $sql);
      if (($r = pg_query($pgcon, $sql)) === false) {
          $log->error('Could not get queue info');

          return false;
      }
      $queues = array();
      $resultset = pg_fetch_all($r);

      if ($resultset === false) {
          $log->notice('PGQConsumer.queue_exists() got no queue.');

          return false;
      }

      foreach ($resultset as $row) {
          if ($row['queue_name'] == $qname) {
              return true;
          }
      }

      return false;
  }

  /**
   * Register PGQ Consumer.
   *
   * @return: boolean
   */
  public static function register($log, $pgcon, $qname, $cname)
  {
      $sql = sprintf("SELECT pgq.register_consumer('%s', '%s');",
           pg_escape_string($qname),
           pg_escape_string($cname));

      $log->verbose('%s', $sql);
      if (($r = pg_query($pgcon, $sql)) === false) {
          $log->warning("Could not register consumer '%s' to queue '%s'",
            $cname, $qname);

          return false;
      }

      $registered = pg_fetch_result($r, 0, 0);
      if ($registered == '1') {
          return true;
      } else {
          $log->fatal('Register Consumer failed (%d).', $registered);

          return false;
      }
  }

  /**
   * Unregister PGQ Consumer. Called from stop().
   */
  public static function unregister($log, $pgcon, $qname, $cname)
  {
      $sql = sprintf("SELECT pgq.unregister_consumer('%s', '%s');",
           pg_escape_string($qname),
           pg_escape_string($cname));

      $log->verbose('%s', $sql);
      if (($r = pg_query($pgcon, $sql)) === false) {
          $log->fatal("Could not unregister consumer '%s' to queue '%s'",
          $cname, $qname);

          return false;
      }

      $unregistered = pg_fetch_result($r, 0, 0);
      if ($unregistered == '1') {
          return true;
      } else {
          $log->fatal('Register Consumer failed (%d).', $unregistered);

          return false;
      }
  }

  /**
   * are we registered already?
   */
  public static function is_registered($log, $pgcon, $qname, $cname)
  {
      $infos = self::get_consumer_info($log, $pgcon, $qname, $cname);

      if ($infos !== false) {
          $log->debug('is_registered %s',
          ($infos['queue_name'] == $qname
            && $infos['consumer_name'] == $cname
            ?
            'True' : 'False'));

          return $infos['queue_name'] == $qname
    && $infos['consumer_name'] == $cname;
      }
      $log->warning('is_registered: count not get consumer infos.');

      return false;
  }

  /**
   * get_consumer_info.
   */
  public static function get_consumer_info($log, $pgcon, $qname, $cname)
  {
      $sq = sprintf("SELECT * FROM pgq.get_consumer_info('%s', '%s')",
          pg_escape_string($qname),
          pg_escape_string($cname));

      $log->debug('%s', $sq);
      $result = pg_query($pgcon, $sq);

      if ($result === false) {
          $log->warning("Could not get consumer info for '%s'", $cname);

          return false;
      }

      if (pg_num_rows($result) == 1) {
          return pg_fetch_assoc($result, 0);
      } else {
          $log->warning("get_consumer_info('%s', '%s') did not get 1 row.",
            $qname, $cname);

          return false;
      }
  }

  /**
   * get_consumers returns a list of consumers attached to the queue.
   */
  public static function get_consumers($log, $pgcon, $qname)
  {
      $sq = sprintf("SELECT * FROM pgq.get_consumer_info('%s')",
          pg_escape_string($qname));

      $log->debug('%s', $sq);
      $result = pg_query($pgcon, $sq);
      $resultset = pg_fetch_all($result);

      if ($result === false or $resultset === false) {
          $log->warning("Could not get consumers list for '%s'", $qname);

          return false;
      }
      $clist = array();

      foreach ($resultset as $row) {
          $clist[] = $row;
      }

      return $clist;
  }

  /**
   * Get next batch id.
   *
   * Returns null when pgq.next_batch() returns null or failed.
   */
  public static function next_batch($log, $pgcon, $qname, $cname)
  {
      $sql = sprintf("SELECT pgq.next_batch('%s', '%s')",
           pg_escape_string($qname),
           pg_escape_string($cname));

      $log->verbose('%s', $sql);
      if (($r = pg_query($pgcon, $sql)) === false) {
          $log->error('Could not get next batch');

          return false;
      }

      $batch_id = pg_fetch_result($r, 0, 0);
      $log->debug('Get batch_id %s (isnull=%s)',
        $batch_id,
        ($batch_id === null ? 'True' : 'False'));

      return $batch_id;
  }

  /**
   * Finish Batch.
   */
  public static function finish_batch($log, $pgcon, $batch_id)
  {
      $sql = sprintf('SELECT pgq.finish_batch(%d);', (int) $batch_id);

      $log->verbose('%s', $sql);
      if (pg_query($pgcon, $sql) === false) {
          $log->error('Could not finish batch %d', (int) $batch_id);

          return false;
      }

      return true;
  }

  /**
   * Get batch events.
   * 
   * @return array(PGQEvents);
   */
  public static function get_batch_events($log, $pgcon, $batch_id)
  {
      $sql = sprintf('SELECT * FROM pgq.get_batch_events(%d)', (int) $batch_id);

      $log->verbose('%s', $sql);
      if (($r = pg_query($pgcon, $sql)) === false) {
          $log->error('Could not get next batch events from batch %d',
          $batch_id);

          return false;
      }
      $events = array();
      $resultset = pg_fetch_all($r);

      if ($resultset === false) {
          $log->notice("get_batch_events(%d) got 'False' ".
           '(empty list or error)', $batch_id);

          return false;
      }

      foreach ($resultset as $row) {
          $events[] = new PGQEvent($log, $row);
      }

      return $events;
  }

  /**
   * Mark event as failed.
   */
  public static function event_failed($log, $pgcon, $batch_id, $event)
  {
      $sql = sprintf("SELECT pgq.event_failed(%d, %d, '%s');",
           (int) $batch_id,
           (int) $event->id,
           pg_escape_string($event->failed_reason));

      $log->verbose('%s', $sql);
      if (pg_query($pgcon, $sql) === false) {
          $log->error('Could not mark failed event %d from batch %d',
          (int) $event->id, (int) $batch_id);

          return false;
      }

      return true;
  }

  /**
   * Mark event for retry.
   */
  public static function event_retry($log, $pgcon, $batch_id, $event)
  {
      $sql = sprintf('SELECT pgq.event_retry(%d, %d, %d);',
           (int) $batch_id,
           (int) $event->id,
           (int) $event->retry_delay);

      $log->verbose('%s', $sql);
      if (pg_query($pgcon, $sql) === false) {
          $log->error('Could not retry event %d from batch %d',
          (int) $event->id, (int) $batch_id);

          return false;
      }

      return true;
  }

  /**
   * failed_event_list
   * returns array(PGQEvent).
   */
  public static function failed_event_list($log, $pgcon, $qname, $cname)
  {
      $sql = sprintf("SELECT * FROM pgq.failed_event_list('%s', '%s')",
                   pg_escape_string($qname),
                   pg_escape_string($cname));

      $log->verbose('%s', $sql);
      if (($r = pg_query($pgcon, $sql)) === false) {
          $log->error('Could not get next failed event list');

          return false;
      }
      $events = array();
      $resultset = pg_fetch_all($r);

      if ($resultset === false) {
          $log->notice("failed_event_list(%d) got 'False' ".
           '(empty list or error)', $batch_id);

          return false;
      }

      foreach ($resultset as $row) {
          $event = new PGQEvent($log, $row);
          $events[$event->id] = $event;
      }

      return $events;
  }

  /**
   * Helper function failed_event_delete_all.
   */
  public static function failed_event_delete_all($log, $pgcon,
                         $qname, $cname)
  {
      $allok = true;
      foreach (self::failed_event_list($log, $pgcon, $qname, $cname)
         as $event_id => $event) {
          $allok = $allok && self::failed_event_delete($log, $pgcon,
                          $qname, $cname, $event_id);
          if (!$allok) {
              return false;
          }
      }

      return true;
  }

  /**
   * failed_event_delete.
   */
  public static function failed_event_delete($log, $pgcon,
                         $qname, $cname, $event_id)
  {
      $sql = sprintf("SELECT pgq.failed_event_delete('%s', '%s', %d)",
           pg_escape_string($qname),
           pg_escape_string($cname),
           $event_id);

      $log->debug('%s', $sql);
      $result = pg_query($pgcon, $sql);

      if ($result === false) {
          $log->error('Could not delete failed event %d', $event_id);

          return false;
      }
      if (pg_num_rows($result) == 1) {
          $event = new PGQEvent($log, pg_fetch_assoc($result, 0));
          echo $event."\n";

          return true;
      } else {
          $log->warning("failed_event_delete('%s', '%s', %d) did not get 1 row.",
            $qname, $cname, $event_id);

          return false;
      }

      return true;
  }

  /**
   * Helper function failed_event_retry_all.
   */
  public static function failed_event_retry_all($log, $pgcon, $qname, $cname)
  {
      $allok = true;

      foreach (self::failed_event_list($log, $pgcon, $qname, $cname)
         as $event_id => $event) {
          $allok = $allok && self::failed_event_retry($log, $pgcon,
                         $qname, $cname, $event_id);
          if (!$allok) {
              return false;
          }
      }

      return true;
  }

  /**
   * failed_event_retry.
   */
  public static function failed_event_retry($log, $pgcon,
                        $qname, $cname, $event_id)
  {
      $sql = sprintf("SELECT pgq.failed_event_retry('%s', '%s', %d)",
           pg_escape_string($qname),
           pg_escape_string($cname),
           $event_id);

      $log->debug('%s', $sql);
      $result = pg_query($pgcon, $sql);

      if ($result === false) {
          $log->error('Could not retry failed delete event %d', $event_id);

          return false;
      }
      if (pg_num_rows($result) == 1) {
          $event = new PGQEvent($log, pg_fetch_assoc($result, 0));
          echo $event."\n";

          return true;
      } else {
          $log->error("failed_event_retry('%s', '%s', %d) did not get 1 row.",
          $qname, $cname, $event_id);

          return false;
      }

      return true;
  }
}
