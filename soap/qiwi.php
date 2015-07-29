<?php

/**
 * На этот скрипт приходят уведомления от QIWI Кошелька.
 * SoapServer парсит входящий SOAP-запрос, извлекает значения тегов login, password, txn, status,
 * помещает их в объект класса Param и вызывает функцию updateBill объекта класса TestServer.
 *
 * Логика обработки магазином уведомления должна быть в updateBill.
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/qiwi_soap.php';

$soap = new SoapServer('IShopClientWS.wsdl', array('classmap' => array('tns:updateBill' => 'Param', 'tns:updateBillResponse' => 'Response')));

$soap->setClass('QiwiServer');
$soap->handle();

class Response
{
    public $updateBillResult;
}

class Param
{
    public $login;
    public $password;
    public $txn;
    public $status;
}

class QiwiServer
{
    public function updateBill($param)
    {
        // В зависимости от статуса счета $param->status меняем статус заказа в магазине
        if ($param->status = 60) {
            // заказ оплачен
            // найти заказ по номеру счета ($param->txn), пометить как оплаченный
        } elseif ($param->status > 100) {
            // заказ не оплачен (отменен пользователем, недостаточно средств на балансе и т.п.)
            // найти заказ по номеру счета ($param->txn), пометить как неоплаченный
        } elseif ($param->status >= 50 && $param->status < 60) {
            // счет в процессе проведения
        } else {
            // неизвестный статус заказа
        }

        // формируем ответ на уведомление
        // если все операции по обновлению статуса заказа в магазине прошли успешно, отвечаем кодом 0
        // $temp->updateBillResult = 0
        // если произошли временные ошибки (например, недоступность БД), отвечаем ненулевым кодом
        // в этом случае QIWI Кошелёк будет периодически посылать повторные уведомления пока не получит код 0
        // или не пройдет 24 часа
        $temp = new Response();
        $temp->updateBillResult = 0;

        return $temp;
    }
}
