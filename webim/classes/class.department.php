<?php
/* 
 * 
 * Р”Р°РЅРЅС‹Р№ С„Р°Р№Р» СЏРІР»СЏРµС‚СЃСЏ С‡Р°СЃС‚СЊСЋ РїСЂРѕРµРєС‚Р° Р’РµР± РњРµСЃСЃРµРЅРґР¶РµСЂ.
 * 
 * Р’СЃРµ РїСЂР°РІР° Р·Р°С‰РёС‰РµРЅС‹. (c) 2005-2009 РћРћРћ "РўРћРџ".
 * Р”Р°РЅРЅРѕРµ РїСЂРѕРіСЂР°РјРјРЅРѕРµ РѕР±РµСЃРїРµС‡РµРЅРёРµ Рё РІСЃРµ СЃРѕРїСѓС‚СЃС‚РІСѓСЋС‰РёРµ РјР°С‚РµСЂРёР°Р»С‹
 * РїСЂРµРґРѕСЃС‚Р°РІР»СЏСЋС‚СЃСЏ РЅР° СѓСЃР»РѕРІРёСЏС… Р»РёС†РµРЅР·РёРё, РґРѕСЃС‚СѓРїРЅРѕР№ РїРѕ Р°РґСЂРµСЃСѓ
 * http://webim.ru/license.html
 * 
 */
?>
<?php 
require_once 'common.php';
require_once 'models/generic/class.mapperfactory.php';

class Department
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function save($hash, $locale)
    {
        $d['departmentkey'] = $hash['departmentkey'];
        $dl = array('departmentname' => $hash['departmentname'], 'locale' => $locale);

        $id = null;

        if (isset($hash['departmentid'])) { // existing department
      $d['departmentid'] = $hash['departmentid'];
            $id = $d['departmentid'];

            MapperFactory::getMapper('Department')->save($d);
        } else { // new department
      $id = MapperFactory::getMapper('Department')->save($d);
        }

    // check if locale exists
    $localeid = MapperFactory::getMapper('DepartmentLocale')->getDepartmentLocale($id, $locale);

        if (!empty($localeid)) {
            $dl['departmentlocaleid'] = $localeid['departmentlocaleid'];
        }

        $dl['departmentid'] = $id;

        MapperFactory::getMapper('DepartmentLocale')->save($dl);

        return $id;
    }

    public function getById($id, $locale)
    {
        $hash1 = MapperFactory::getMapper('Department')->getById($id);
        $hash2 = MapperFactory::getMapper('DepartmentLocale')->getDepartmentLocale($id, $locale);
        if (empty($hash2)) {
            return $hash1;
        } else {
            return array_merge($hash1, $hash2);
        }
    }

    public function deleteDepartment($id)
    {
        $dl = MapperFactory::getMapper('DepartmentLocale')->getDepartmentLocale($id, $locale);
        MapperFactory::getMapper('Department')->delete($dl['departmentlocaleid']);

        MapperFactory::getMapper('DepartmentLocale')->delete($id);

        $od = MapperFactory::getMapper('OperatorDepartment')->deleteDepartment($id);
    }
}
?>