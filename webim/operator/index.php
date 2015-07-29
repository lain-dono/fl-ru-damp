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
$TITLE_KEY = 'topMenu.admin';

require_once dirname(__FILE__).'/inc/admin_prolog.php';

require_once '../classes/common.php';
require_once '../classes/functions.php';
require_once '../classes/class.adminurl.php';
require_once '../classes/class.operator.php';
require_once '../classes/class.settings.php';
require_once '../classes/class.smartyclass.php';

$operator = Operator::getInstance()->GetLoggedOperator();

$TML = new SmartyClass($TITLE_KEY);

$count = 0;
foreach (AdminURL::$ADMIN_MENU as $i) {
    if ($i['role'] == 'operator' || $i['role'] == $operator['role']) {
        $prepared[$count] = $i;
        $prepared[$count]['link'] = AdminURL::getInstance()->getURL($i['link_name']);
        ++$count;
    }
}
if (sizeof($prepared) % 2 == 1) {
    $prepared[] = array();
}
$TML->assign('items', $prepared);
$TML->display('menu.tpl');

require_once dirname(__FILE__).'/inc/admin_epilog.php';
?>