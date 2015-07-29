<?php

class TestCase extends UnitTestCase
{
    public function TestCase()
    {
        $this->tpl = new system_tpl_layer();
    }

    public function testSettings()
    {
        $this->assertTrue(is_dir($this->tpl->getTemplatesDir()), 'Not found templates_dir');
        $this->assertTrue(is_dir($this->tpl->getCacheDir()), 'Not found cache_dir');
    }
}

$test->addTestCase(new TestCase());
