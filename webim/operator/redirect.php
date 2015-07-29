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
require_once '../classes/class.department.php';
require_once '../classes/class.smartyclass.php';

$TML = new SmartyClass();

$operator = Operator::getInstance()->GetLoggedOperator();

$threadid = verify_param('thread', "/^\d{1,8}$/");
$token = verify_param('token', "/^\d{1,8}$/");

$thread = Thread::getInstance()->GetThreadById($threadid);
$visitSession = VisitSession::GetInstance()->GetVisitSessionById($thread['visitsessionid']);
$TML->assign('visit_session', $visitSession);

if (!$thread || !isset($thread['token']) || $token != $thread['token']) {
    die('wrong thread');
}

$nextid = verify_param('nextoperatorid', "/^\d{1,8}$/");
$nextdepartmentid = verify_param('nextdepartmentid', "/^\d{1,8}$/");

$page = array();

if (!empty($nextid)) {
    $nextOperator = Operator::getInstance()->GetOperatorById($nextid);
    $TML->assign('nextoperator', $nextOperator);
}

if (!empty($nextdepartmentid)) {
    $nextdepartment = Department::getInstance()->getById($nextdepartmentid, Resources::getCurrentLocale());
    $TML->assign('nextdepartment', $nextdepartment);
}

$errors = array();

ThreadProcessor::getInstance()->ProcessThread($threadid, 'redirect', array('nextoperatorid' => $nextid, 'nextdepartmentid' => $nextdepartmentid, 'operator' => Operator::getInstance()->GetLoggedOperator()));

$TML->assign('page_settings', $page);

if (count($errors) > 0) {
    $TML->assign('errors', $errors);
    $TML->display('chat_error.tpl');
} else {
    $TML->display('redirected.tpl');
}

?>