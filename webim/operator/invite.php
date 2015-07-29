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


require_once '../classes/functions.php';
require_once '../classes/class.thread.php';
require_once '../classes/class.operator.php';
require_once '../classes/class.visitsession.php';
require_once '../classes/class.visitedpage.php';
require_once '../classes/class.invitation.php';

$operator = Operator::getInstance()->GetLoggedOperator();

$pageId = verify_param('pageid', '/^([a-z0-9]{32})?$/', '');

if (empty($pageId)) {
    die('invalid or absent pageid');
}

$visitSession = VisitSession::GetInstance()->GetVisitSessionByPageId($pageId);
$remoteLevel = Browser::GetRemoteLevel($visitSession['useragent']);

$thread = VisitedPage::GetInstance()->GetInvitationThread($pageId);

if (empty($thread) || $thread['state'] == STATE_CLOSED) {
    $thread = Thread::getInstance()->CreateThread(WEBIM_CURRENT_LOCALE, STATE_INVITE,
    array('operatorfullname' => $operator['fullname'],
          'operatorid' => $operator['operatorid'],
          'visitsessionid' => $visitSession['visitsessionid'], ));
    VisitSession::GetInstance()->UpdateVisitSession($visitSession['visitsessionid'], array('hasthread' => 1));

    $introMessage = Resources::Get('invite.intro.message', array($visitSession['visitorname']), WEBIM_CURRENT_LOCALE);
    Thread::getInstance()->PostMessage($thread['threadid'], KIND_FOR_AGENT, $introMessage);
    $invitationId = Invitation::getInstance()->CreateInvitation($thread['threadid']);

    VisitedPage::GetInstance()->UpdateVisitedPage($pageId, array('invitationid' => $invitationId));
}

header('Location: '.WEBIM_ROOT.'/operator/agent.php?thread='.$thread['threadid'].
       '&token='.$thread['token'].'&level='.$remoteLevel.'&force=false');
exit;
?>