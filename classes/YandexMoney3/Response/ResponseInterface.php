<?php

namespace YandexMoney3\Response;

interface ResponseInterface
{
    /**
     * @return string
     */
    public function getError();

    /**
     * @return bool
     */
    public function isSuccess();
}
