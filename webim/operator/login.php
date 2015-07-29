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
$TITLE_KEY = 'page_login.title';

require_once '../classes/functions.php';
require_once '../classes/common.php';
require_once '../classes/class.smartyclass.php';
require_once '../classes/class.operator.php';
require_once '../classes/class.browser.php';

$TML = new SmartyClass($TITLE_KEY);

$errors = array();

if (isset($_REQUEST['login']) && isset($_REQUEST['password'])) {
    $login = get_mandatory_param('login');
    $password = get_mandatory_param('password');
    $remember = isset($_REQUEST['isRemember']) && $_REQUEST['isRemember'] == 'on';
    $e = Operator::getInstance()->DoLogin($login, $password, $remember);

    if (isset($e)) {
        $errors[] = $e;
    }

    if (empty($errors)) {
        if (!empty($_REQUEST['redir'])) {
            header('Location: '.$_REQUEST['redir']);
        } else {
            header('Location: '.WEBIM_ROOT.'/');
        }
        exit;
    }
}

$TML->assign('errors', $errors);
$TML->assign('isRemember', true);

if (!empty($_REQUEST['redir'])) {
    $TML->assign('redir', htmlspecialchars($_REQUEST['redir']));
}

$status = verify_param('status', '/^(new)$/', '');
if ($status == 'new') {
    $introduction = 'true';
    $TML->assign('introduction', $introduction);
}

$TML->display('../templates/login.tpl');

?>