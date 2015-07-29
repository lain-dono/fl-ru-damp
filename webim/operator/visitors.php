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
$TITLE_KEY = 'topMenu.visitors';

require_once dirname(__FILE__).'/inc/admin_prolog.php';

require_once '../classes/functions.php';
require_once '../classes/class.operator.php';
require_once '../classes/class.smartyclass.php';

$TML = new SmartyClass($TITLE_KEY);

$o = Operator::getInstance();

$operator = $o->GetLoggedOperator();

if ($o->isOperatorsLimitExceeded()) {
    $TML->display('operators_limit.tpl');
    require_once dirname(__FILE__).'/inc/admin_epilog.php';
    die();
}

$o->UpdateOperatorStatus(
    $operator/*, 
    OPERATOR_STATUS_ONLINE, 
    $o->getLoggedOperatorDepartmentsKeys(), 
    $o->getLoggedOperatorLocales()*/
);

$lang = verify_param('lang', "/^[\w-]{2,5}$/", '');
if (!empty($lang)) {
    $TML->assign('lang_param', "?lang=$lang");
    $TML->assign('lang_and_is_operator_param', "?isoperator=true&lang=$lang");
} else {
    $TML->assign('lang_and_is_operator_param', '?isoperator=true');
}

$TML->display('pending_visitors.tpl');

require_once dirname(__FILE__).'/inc/admin_epilog.php';
?>