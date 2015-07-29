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

class VisitedPageMapper extends BaseMapper
{
    public function __construct(DBDriver $db, $model_name)
    {
        parent::__construct($db, $model_name, array('opened', 'updated'));
    }

    public function getVisitTimeByVisitSessionId($visitsessionid)
    {
        return $this->makeSearch('visitsessionid=?',
        $visitsessionid,
        'max(unix_timestamp(updated)) as timeend,
         min(unix_timestamp(opened)) as timestart,
         unix_timestamp(CURRENT_TIMESTAMP) as curtime,
         max(unix_timestamp(updated)) - min(unix_timestamp(opened)) as diff'
      );
    }

    public function enumByVisitSessionId($visitsessionid)
    {
        return $this->makeSearch('visitsessionid=?',
        $visitsessionid,
        'uri,
         referrer,
         unix_timestamp(updated) as updatedtime,
         unix_timestamp(opened) as openedtime,
         (unix_timestamp(updated) - unix_timestamp(opened)) as sessionduration
        '
      );
    }

    public function getFirstBySessionId($visitsessionid)
    {
        $sql = '
  	  			SELECT
                    *,
                    unix_timestamp(opened) as tsopened,
                    unix_timestamp(updated) as tsupdated,
                    unix_timestamp(CURRENT_TIMESTAMP) as current
                FROM {visitedpage}
                WHERE
                    visitsessionid = ?
                HAVING 
                    opened = min(opened)
  	  ';

        try {
            $this->db->Query($sql, $visitsessionid);
            $this->db->nextRecord();

            return $this->db->getRow();
        } catch (Exception $e) {
            return;
        }
    }
}
?>