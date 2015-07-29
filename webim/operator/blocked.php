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
$TITLE_KEY = 'menu.blocked';

require_once dirname(__FILE__).'/inc/admin_prolog_before.php';

require_once '../classes/functions.php';
require_once '../classes/class.operator.php';
require_once '../classes/class.pagination.php';
require_once '../classes/class.smartyclass.php';
require_once '../classes/models/generic/class.mapperfactory.php';

$operator = Operator::getInstance()->GetLoggedOperator();

$errors = array();

$banMapper = MapperFactory::getMapper('Ban');

if (!empty($_GET['act']) && $_GET['act'] == 'delete') {
    $banId = isset($_GET['id']) ? $_GET['id'] : '';
    if (!preg_match("/^\d+$/", $banId)) {
        $errors[] = 'Wrong argument';
    }
    if (count($errors) == 0) {
        $banMapper->delete($banId);

        header('Location: '.WEBIM_ROOT.'/operator/blocked.php');
        exit;
    }
}

$blockedList = $banMapper->getAll();
foreach ($blockedList as $k => $v) {
    $blockedList[$k]['till'] = date(getDateTimeFormat(), $v['till']);
}

require_once dirname(__FILE__).'/inc/admin_prolog_after.php';
$TML = new SmartyClass($TITLE_KEY);

if (!empty($blockedList)) {
    $pagination = setup_pagination($blockedList);
    $tmlPage['pagination'] = $pagination['pagination'];
    $tmlPage['pagination_items'] = $pagination['pagination_items'];
    $TML->assign('pagination', generate_pagination($tmlPage['pagination']));
    $TML->assign('page_settings', $tmlPage);
}

$TML->assign('errors', $errors);
$TML->display('blocked_visitors.tpl');

require_once dirname(__FILE__).'/inc/admin_epilog.php';
?>