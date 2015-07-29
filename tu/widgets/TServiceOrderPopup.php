<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/tservices/tservices_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/reserves/ReservesTaxes.php';

/**
 * Class TServiceOrderPopup
 * Виджет показывает попап при заказе ТУ.
 */
class TServiceOrderPopup extends CWidget
{
    public $data = array();
    public $is_emp;
    public $is_auth;

        /**
         * Инициализация попапа данными из карточки ТУ.
         * 
         * @param type $data
         */
        public function init($data = array())
        {
            parent::init();
            if (!empty($data)) {
                $this->data = $data;
            }
            $this->is_emp = is_emp();
            $this->is_auth = (get_uid(false) > 0);
        }

        /**
         * Метод сразу печатает в поток окошко попапа
         * см render.
         * 
         * @return bool
         */
        public function run()
        {
            //Для фрилансера ненужен попап
            if ($this->is_auth && !$this->is_emp) {
                return false;
            }

            $is_emp = $this->is_emp && $this->is_auth;
            $is_allowOrderReserve = tservices_helper::isAllowOrderReserve($this->data['category_id']);

            //Для анонимуса и заказчика показываем соответствующий попап с учетом доступа
            $sufix = ($is_emp) ? 'emp' : 'reg';
            //Задействуем для этого юзера и категории ТУ новую БС с резервом или нет
            $sufix .= (($is_allowOrderReserve) ? '-reserve' : '');

            if ($is_emp && $is_allowOrderReserve) {
                $reservesTaxes = ReservesTaxes::model();
                $this->data['reserveTax'] = $reservesTaxes->getTax($this->data['price'], true);
                $this->data['priceWithTax'] = $reservesTaxes->calcWithTax($this->data['price']);
                $this->data['reserveAllTaxJSON'] = json_encode($reservesTaxes->getList());
            }

            $this->render("t-service-order-popup-{$sufix}", $this->data);
        }
}
