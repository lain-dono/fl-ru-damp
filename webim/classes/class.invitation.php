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

class Invitation
{
    private static $instance = null;

    public static function GetInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function CreateInvitation($threadid)
    {
        return MapperFactory::getMapper('Invitation')->save(
      array(
          'state' => INVITATION_CAN_BE_SENT,
          'threadid' => $threadid,
      )
    );
    }

    public function UpdateInvitationMessage($threadid, $inviteMessageId)
    {
        MapperFactory::getMapper('Invitation')->updateInvitationMessageByThreadId($threadid, $inviteMessageId);
    }

    public function GetInvitationByVisitedPageId($visitedpageid)
    {
        $visitedpage = VisitedPage::GetInstance()->GetVisitedPageById($visitedpageid);
        if (!is_array($visitedpage) || !isset($visitedpage['invitationid']) || empty($visitedpage['invitationid'])) {
            return;
        }

        $invitation = MapperFactory::getMapper('Invitation')->getById($visitedpage['invitationid']);

        return $invitation;
    }

    public function GetInvitationState($visitedpageid)
    {
        $visitedpage = VisitedPage::GetInstance()->GetVisitedPageById($visitedpageid);

        $state = INVITATION_UNINITIALIZED;
        if (!empty($visitedpage['invitationid'])) {
            $invitation = MapperFactory::getMapper('Invitation')->getById($visitedpage['invitationid']);
            $state = $invitation['state'];
        }

        return $state;
    }

    public function GetInvitationById($invitationId)
    {
        return MapperFactory::getMapper('Invitation')->getById($invitationId);
    }

    public function GetInvitationMessageById($messageId)
    {
        return MapperFactory::getMapper('Message')->getById($messageId);
    }
}
?>
