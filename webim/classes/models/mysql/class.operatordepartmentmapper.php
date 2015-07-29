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

class OperatorDepartmentMapper extends BaseMapper
{
    public function deleteDepartment($departmentid)
    {
        $this->db->Query('DELETE FROM {operatordepartment} WHERE departmentid = ?', array($departmentid));
    }

    public function deleteByOperatorId($operatorid)
    {
        $this->db->Query('DELETE FROM {operatordepartment} WHERE operatorid = ?', array($operatorid));
    }

    public function hasDepartments($operatorid)
    {
        $query = 'SELECT COUNT(*) as cnt FROM {'.$this->getTableName().'} WHERE operatorid=?';

        try {
            $this->db->Query($query, array($operatorid));
        } catch (Exception $e) {
            $total = 0;
        }

        $this->db->nextRecord();
        $row = $this->db->getRow();

        return $row['cnt'] > 0;
    }

    public function enumDepartmentsWithOperator($operatorid, $locale)
    {
        $sql = 'SELECT *, 
      EXISTS(SELECT * 
        FROM {operatordepartment} od 
        WHERE od.operatorid=:operatorid  
        AND od.departmentid=d.departmentid) AS isindepartment 
      FROM {department} d 
      INNER JOIN {departmentlocale} dl 
      ON d.departmentid=dl.departmentid 
      WHERE locale=:locale';
        try {
            $this->db->Query($sql, array('locale' => $locale, 'operatorid' => $operatorid));
        } catch (Exception $e) {
            return array();
        }

        return $this->db->getArrayOfRows();
    }

//  function enumDepartmentsWithOnllineStatus($locale) {
//    $sql = "SELECT *, 
//      EXISTS(SELECT * FROM {operatordepartment} od 
//        INNER JOIN {operatorlastaccess} la ON od.operatorid=la.operatorid 
//        WHERE la.status = :status 
//        AND od.departmentid=d.departmentid
//        AND (unix_timestamp(CURRENT_TIMESTAMP) - unix_timestamp(la.lastvisited)) < :delta) as isonline 
//      FROM {department} d 
//      INNER JOIN {departmentlocale} dl ON d.departmentid=dl.departmentid 
//      WHERE locale=:locale";
//    try {
//      $this->db->Query($sql, array('status' => OPERATOR_STATUS_ONLINE, 
//        'delta' => ONLINE_TIMEOUT, 
//        'locale' => $locale));
//      return  $this->db->getArrayOfRows();
//    } catch (Exception $e) {

//      return array();
//  
//    }
//  }

  public function enumOnlineDepartments($locale)
  {
      $sql = 'SELECT *
      FROM {department} d 
      INNER JOIN {operatordepartment} od 
      ON od.departmentid = d.departmentid
      INNER JOIN {operatorlastaccess} la 
      ON od.operatorid=la.operatorid 
      INNER JOIN {departmentlocale} dl 
      ON d.departmentid=dl.departmentid 
      WHERE la.status = :status 
      AND od.departmentid=d.departmentid
      AND unix_timestamp(CURRENT_TIMESTAMP) - unix_timestamp(la.lastvisited) < :delta
      AND locale=:locale';
      try {
          $this->db->Query($sql, array('status' => OPERATOR_STATUS_ONLINE,
        'delta' => ONLINE_TIMEOUT,
        'locale' => $locale, ));

          return  $this->db->getArrayOfRows();
      } catch (Exception $e) {
          return array();
      }
  }

    public function enumAvailableDepartmentsForOperator($operatorId, $locale, $shouldCheckDepartments)
    {
        if ($shouldCheckDepartments) {
            $departmentsSql = ' AND EXISTS (SELECT * FROM {'.$this->getTableName().'} od WHERE od.operatorid=:operatorid AND od.departmentid=d.departmentid)';
        } else {
            $departmentsSql = '';
        }
        $sql = 'SELECT * FROM {department} d INNER JOIN {departmentlocale} dl 
          ON d.departmentid=dl.departmentid WHERE locale=:locale '.$departmentsSql;
        try {
            $this->db->Query($sql, array('locale' => $locale, 'operatorid' => $operatorId));

            return $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return array();
        }
    }

    public function isOperatorInDepartment($operatorid, $departmentid)
    {
        try {
            $this->db->Query('SELECT * FROM '.$this->getTableName().' WHERE operatorid=:operatorid AND departmentid=:departmentid ', array('operatorid' => $operatorid, 'departmentid' => $departmentid));

            return $this->db->getNumRows() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function enumDepartmentKeysByOperator($operatorid)
    {
        $query = '
  		SELECT 
  			d.departmentkey, d.departmentid 
  		FROM  
  			{operatordepartment} as od
  		INNER JOIN
  			{department} as d
  		ON
  			od.departmentid = d.departmentid
  		WHERE
  			od.operatorid = ?
  		';
        try {
            $this->db->Query($query, array($operatorid));

            return $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>