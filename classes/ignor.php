<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/stdf.php';

/**
 * Класс для работы с игнорированием пользователей друг друга (Личных сообщений).
 */
class ignor
{
    protected $DB;

	/**
     * Добавляет пользователя в список игнорирования.
     *
     * @param integer $user_id      id пользователя, добавляющего другого в игнор-лист
     * @param string  $target_login логин пользователя, добаляемого в игнор-лист
     *
     * @return string пустая строка или сообщение об ошибке в случае неуспеха
     */    
    public function Add($user_id, $target_login)
    {
        global $usersNotBeIgnored;
        if (empty($user_id) || empty($target_login) || in_array($target_login, $usersNotBeIgnored)) {
            return false;
        }
        $user = new users();
        $user->login = $target_login;
        $target_id = $user->GetUid($error);
        $DB = new DB();
        $r = $DB->val('SELECT ignor_add(?i, ?i)', $user_id, $target_id);

        return '';
    }

    /**
     * Добавляет пользователей в список игнорирования.
     *
     * @param integer $user_id  id пользователя, добавляющего других в игнор-лист
     * @param array   $selected id пользователей, добавляемых в игнор-лист
     *
     * @return string пустая строка или сообщение об ошибке в случае неуспеха
     */    
    public function AddEx($user_id, $selected)
    {
        $DB = new DB();
        if (!empty($user_id) && is_array($selected) && count($selected)) {
            $DB->query('SELECT ignor_add(?i, ?a)', $user_id, $selected);
            $error = '';
        } else {
            $error = 'Необходимо выбрать хотя бы один контакт';
        }

        return $errors;
    }

    /**
     * Удаляет пользователя из списка игнорирования.
     *
     * @param integer $user_id  id пользователя, удаляющего другого из игнор-листа
     * @param array   $selected id пользователей, удаляемого из игнор-листа
     *
     * @return string пустая строка или сообщение об ошибке в случае неуспеха
     */    
    public function Del()
    {
        $DB = new DB();
        if (empty($this->user_id) || empty($this->target_id)) {
            return 'Вы ну указали контакт';
        }
        $DB->query('SELECT ignor_del(?i, ?)', $this->user_id, $this->target_id);

        return '';
    }

    /**
     * Удаляет пользователей из списка игнорирования.
     *
     * @param integer $user_id  id пользователя, удаляющего других из игнор-листа
     * @param array   $selected id пользователей, удаляемых из игнор-листа
     *
     * @return string пустая строка или сообщение об ошибке в случае неуспеха
     */    
    public function DeleteEx($user_id, $selected)
    {
        if (is_numeric($selected)) {
            $selected = array($selected);
        }
        $DB = new DB();
        if (!empty($user_id) && is_array($selected) && count($selected)) {
            $DB->query('SELECT ignor_del(?i, ?a)', $user_id, $selected);
            $error = '';
        } else {
            $error = 'Необходимо выбрать хотя бы один контакт';
        }

        return $error;
    }

    /**
     * Проверка польователя на нахождениии в игнор-листе.
     *
     * @param integer $from_id id пользователя, владельца игнор-листа
     * @param array   $tar_id  id пользователей, которого проверяем
     *
     * @return integer 0 - нет, 1 - есть
     */    
    public function CheckIgnored($from_id, $tar_id)
    {
        $DB = new DB();
        $r = $DB->val('SELECT ignor_check(?i, ?i)', $from_id, $tar_id);

        return $r;
    }

    /**
     * Проверка польователя на нахождениии в игнор-листе.
     * В случае нахождения удаляет пользователя из списка, иначе добавляет.
     *
     * @param integer $login логин пошльзователя, которого проверяем
     *
     * @return string пустая строка или сообщение об ошибке в случае неуспеха
     */    
    public function Change($login)
    {
        $DB = new DB();
        $r = $DB->val('SELECT ignor_check(?, ?)', $this->user_id, $login);
        if ($r) {
            $this->target_id = $login;
            $this->Del();
        } else {
            $this->Add($this->user_id, $login);
        }

        return $r;
    }
}

?>