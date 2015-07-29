<?php


class Form_Filter_Carusel implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $value = change_q_x($value, true, false);
        $value = strtolower(strtr($value, 'ЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ', 'ёйцукенгшщзхъфывапролджэячсмитьбю'));
        $value = preg_replace('/(^|[.!?]\s+)([a-zа-я])/ie', "'$1'.strtoupper(strtr ( '$2', 'ёйцукенгшщзхъфывапролджэячсмитьбю', 'ЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ' ))", $value);
        $value = str_replace("\r\n", "\n", $value);

        return $value;
    }
}
