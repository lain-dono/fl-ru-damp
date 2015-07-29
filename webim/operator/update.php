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
require_once '../classes/class.operator.php';
require_once '../classes/class.thread.php';
require_once '../classes/class.threadprocessor.php';
require_once '../classes/class.eventcontroller.php';
require_once '../classes/events_register.php';

ThreadProcessor::getInstance()->ProcessOpenThreads();

$o = Operator::getInstance();
$operator = $o->GetLoggedOperator(false);

$f = 'i'.'s'.'Op'.'er'.'a'.'to'.'rsL'.'im'.'it'.'E'.'x'.'ce'.'ed'.'ed';
if ($o->$f()) {
    die();
}

$status = verify_param('status', "/^\d{1,9}$/", OPERATOR_STATUS_ONLINE);

EventController::getInstance()->dispatchEvent(
    EventController::EVENT_OPERATOR_STATUS,
    array(
        $operator, /*, 
        $status, 
        Operator::getInstance()->getLoggedOperatorDepartmentsKeys(), 
        Operator::getInstance()->getLoggedOperatorLocales()*/
    )
);

if ($status != 0) {
    $since = verify_param('since', "/^\d{1,9}$/", 0);
    $xml = Thread::getInstance()->buildPendingThreadsXml($since, $operator);
    Browser::SendXmlHeaders();
    echo $xml;
}

exit;
?>