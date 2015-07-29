<?php

class static_pages
{
    public static function get($alias)
    {
        return front::og('db')->select('SELECT * FROM static_pages WHERE alias = ? LIMIT 1;', $alias)->fetchRow();
    }
}
