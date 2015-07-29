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
$TITLE_KEY = 'active.visits.queue';

require_once dirname(__FILE__).'/inc/admin_prolog.php';

require_once '../classes/functions.php';
require_once '../classes/class.thread.php';
require_once '../classes/class.smartyclass.php';

$TML = new SmartyClass($TITLE_KEY);

$o = Operator::getInstance();
$operator = $o->GetLoggedOperator();

if ($o->isOperatorsLimitExceeded()) {
    $TML->display('operators_limit.tpl');
    require_once dirname(__FILE__).'/inc/admin_epilog.php';
    die();
}

$TML->assign('visit_details', get_app_location(true, false).'/operator/visit.php?pageid=');

$TML->display('../templates/active_visitors.tpl');

require_once dirname(__FILE__).'/inc/admin_epilog.php';
?>