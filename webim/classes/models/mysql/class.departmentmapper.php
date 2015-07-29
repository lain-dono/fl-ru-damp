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
require_once dirname(__FILE__).'/class.basemapper.php';

class DepartmentMapper extends BaseMapper
{
    public function enumDepartments($locale)
    { // TODO check for oracle inner vs outer joins
    $sql = 'SELECT dl.*, d.* FROM {'.$this->getTableName().'} d 
        LEFT OUTER JOIN {departmentlocale} dl
        ON d.departmentid=dl.departmentid 
        AND locale=:locale';
        try {
            $this->db->Query($sql, array('locale' => $locale));

            return $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return array();
        }
    }

    public function getByDepartmentKey($key)
    {
        return array_shift($r = $this->makeSearch('departmentkey = ?', $key, null, 1));
    }

    public function departmentsExist()
    {
        $sql = 'SELECT * FROM {'.$this->getTableName().'} LIMIT 1';
        try {
            $this->db->Query($sql);

            return $this->db->getNumRows() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>