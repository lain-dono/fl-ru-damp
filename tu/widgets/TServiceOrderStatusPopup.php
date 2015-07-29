<?php

/**
 * Class TServiceOrderStatusPopup
 * Виджет показывает попап фрилансеру перед сменой статуса с заказе ТУ.
 */
class TServiceOrderStatusPopup extends CWidget
{
    public $data = array();

    public function init($data = array())
    {
        parent::init();
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    public function run()
    {
        //собираем шаблон
        $this->render('t-service-order-status-frl-popup', $this->data);
    }
}
