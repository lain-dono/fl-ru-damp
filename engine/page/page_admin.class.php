<?php

class page_admin extends page_base
{
    public function indexAction()
    {
        front::og('tpl')->display('admin.tpl');
    }
}
