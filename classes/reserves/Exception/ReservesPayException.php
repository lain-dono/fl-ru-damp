<?php


class ReservesPayException extends Exception
{
    const RESERVE_NOTFOUND = 'БС не найдена.';
    const RESERVE_STATUS_FAIL = 'Статус БС не подходящий для операции.';
    const API_CRITICAL_FAIL = 'Невозможно повторить запрос. Код ошибки API: %d.';
    const REQUEST_LIMIT = 'Превышен лимит в более 999 запросов.';

    protected $repeat = false;

    public function __construct()
    {
        $args = func_get_args();
        $cnt = count($args);

        if ($cnt > 0) {
            $message = current($args);
            if ($cnt > 1) {
                $this->repeat = (end($args) === true);
                unset($args[$cnt - 1], $args[0]);
                $message = (count($args)) ? vsprintf($message, $args) : $message;
            }

            parent::__construct($message);
        }
    }

    public function isRepeat()
    {
        return $this->repeat;
    }
}
