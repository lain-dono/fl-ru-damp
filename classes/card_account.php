<?php

/**
 * Класс для работы с пластиковыми картами пользователей.
 */
class card_account
{
    /**
	 * ИД операции.
	 *
	 * @var integer
	 */
	public $id;
	/**
	 * ИД аккаунта пользователя (accounts.id).
	 *
	 * @var integer
	 */
	public $account_id;
	/**
	 * Дата создания.
	 *
	 * @var data
	 */
	public $post_date;

	/**
	 * Проверка оплаты пользователя карты (проверяем оплачено ли, если да то удаляем).
	 *
	 * @param integer $id ИД карты
 *
	 * @return integer если оплачено возвращаем ИД аккаунта, 0 - если нет 
	 */
	public function checkPayment($id)
	{
	    global $DB;
	    $this->account_id = NULL;
	    if ($row = $this->getPayments($id)) {
	        foreach ($row as $key => $val) {
	            $this->$key = $val;
	        }
	        if ($this->id) {
	            $DB->query('DELETE FROM card_account WHERE id = ?', $this->id);
	        }
	    }

	    return (int) $this->account_id;
	}

    public function getPayments($id = NULL)
    {
        global $DB;
        $m = 'rows';
        if ($id) {
            $where = 'WHERE id = ?';
            $m = 'row';
        }

        return $DB->$m("SELECT * FROM card_account {$where} ORDER BY id", $id);
    }

	/**
	 * Добавить запись.
	 * 
	 * @return идентификатор добавленной записи
	 */
	public function Add()
	{
	    global $DB;

	    $aData = array('account_id' => $this->account_id);
	    $mRes = $DB->insert('card_account', $aData, 'id');

	    if ($DB->error) {
	        $sError = $DB->error;

	        return -1;
	    } else {
	        return $mRes;
	    }
	}
}
?>