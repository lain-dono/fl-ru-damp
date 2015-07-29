<?php

require_once ABS_PATH.'/classes/external/base.php';

/**
 * Сессии авторизированного API-клиента. Хранятся в мемкэше.
 * По истечении заданного в константе периода данные обновляются.
 */
class externalSession extends externalBase
{
    const MEM_TIME = 7200; // время жизни сессии в мемкэше
    const MEM_GROUP = 'EXTERNAL_SESSIONS';
    const CACHE_TIME = 360; // период обновления сессионных данных. (На заметку: 300 равносильно 301-599, т.к. клиент
                            // обновляется минимум раз в 5 минут, но на доли секунды меньше 300, что считается как 299.)

    private $_mb;

    /**
     * Хранит публичные данные сессии -- те, к которым имеет доступ клиент.
     *
     * @var array
     */
    public $public = array(
        'id' => null,
        'login' => null,
        'uname' => null,
        'usurname' => null,
        'role' => null,
        'is_pro' => null,
        'sum' => null,
        'new_msgs' => null,
        'sbr_count' => null,
        'new_sbr_events' => null,
    );

    /**
     * Скрытые данные сессии -- те, к которым имеет доступ только сервер.
     *
     * @var array
     */
    public $private = array(
        'uid' => null,
        'last_updated' => null,
    );

    /**
     * Указывает, обновились ли публичные данные во время работы.
     *
     * @var bool
     */
    public $is_updated = false;

    /**
     * Читает сессию из кэша, если клиент передал идентификатор.
     *
     * @param string $id идентификатор сессии
     */
    public function __construct($id = null)
    {
        $this->_mb = new memBuff();
        if ($id) {
            $this->read($id);
        }
    }

    /**
     * Получить свойство сессии:
     *   $this->prop -- свойство будет искаться в $this->public
     *   $this->_prop -- свойство будет искаться в $this->private.
     *
     * @param string $f имя свойства
     *
     * @return mixed значение
     */
    public function __get($f)
    {
        $d = $this->public;
        if ($f[0] == '_') {
            $d = $this->private;
            $f = substr($f, 1);
        }

        return (isset($d[$f]) ? $d[$f] : null);
    }

    /**
     * Установить свойство сессии:
     *   $this->prop = 1 -- свойство будет установлено в $this->public
     *   $this->_prop = 1 -- свойство будет установлено в $this->private.
     *
     * @param string $f имя свойства
     * @param mixed  $v значение
     */
    public function __set($f, $v)
    {
        $d = &$this->public;
        if ($f[0] == '_') {
            $d = &$this->private;
            $f = substr($f, 1);
        } else {
            if ($d[$f] !== $v) {
                $this->is_updated = true;
            }
        }
        $d[$f] = $v;
    }

    private function _uidMemKey($uid)
    {
        return self::MEM_GROUP.'-'.$uid;
    }

    /**
     * Читает сессию из мемкэша, обновляет данные, если считает, что они устарели.
     *
     * @param string $id ид. сессии.
     */
    public function read($id)
    {
        if ($id) {
            if ($data = $this->_mb->get($id)) {
                list($this->public, $this->private) = $data;
                if (time() - $this->_last_updated >= self::CACHE_TIME) {
                    $this->refresh();
                }

                return;
            }
            $this->error(EXTERNAL_ERR_SESSION_EXPIRED);
        }
    }

    /**
     * Сохраняет сессию в мемкэш.
     */
    public function write()
    {
        if ($this->id) {
            $this->_mb->set($this->id, array($this->public, $this->private), self::MEM_TIME);
            $this->_mb->set($this->_uidMemKey($this->_uid), $this->id, self::MEM_TIME);
        }
    }

    /**
     * Уничтожает сессию из кэша.
     */
    public function destroy($uid = null)
    {
        if ($uid) {
            $muk = $this->_uidMemKey($uid);
            $this->id = $this->_mb->get($muk);
        } else {
            $muk = $this->_uidMemKey($this->_uid);
        }
        if ($this->id) {
            $this->_mb->delete($this->id);
            $this->_mb->delete($muk);
            $this->id = null;
        }
    }

    /**
     * Обновляет данные сессии.
     */
    public function refresh()
    {
        if ($this->id) {
            $this->destroy();
            $this->fill($this->_uid);
        }
    }

    /**
     * Инициализирует все данные в сессии.
     *
     * @param users $user инициализирванный объект users.
     */
    public function fillU($user)
    {
        if (!$user->uid) {
            $this->error(EXTERNAL_ERR_USER_NOTFOUND);
        }
        if ($user->is_banned) {
            $this->error(EXTERNAL_ERR_USER_BANNED);
        }
        if ($user->active != 't') {
            $this->error(EXTERNAL_ERR_USER_NOTACTIVE);
        }

        $data = get_object_vars($user);
        unset($user);

        require_once ABS_PATH.'/classes/sbr.php';
        require_once ABS_PATH.'/classes/sbr_meta.php';
        require_once ABS_PATH.'/classes/messages.php';
        require_once ABS_PATH.'/classes/account.php';

        $is_emp = is_emp($data['role']);

        $data['id'] = md5(self::MEM_GROUP.uniqid($data['uid']));
        $data['new_msgs'] = messages::GetNewMsgCount($data['uid']);
        $data['role'] = (int) $is_emp;
        $data['is_pro'] = $this->pg2ex($data['is_pro'], EXTERNAL_DT_BOOL);

        $sbr_cls = $is_emp ? 'sbr_emp' : 'sbr_frl';
        $sbr = new $sbr_cls($data['uid'], $data['login']);
        $data['sbr_count'] = $sbr->getActivesCount();
        $data['new_sbr_events'] = sbr_meta::getNewEventCount($data['uid']);

        $account = new account();
        $account->GetInfo($data['uid']);
        $data['sum'] = $account->sum;

        foreach ($this->public as $f => $v) {
            $this->$f = $data[$f];
        }

        $this->_uid = $data['uid'];
        $this->_last_updated = time();
    }

    /**
     * Инициализирует все данные в сессии.
     *
     * @param int $uid ид. пользователя.
     */
    public function fill($uid)
    {
        require_once ABS_PATH.'/classes/users.php';
        $user = new users();
        $user->GetUserByUID($uid);
        $this->fillU($user);
    }

    /**
     * Сохраняем сессию по завершении работы.
     */
    public function __destruct()
    {
        $this->write();
    }
}
