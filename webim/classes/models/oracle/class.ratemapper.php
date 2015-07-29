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

class RateMapper extends BaseMapper
{
    public function __construct(DBDriver $db, $model_name)
    {
        parent::__construct($db, $model_name, array('date', 'deldate'));
    }

    public function getByThreadidWithOperator($threadid)
    {
        $query = '
    			SELECT r.*, o."fullname" "operator", '.$this->getDateColumnsSql('r').' 
                FROM "{rate}" r INNER JOIN "{operator}" o 
    			ON r."operatorid" = o."operatorid" 
          		WHERE r."threadid" = :threadid AND r."deldate" IS NULL
    			ORDER BY r."date"
    		';

        try {
            $this->db->Query($query, array('threadid' => $threadid));

            return  $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return array();
        }
    }

    public function getByThreadid($threadid)
    {
        return $this->makeSearch('"threadid" = :id', array('id' => $threadid), null, null, null, null, array('"date"', 'ASC'));
    }

    public function getByThreadidAndOperatorid($threadid, $operatorid)
    {
        $r = $this->makeSearch(
            '"threadid" = :threadid AND "operatorid" = :operatorid',
            array('threadid' => $threadid, 'operatorid' => $operatorid)
    );

        return array_shift($r);
    }

    public function removeRate($rateid)
    {
        $sql = '
    	UPDATE "{rate}" SET "deldate"=SYSDATE WHERE "rateid"=:rateid
    ';

        try {
            $this->db->Query($sql, array('rateid' => $rateid));

            return  $this->db->getArrayOfRows() > 0;
        } catch (Exception $e) {
            return array();
        }
    }
}

?>
