<?php

/**
 * Подключаем файл с основными функциями.
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/stdf.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/users.php';

/**
 * Класс для контроля отправления уведомлений который отправляются только один раз.
 */
class single_send
{
    // Уведомление о веб-кошельке @see pmail::SbrMoneyPaidFrl()
    const NOTICE_WEBM = 0x0001;

    protected $_bit = 0;
    protected $_user;

    public function __construct($user = false)
    {
        $this->setUser($user);
        $this->setBit($this->_user->single_send);
    }

    public function setUser($user)
    {
        $this->_user = $user ? $user : new users();
    }

    public function setBit($bit)
    {
        $this->_bit += $bit;
    }

    public function getBit()
    {
        return $this->_bit;
    }

    public function is_send($type)
    {
        return ($this->getBit() & $type);
    }

    public function setUpdateBit($type)
    {
        $this->setBit($type);
        $this->_user->single_send = $this->getBit();
        $this->_user->update($this->_user->uid, $error);
    }
}
