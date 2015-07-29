<?php

/**
 * log levels.
 */
define('FATAL',   60);
define('ERROR',   50);
define('WARNING', 40);
define('NOTICE',  30);
define('VERBOSE', 20);
define('DEBUG',   10);

define('DEFAULT_TZ', 'Europe/Moscow');

class SimpleLogger
{
    private $logfile_fd = false;
    public $loglevel = WARNING;
    public $logfile;
    public $is_daemon;

    public function __construct($loglevel, $logfile, $is_daemon = null)
    {
        $this->loglevel = $loglevel;
        $this->is_daemon = $is_daemon;

        if (empty($logfile)) {
            $this->logfile = sprintf('%s.log', tempnam('/tmp', 'SimpleLogger-'));
        } else {
            $this->logfile = $logfile;
        }

        date_default_timezone_set(DEFAULT_TZ);
        $this->open();
    }

    public function __destruct()
    {
        $this->notice('Closing log file '.$this->logfile);
        fclose($this->logfile_fd);
    }

  /**
   * Opens the given filename, or use the given stream resource (STDOUT).
   */
  private function open()
  {
      if (@fstat($this->logfile) !== false) {
          $this->logfile_fd = $this->logfile;
      } else {
          $this->logfile_fd = fopen($this->logfile, 'a+');
      }

      if ($this->logfile_fd === false) {
          fprintf(STDERR, "FATAL: couldn't open '%s' \n", $this->logfile);
      } else {
          $this->notice("Logging to file '%s'", $this->logfile);
      }
  }

  /**
   * At reload time, don't forget to reopen $this->logfile
   * This allows for log rotating.
   */
  public function reopen()
  {
      $this->warning('Closing log file '.$this->logfile);
      fclose($this->logfile_fd);
      $this->logfile_fd = false;
      $this->open();
  }

  /**
   * Check that the logfile has been opened with success.
   *
   * @return bool
   */
  public function check()
  {
      return $this->logfile_fd !== false;
  }

    public function debug()
    {
        $args = func_get_args();
        $this->_log(DEBUG, $args);
    }

    public function verbose()
    {
        $args = func_get_args();
        $this->_log(VERBOSE, $args);
    }

    public function notice()
    {
        $args = func_get_args();
        $this->_log(NOTICE, $args);
    }

    public function warning()
    {
        $args = func_get_args();
        $this->_log(WARNING, $args);
    }

    public function error()
    {
        $args = func_get_args();
        $this->_log(ERROR, $args);
    }

    public function fatal()
    {
        $args = func_get_args();
        $this->_log(FATAL, $args);
    }

    public function _log($level, $args)
    {
        if ($level >= $this->loglevel) {
            $format = array_shift($args);
            $date = date('Y-m-d H:i:s');
            if ($this->is_daemon !== null) {
                $vargs = array_merge(array($date, ($this->is_daemon ? 'DAEMON' : 'YOU   '), $this->strlevel($level)), $args);
                $mesg = vsprintf("%s\t%s\t%s\t".$format."\n", $vargs);
            } else {
                $vargs = array_merge(array($date, $this->strlevel($level)), $args);
                $mesg = vsprintf("%s\t%s\t".$format."\n", $vargs);
            }

            fwrite($this->logfile_fd, $mesg);
        }
    }

    public function strlevel($level)
    {
        switch ($level) {
    case DEBUG:
      return 'DEBUG  ';
      break;

    case VERBOSE:
      return 'VERBOSE';
      break;

    case NOTICE:
      return 'NOTICE ';
      break;

    case WARNING:
      return 'WARNING';
      break;

    case ERROR:
      return 'ERROR  ';
      break;

    case FATAL:
      return 'FATAL  ';
      break;

    default:
      return $level;
    }
    }

  /**
   * On the fly log level control utility functions.
   */
  public function logless()
  {
      $this->_log($this->loglevel, array('Incrementing loglevel'));

      if ($this->loglevel < FATAL) {
          $this->loglevel += 10;
      }

      $this->_log($this->loglevel,
        array('loglevel is now %s', $this->strlevel($this->loglevel)));
  }

    public function logmore()
    {
        $this->_log($this->loglevel, array('Decrementing loglevel'));

        if ($this->loglevel > DEBUG) {
            $this->loglevel -= 10;
        }

        $this->_log($this->loglevel,
        array('loglevel is now %s', $this->strlevel($this->loglevel)));
    }
}
