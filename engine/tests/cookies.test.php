<?php

class TestOfLogging extends UnitTestCase
{
    public function testLogCreatesNewFileOnFirstMessage()
    {
        $this->assertFalse(file_exists('/temp/test.log'));
    }
}

$test->addTestCase(new TestOfLogging());
