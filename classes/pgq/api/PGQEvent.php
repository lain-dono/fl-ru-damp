<?php

/**
 * PGQEvent stores URLencoded data and provides simple API
 * to access its fields.
 */
class PGQEvent
{
    public $id;
    public $time;
    public $txid;
    public $retry;
    public $type;
    public $table;
    public $data;

    public function __construct($log, $row = null)
    {
        $this->id = null;
        $this->log = $log;

        $this->tag = null;
        $this->failed_reason = null;
        $this->retry_delay = null;

        if ($row !== null) {
            $this->init_from_row($row);
        }
    }

  /**
   * Return an event from a resultset row
   * read C logutriga code in Skytools module for how to map.
   */
  public function init_from_row($row)
  {
      $this->id = (int) $row['ev_id'];
      $this->time = $row['ev_time'];
      $this->txid = $row['ev_txid'];
      $this->retry = $row['ev_retry'];
      $this->type = $row['ev_type'];
      $this->data = $this->decode($row['ev_data']);
      $this->table = $row['ev_extra1'];
      $this->extra1 = $row['ev_extra1'];
      $this->extra2 = $row['ev_extra2'];
      $this->extra3 = $row['ev_extra3'];
      $this->extra4 = $row['ev_extra4'];

      if (array_key_exists('ev_failed_reason', $row)) {
          $this->failed_reason = $row[ 'ev_failed_reason' ];
      }
      if (array_key_exists('ev_failed_time', $row)) {
          $this->failed_time = $row[ 'ev_failed_time' ];
      }
  }

  /**
   * decode urlencoded data field1=value1&field2=value2.
   * 
   * @return: array("field1" => "value1", ...);
   */
  protected function decode($data)
  {
      $this->log->debug('PGQEvent::decode(%s)', $data);
      $decoded = array();

      $pairs = explode('&', $data);
      $n = count($pairs);

      for ($i = 0; $i < $n; ++$i) {
          $pair = split('=', $pairs[$i]);
          $decoded[urldecode($pair[0])] = (string) $pair[1] === '' ? null : urldecode($pair[1]);
      }

      return $decoded;
  }

  /**
   * Compat array representation.
   */
  public function as_array()
  {
      return array('time' => $this->time,
         'txid' => $this->txid,
         'retry' => $this->retry,
         'type' => $this->type,
         'data' => $this->data,
         'table' => $this->table, );
  }

  /**
   * String representation.
   */
  public function __toString()
  {
      $data_str = '';

      foreach ($this->data as $fieldName => $value) {
          $data_str .= "\n\t[".$fieldName.'] => '.$value;
      }

      return sprintf("Event id : %10d\nTime : %s\nFailed time : %s\n".
           "Type: %s\nFailed reason : %s\nData :%s\n",
           $this->id,
           $this->time,
           $this->failed_time,
           $this->type,
           $this->failed_reason,
           $data_str);
  }

  /**
   * Tag the event, $tag is supposed to be one of
   *  PGQ_EVENT_OK, PGQ_EVENT_FAILED, PGQ_EVENT_RETRY.
   */
  public function tag($tag)
  {
      if ($tag == PGQ_EVENT_OK
        || $tag == PGQ_EVENT_FAILED
    || $tag == PGQ_EVENT_RETRY) {
          $this->tag = $tag;
      }

      return $tag;
  }
}
