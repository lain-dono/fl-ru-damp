<?php

namespace YandexMoney3\Domain;

class Base
{
    /**
     * @var array of mixed
     */
    protected $params = array();

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $key of response parameter
     */
    public function checkAndReturn($key)
    {
        $value = null;
        if (array_key_exists($key, $this->params)) {
            $value = $this->params[$key];
        }

        return $value;
    }
}
