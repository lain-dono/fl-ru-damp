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

class InvitationMapper extends BaseMapper
{
    public function __construct(DBDriver $db, $model_name)
    {
        parent::__construct($db, $model_name, array());
    }

    public function updateInvitationMessageByThreadId($threadid, $invitemessageid)
    {
        $query = 'UPDATE "{invitation}" SET "invitemessageid"=:invitemessageid WHERE "threadid"=:threadid';
        try {
            $this->db->Query($query, array('invitemessageid' => $invitemessageid, 'threadid' => $threadid));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getByVisitedPageId($visitedpageid)
    {
        $sql = '
  	          SELECT i.*
              FROM "{invitation}" i
              LEFT JOIN "{visitedpage}" p
              ON 
                p."invitationid" = i."invitationid"
              WHERE
                p."visitedpageid" = :vistedpageid
              ';
        try {
            $this->db->Query($sql, array('visitedpageid' => $visitedpageid));
            $this->db->nextRecord();

            return $this->db->getRow();
        } catch (Exception $e) {
            return;
        }
    }
}
?>