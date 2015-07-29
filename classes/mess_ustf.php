<?php

/**
 * Класс управления контактами пользователя в личных пользовательских папках
 * Используется таблица mess_ustf.
 *
 * @see class mess_folders.php
 */
class mess_ustf
{
    /**
     * id записи.
     *
     * @var integer
     */
    public $id;

    /**
     * id пользователя, создавшего папку.
     *
     * @var integer
     */
    public $from_id;

    /**
     * id пользователя в папке с которым работаем.
     *
     * @var integer
     */
    public $to_id;

    /**
     * id папки с которой работаем.
     *
     * @var integer
     */
    public $folder;

    /**
     * Имя поля, таблицы mess_ustf, содержащего id serial.
     *
     * @var string
     */
    public $pr_key = 'id';

    /**
     * Добавляет/удаляет пользователя в личную папку.
     *
     * @param string $login логин пользователя, над которым производим действия
     *
     * @return integer
     */
    public function Change($login)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/classes/users.php';
        $DB = new DB();
        $ok = FALSE;
        $this->to_id = users::GetUid($error, $login);
        $res = $DB->query('SELECT * FROM messages_folders(?i)', $this->from_id);
        while ($row = pg_fetch_assoc($res)) {
            if ($row['id'] == $this->folder) {
                $ok = TRUE;
                break;
            }
        }
        if ($this->to_id && $this->from_id) {
            $id = $DB->val('SELECT messages_folders_user_move(?i, ?i, ?i)', $this->from_id, $this->to_id, $this->folder);
        }

        return $id;
    }
}

?>