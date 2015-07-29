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


require_once 'classes/common.php';
require_once 'classes/functions.php';
require_once 'classes/class.thread.php';
require_once 'classes/class.operator.php';
require_once 'classes/class.browser.php';
require_once 'classes/class.button.php';
require_once 'classes/class.mailstatistics.php';

  set_error_handler('button_output_anyway', E_ALL & ~E_NOTICE & ~E_WARNING);
  $OLD_DISPLAY_ERRORS = ini_get('display_errors');
  ini_set('display_errors', 0);

$button_sent = false;

$lang = verify_param(isset($_GET['language']) ? 'language' : 'lang', "/^[\w-]{2,5}$/", '');

$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$image = verify_param('bim', "/^[\w\.]+$/");

if (empty($image)) {
    $image = verify_param('image', "/^[\w\.]+$/", 'webim');
}

if (empty($image)) {
    $image = 'webim';
}

$departmentKey = verify_param('departmentkey', "/^\w+$/");

@MailStatistics::sendStatsIfNeeded(MAIL_STATISTICS_FILE, MAIL_STATISTICS_HOUR);

$button_sent = Button::sendButton($image, $departmentKey, $lang, $_SERVER['DOCUMENT_ROOT'].WEBIM_ROOT);

  restore_error_handler();
  ini_set('display_errors', $OLD_DISPLAY_ERRORS);

exit;

function button_output_anyway($errno, $errstr, $errfile, $errline, $errcontext)
{
    global $button_sent, $image, $lang, $departmentKey;

    if ($button_sent) {
        return;
    }
    Button::sendButton($image, $departmentKey, $lang, $_SERVER['DOCUMENT_ROOT'].WEBIM_ROOT, 'off');
    die(1);
}
?>