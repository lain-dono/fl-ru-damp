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

class OperatorOnlineMapper extends BaseMapper
{
    public function __construct(DBDriver $db, $model_name)
    {
        parent::__construct($db, $model_name, array('updated'), false, 'id');
    }

    public function enumAllAccessedOperatorsWithoutLastAccess()
    { // TODO make the same for Oracle
        $sql = 'SELECT DISTINCT OO.operatorid FROM {operatoronline} OO WHERE '
                .' NOT EXISTS (SELECT * FROM {operatorlastaccess} LA WHERE LA.operatorid=OO.operatorid) ORDER BY OO.operatorid';
        try {
            $this->db->Query($sql);

            return $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return array();
        }
    }

    public function updateOperatorOnlineTime($operatorid, $threadid = -1)
    {
        $sql = '
  			INSERT INTO {operatoronline}
  				(date, operatorid, threadid, seconds)
  				VALUES (:date, :operatorid, :threadid, :seconds) 
  			ON DUPLICATE KEY UPDATE 
  				seconds = IF(
  						UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(updated) < :timeout, 
  						seconds + UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(updated), 
  						seconds + 1
  						)';
        try {
            $this->db->Query($sql, array(
                'date' => date('Y-m-d'),
                'operatorid' => $operatorid,
                'seconds' => 0,
                'timeout' => TIMEOUT_OPERATOR_PING,
                'threadid' => $threadid,
            ));
        } catch (Exception $e) {
        }
    }

    public function pushOnlineStatsForOperator($operatorid, $stats)
    {
        $min_date = 0;
        foreach ($stats as $d) {
            foreach (array_keys($d) as $date) {
                $cur_time = strtotime($date);
                if ($min_date == 0) {
                    $min_date = $cur_time;
                }

                $min_date = min($cur_time, $min_date);
            }
        }

        $min_date = date('Y-m-d', $min_date);

        $results = $this->makeSearch('operatorid = :operatorid AND date >= :min_date', array('operatorid' => $operatorid, 'min_date' => $min_date));
        $db_stats = array();
        foreach ($results as $r) {
            if (!isset($db_stats[$r['threadid']])) {
                $db_stats[$r['threadid']] = array();
            }

            $db_stats[$r['threadid']][$r['date']] = $r;
        }

        foreach ($stats as $id => $d) {
            foreach ($d as $date => $v) {
                if (isset($db_stats[$id], $db_stats[$id][$date])) {
                    $data = $db_stats[$id][$date];
                    $data['seconds'] += $v['seconds'];
                    $data['updated'] = date('Y-m-d H:i:s', $v['updated']);
                    $this->update($data);
                } else {
                    $data = $v;
                    $data['operatorid'] = $operatorid;
                    $data['updated'] = date('Y-m-d H:i:s', $data['updated']);
                    $this->add($data);
                }
            }
        }
    }

    /**
     * Р’РѕР·РІСЂР°С‰Р°РµС‚ РґР°РЅРЅС‹Рµ РїРѕ РѕРїРµСЂР°С‚РѕСЂСѓ.
     * 
     * РђР»СЊС‚РµСЂРЅР°С‚РёРІР° РїР°РїРєРµ online_stats
     * 
     * @param int $operatorid ID РѕРїРµСЂР°С‚РѕСЂР°
     *
     * @return string
     */
    public function getOperatorMemStats($operatorid)
    {
        $sQuery = 'SELECT stats FROM {operatormemstats} WHERE operator_id = :operator_id';

        try {
            $this->db->Query($sQuery, array('operator_id' => $operatorid));
            $this->db->nextRecord();

            $stats = $this->db->getRow('stats');

            return $stats;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Р’РѕР·РІСЂР°С‰Р°РµС‚ РґР°РЅРЅС‹Рµ РїРѕ РІСЃРµРј РѕРїРµСЂР°С‚РѕСЂР°Рј.
     * 
     * РђР»СЊС‚РµСЂРЅР°С‚РёРІР° РїР°РїРєРµ online_stats
     * 
     * @return array
     */
    public function getAllOperatorsMemStats()
    {
        try {
            $this->db->Query('SELECT operator_id, stats FROM {operatormemstats}');

            return $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * РЎРѕС…СЂР°РЅСЏРµС‚ РґР°РЅРЅС‹Рµ РїРѕ РѕРїРµСЂР°С‚РѕСЂСѓ.
     * 
     * РђР»СЊС‚РµСЂРЅР°С‚РёРІР° РїР°РїРєРµ online_stats
     * 
     * @param int    $operatorid ID РѕРїРµСЂР°С‚РѕСЂР°
     * @param string $stats      РґР°РЅРЅС‹Рµ
     *
     * @return bool true - СѓСЃРїРµС…, false - РїСЂРѕРІР°Р»
     */
    public function setOperatorMemStats($operatorid, $stats)
    {
        $sQuery = 'INSERT INTO {operatormemstats} (operator_id, stats) VALUES (:operator_id, :stats) 
            ON DUPLICATE KEY UPDATE stats = :stats';

        try {
            $this->db->Query($sQuery, array('operator_id' => $operatorid, 'stats' => $stats));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * РЈРґР°Р»СЏРµС‚ РґР°РЅРЅС‹Рµ РїРѕ РѕРїРµСЂР°С‚РѕСЂСѓ.
     * 
     * @param int $operatorid ID РѕРїРµСЂР°С‚РѕСЂР°
     *
     * @return bool true - СѓСЃРїРµС…, false - РїСЂРѕРІР°Р»
     */
    public function delOperatorMemStats($operatorid)
    {
        $sQuery = 'DELETE FROM {operatormemstats} WHERE operator_id = :operator_id';

        try {
            $this->db->Query($sQuery, array('operator_id' => $operatorid));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * РћС‡РёС‰Р°РµС‚ РґР°РЅРЅС‹Рµ РїРѕ РІСЃРµРј РѕРїРµСЂР°С‚РѕСЂР°Рј.
     * 
     * @return bool true - СѓСЃРїРµС…, false - РїСЂРѕРІР°Р»
     */
    public function truncateOperatorMemStats()
    {
        try {
            $this->db->Query('TRUNCATE {operatormemstats}');

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>