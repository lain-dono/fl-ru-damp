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
$TITLE_KEY = 'page_analysis.search.title';

require_once dirname(__FILE__).'/inc/admin_prolog.php';

require_once '../classes/functions.php';
require_once '../classes/class.thread.php';
require_once '../classes/class.operator.php';
require_once '../classes/class.smartyclass.php';
require_once '../classes/class.pagination.php';

$TML = new SmartyClass($TITLE_KEY);
$tmlPage = null;

$operator = Operator::getInstance()->GetLoggedOperator();
$items_per_page = verify_param('items', "/^\d{1,3}$/", DEFAULT_ITEMS_PER_PAGE);
$show_empty = isset($_REQUEST['show_empty']) && $_REQUEST['show_empty'] == 1 ? true : false;

if (isset($_REQUEST['q'])) {
    $nTotal = Thread::getInstance()->GetListThreadsCount($operator['operatorid'], $_REQUEST['q'], $show_empty);

    if ($nTotal) {
        $pagination = setup_pagination_cnt($nTotal, $items_per_page);
        $nLimit = $pagination['items'];
        $nOffset = $pagination['start'];

        $res = Thread::getInstance()->GetListThreads($operator['operatorid'], $_REQUEST['q'], $show_empty, $nLimit, $nOffset);

        $tmlPage['pagination'] = $pagination;
        $tmlPage['pagination_items'] = $res;
    }

    if (!empty($tmlPage['pagination_items'])) {
        $TML->assign('pagination', generate_pagination($tmlPage['pagination']));
    }

    $tmlPage['formq'] = $_GET['q'];
    $tmlPage['show_empty'] = $show_empty;
}

$TML->assign('advanced', false);
$TML->assign('page_settings', $tmlPage);
$TML->display('thread_search.tpl');

require_once dirname(__FILE__).'/inc/admin_epilog.php';

?>