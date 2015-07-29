<?php

require_once __DIR__.'/ReservesPayException.php';

class ReservesPaybackException extends ReservesPayException
{
    const INSERT_FAIL_MSG = 'Не удается добавить запрос за возврат средств.';
    const ALREADY_PAYBACK_MSG = 'Средства запроса уже были возвращены.';
    const PAYBACK_INPROGRESS = 'Запрос в процессе возврата средств.';
    const PAYBACK_NOTFOUND = 'Запрос на возврат средств не найден.';
    const UNDEFINED_STATUS = 'На запрос возврата средств был получен не известный статус.';
    const CANT_CHANGE_SUBSTATUS = 'На запрос возврата средств не удалось сменить подстатус резерва.';
}
