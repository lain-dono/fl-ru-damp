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
require_once '../classes/class.pagination.php';
require_once '../classes/class.smartyclass.php';

$operator = Operator::getInstance()->GetLoggedOperator();

$TML = new SmartyClass();

$action = $_REQUEST['action'];
$TML->assign('action', $action);

$threadid = verify_param('thread', "/^\d{1,8}$/");
$token = verify_param('token', "/^\d{1,8}$/");

$TML->assign('threadid', $threadid);
$TML->assign('token', $token);

if ($action == 'operators') {
    $found = Operator::getInstance()->getOnlineOperatorsWithDepartments($operator['operatorid'], Resources::getCurrentLocale());
    $TML->assign('operators', $found);

//  $out = setupPage($found, $action, 'operatorid', 'fullname');
//  $TML->assign('out', $out);
} elseif ($action == 'visitor_redirected') {
    $TML->Assign('link', WEBIM_ROOT.'/operator/agent.php?thread='.$threadid.'&token='.$token.'&level=ajaxed&viewonly=true');
} elseif ($action == 'chat_closed') {
    $TML->Assign('link', WEBIM_ROOT.'/operator/agent.php?thread='.$threadid.'&token='.$token.'&level=ajaxed&viewonly=true&history=true');
}

$TML->display('popup.tpl');

function setupPage($list, $action, $idfield, $valuefield)
{
    global $token, $threadid, $TML;
    $pagination = setup_pagination($list);
    if (!empty($pagination)) {
        $page = array();
        $page['pagination'] = $pagination['pagination'];
        $page['pagination_items'] = $pagination['pagination_items'];
        $page['params'] = array('thread' => $threadid, 'token' => $token);
        $TML->assign('pagination', generate_pagination($page['pagination']));
    }

    $out = array();
    if (!empty($page['pagination_items'])) {
        foreach ($page['pagination_items'] as $v) {
            $page['params']['nextoperatorid'] = $v[$idfield];
            $params = array(
        'servlet_root' => WEBIM_ROOT,
        'servlet' => '/operator/redirect.php',
        'path_vars' => $page['params'],
      );

            $href = generate_get($params);
            $value = $v[$valuefield];

            $out[] = '<li><a href="'.$href.'" title="'.$value.'">'.$value.'</a></li>';
        }
    }

    return $out;
}
?>