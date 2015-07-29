<?php

//new system_db_layer();

class TestCase extends UnitTestCase
{
    public function TestCase()
    {
        //        /$mock = &new Mocksystem_db_layer();
        //$this->assertIsA($mock, 'SimpleMock');
    }

    public function testSettings()
    {
        try {
            $e = system_db_layer::getInstance()->select('SELECT * FROM users LIMIT 1;')->fetchAll();
        } catch (Exception $v) {
        }
        $this->assertTrue(sizeof($e) == 1, 'Select from users error');

        $e = false;
        try {
            $e = system_db_layer::getInstance()->select('SELECT * FROM users LIMIT 1;')->fetchRow();
        } catch (Exception $v) {
        }
        $this->assertTrue(sizeof($e) > 1, 'Select Row from users error');

        $e = false;
        try {
            $e = system_db_layer::getInstance()->select('SELECT * FROM users LIMIT 1;')->fetchOne();
        } catch (Exception $v) {
        }
        $this->assertTrue(sizeof($e) == 1, 'Select One Cell from users error');

        $e = false;
        try {
            $e = system_db_layer::getInstance()->select('SELECT * FROM users WHERE uid > ? LIMIT 1;', 10000)->fetchRow();
        } catch (Exception $v) {
        }
        $this->assertTrue(sizeof($e) > 1, 'Select Row with placeholder from users error');
        $this->assertTrue(false, 'РћС€РёР±РєР° С„СѓРЅРєС†РёРё Р±РёР»Р»РёРЅРіР°');
        $this->assertTrue(false, 'РќРµС‚ РєР»Р°СЃСЃР°');
        $this->assertTrue(false, 'РќРµ РїСЂР°РІРёР»СЊРЅРѕ РІРѕР·РІСЂР°С‰РµРЅ СЂРµР·СѓР»СЊС‚Р°С‚ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ');
    }
}

$test->addTestCase(new TestCase());
