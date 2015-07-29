<?php

abstract class londiste
{
    public $master_alias;
    protected $_qtype = 'rows';
    protected $_helper = array();

    public function __construct($master_alias = 'master')
    {
        $this->master_alias = $master_alias;
        $this->_loadHelperInfo();
    }

    protected function _loadHelperInfo()
    {
    }

    public static function instance($type, $master_alias = 'master')
    {
        $class = 'londiste__'.strtolower($type);
        require_once dirname(__FILE__)."/{$class}.php";

        return new $class($master_alias);
    }

    public function rows()
    {
        $this->_qtype = 'rows';
        $args = func_get_args();

        return call_user_func_array(array($this, 'select'), $args);
    }

    public function row()
    {
        $this->_qtype = 'row';
        $args = func_get_args();

        return call_user_func_array(array($this, 'select'), $args);
    }
}
