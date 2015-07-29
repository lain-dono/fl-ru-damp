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

class VisitSessionMapper extends BaseMapper
{
    public function __construct(DBDriver $db, $model_name)
    {
        parent::__construct($db, $model_name, array('created', 'updated'));
    }

    public function getAliveVisitors()
    {
        return $this->getVisitors();
    }

    public function getDeadVisitors()
    {
        return $this->getVisitors(true);
    }

    private function getVisitors($dead = false)
    {
        $min_delta = VISITED_PAGE_TIMEOUT;
        $max_delta = VISITED_PAGE_TIMEOUT * 3;

        $sql = '
                SELECT
                    s."ip",
                    s."useragent",
                    s."visitorid",
                    s."visitorname",
                    s."visitsessionid",
                    s."bitrixsessionid",
                    p."visitedpageid",
                    p."uri",
                    p."state",
                    p."referrer",
                    WM_UNIX_TIMESTAMP(p."opened") as "opened",
                    WM_UNIX_TIMESTAMP(p."updated") as "updated"                    
                FROM 
                    "{visitsession}" s, 
                    "{visitedpage}" p
                WHERE 
                    s."visitsessionid" = p."visitsessionid"
                    AND';
        if (!$dead) {
            $sql .= '   (
                      p."state" = :state
                      AND 
                      p."updated" >= FROM_UNIXTIME(WM_UNIX_TIMESTAMP(SYSDATE) - :maxdelta)
                    )
                ';
        } else {
            $sql .= '   (
                      (
                         p."state" <> :state
                         AND 
                         p."updated" >= FROM_UNIXTIME(WM_UNIX_TIMESTAMP(SYSDATE) - :mindelta)
                      )
                      OR 
                      (
                         p."updated" < FROM_UNIXTIME(WM_UNIX_TIMESTAMP(SYSDATE) - :mindelta)
                         AND 
                         p."updated" > FROM_UNIXTIME(WM_UNIX_TIMESTAMP(SYSDATE) - :maxdelta)
                      )
                    )';
        }
        $sql .= ' ORDER BY "opened" ASC';
        try {
            $this->db->Query($sql, array('state' => VISITED_PAGE_OPENED, 'mindelta' => $min_delta, 'maxdelta' => $max_delta));

            return $this->db->getArrayOfRows();
        } catch (Exception $e) {
            return array();
        }
    }

    public function getByVisitedPageId($visitedpageid)
    {
        $sql = '
      			SELECT s.*, " . $this->getDateColumnsSql("s") . "
      			FROM "{visitsession}" s
          		LEFT JOIN "{visitedpage}" p
      			ON s."visitsessionid" = p."visitsessionid"
      			WHERE p."visitedpageid"=:visitedpageid
      		';

        try {
            $this->db->Query($sql, array('visitedpageid' => $visitedpageid));
            $this->db->nextRecord();

            return $this->db->getRow();
        } catch (Exception $e) {
            return;
        }
    }

    public function getActiveSessionForVisitor($visitorid)
    {
        $result = array_shift($r = $this->makeSearch(
            '"visitorid" = :visitorid
      		AND(WM_UNIX_TIMESTAMP(SYSDATE)-WM_UNIX_TIMESTAMP("updated")) < :timeout',
            array('visitorid' => $visitorid, 'timeout' => VISIT_SESSION_TIMEOUT),
            null,
            1
        )
      );

        return $result ? $result['visitsessionid'] : null;
    }

    public function getByVisitorId($visitorid)
    {
        return array_shift($r = $this->makeSearch(
              '"visitorid" = :visitorid',
            array('visitorid' => $visitorid),
            null,
            1,
            null,
            null,
            array('updated', 'DESC')
         )
      );
    }

    public function cleanupVisitLogs()
    {
        $keepingDataPeriod = 2 * 24 * 60 * 60; // seconds 

      $sql = 'DELETE  
                {visitedpage}
              FROM
                {visitsession}, {visitedpage}
              WHERE 
                ({visitsession}.visitsessionid = {visitedpage}.visitsessionid)
                AND {visitsession}.hasthread = 0
                AND {visitsession}.updated < FROM_UNIXTIME(WM_UNIX_TIMESTAMP(SYSDATE) - '.$keepingDataPeriod.')';

        try {
            $this->db->Query($sql);
        } catch (Exception $e) {
        }

        $sql = 'DELETE
                {visitsession}
              FROM
                {visitsession}, {thread}
              WHERE 
                {visitsession}.hasthread = 0
                AND {visitsession}.updated < FROM_UNIXTIME(WM_UNIX_TIMESTAMP(SYSDATE) - '.$keepingDataPeriod.')';

        try {
            $this->db->Query($sql);
        } catch (Exception $e) {
        }
    }
}
?>