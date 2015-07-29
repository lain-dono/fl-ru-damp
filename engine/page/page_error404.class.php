<?php

class page_error404 extends page_base
{
    public function indexAction()
    {
        header('Location: /error404/');
        exit();
    }
}
