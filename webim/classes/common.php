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
define('NO_CSRF', true);
define('IS_EXTERNAL', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/stdf.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/webim.php';

$mem_buff = new memBuff();

define('WEBIM_ROOT', '/webim');

define('PASSWORD_RECOVER_TIMEOUT', 24 * 60 * 60 * 1000);

define('SITE_DB_TABLE_PREFIX', 'chat');

define('WEBIM_CONNECTION_TIMEOUT', 60); // seconds

// РѕС‚РїСЂР°РІРєРµ РґРёР°Р»РѕРіРѕРІ РёР· РјРµСЃСЃРµРЅРґР¶РµСЂР° -------------
if ((defined('SERVER') && SERVER != 'release') || (defined('IS_LOCAL') && IS_LOCAL === true)) { // С‚РµСЃС‚РѕРІС‹Рµ
    $aDko = array(
        1 => array('option' => 'РћР±С‰РёРµ РІРѕРїСЂРѕСЃС‹', 'email' => 'helpdesk_beta_1@free-lance.ru', 'subject' => 'Р’РѕРїСЂРѕСЃ РїРѕ СЃРµСЂРІРёСЃР°Рј СЃР°Р№С‚Р°, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        2 => array('option' => 'РћС€РёР±РєРё', 'email' => 'helpdesk_beta_3@free-lance.ru', 'subject' => 'РћС€РёР±РєРё РЅР° СЃР°Р№С‚Рµ, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        3 => array('option' => 'Р¤РёРЅР°РЅСЃС‹', 'email' => 'helpdesk_beta_2@free-lance.ru', 'subject' => 'Р¤РёРЅР°РЅСЃРѕРІС‹Р№ РІРѕРїСЂРѕСЃ, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        4 => array('option' => 'РџРѕРґР±РѕСЂ С„СЂРёР»Р°РЅСЃРµСЂРѕРІ', 'email' => 'helpdesk_beta_4@free-lance.ru', 'subject' => 'РџРѕРґР±РѕСЂ С„СЂРёР»Р°РЅСЃРµСЂРѕРІ, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        5 => array('option' => 'В«Р‘РµР·РѕРїР°СЃРЅР°СЏ РЎРґРµР»РєР°В»', 'email' => 'helpdesk_beta_5@free-lance.ru', 'subject' => 'В«Р‘РµР·РѕРїР°СЃРЅР°СЏ РЎРґРµР»РєР°В»'),
        6 => array('option' => 'Р РµРєР»Р°РјР°', 'email' => 'helpdesk_beta_6@free-lance.ru', 'subject' => 'Р РµРєР»Р°РјР°, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        7 => array('option' => 'РљРѕРЅСЃР°Р»С‚РёРЅРі', 'email' => 'consulting@free-lance.ru', 'subject' => 'РљРѕРЅСЃР°Р»С‚РёРЅРі, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
    );
} else { // Р±РѕРµРІРѕР№
    $aDko = array(
        1 => array('option' => 'РћР±С‰РёРµ РІРѕРїСЂРѕСЃС‹', 'email' => 'info@free-lance.ru', 'subject' => 'Р’РѕРїСЂРѕСЃ РїРѕ СЃРµСЂРІРёСЃР°Рј СЃР°Р№С‚Р°, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        2 => array('option' => 'РћС€РёР±РєРё', 'email' => 'tester@free-lance.ru', 'subject' => 'РћС€РёР±РєРё РЅР° СЃР°Р№С‚Рµ, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        3 => array('option' => 'Р¤РёРЅР°РЅСЃС‹', 'email' => 'finance@free-lance.ru', 'subject' => 'Р¤РёРЅР°РЅСЃРѕРІС‹Р№ РІРѕРїСЂРѕСЃ, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        4 => array('option' => 'РџРѕРґР±РѕСЂ С„СЂРёР»Р°РЅСЃРµСЂРѕРІ', 'email' => 'manager@free-lance.ru', 'subject' => 'РџРѕРґР±РѕСЂ С„СЂРёР»Р°РЅСЃРµСЂРѕРІ, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        5 => array('option' => 'В«Р‘РµР·РѕРїР°СЃРЅР°СЏ РЎРґРµР»РєР°В»', 'email' => 'norisk@free-lance.ru', 'subject' => 'В«Р‘РµР·РѕРїР°СЃРЅР°СЏ РЎРґРµР»РєР°В»'),
        6 => array('option' => 'Р РµРєР»Р°РјР°', 'email' => 'adv@free-lance.ru', 'subject' => 'Р РµРєР»Р°РјР°, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
        7 => array('option' => 'РљРѕРЅСЃР°Р»С‚РёРЅРі', 'email' => 'consulting@free-lance.ru', 'subject' => 'РљРѕРЅСЃР°Р»С‚РёРЅРі, РѕР±СЂР°С‚РЅР°СЏ СЃРІСЏР·СЊ'),
    );
}

$aDkoOrder = array(1, 2, 3, 4, 5, 6, 7);
//-----------------------------------------------

define('STATE_QUEUE', 'queue');
define('STATE_REDIRECTED', 'redirected');
define('STATE_CHATTING', 'chatting');
define('STATE_CLOSED', 'closed');
define('STATE_LOADING', 'loading');
define('STATE_INVITE', 'invite');
define('STATE_CHAT_VISITOR_BROWSER_CLOSED_REFRESHED', 'chat_visitor_browser_closed_refreshed');
define('STATE_CHATTING_CLOSED_REFRESHED', 'chatting_closed_refreshed');
define('STATE_CHATTING_OPERATOR_BROWSER_CLOSED_REFRESHED', 'operator_browswer_closed_refreshed');
define('STATE_QUEUE_EXACT_OPERATOR', 'queue_exact_operator');
define('STATE_LOADING_FOR_EXACT_OPERATOR', 'loading_for_exact_operator');

define('KIND_USER',      1);
define('KIND_AGENT',     2);
define('KIND_FOR_AGENT', 3);
define('KIND_INFO',      4);
define('KIND_CONN',      5);
define('KIND_EVENTS',    6);
define('KIND_AVATAR',    7);
define('KIND_RATE',  8);
define('KIND_NOANSWER',  9);

define('VISITED_PAGE_TIMEOUT', 45);
define('TIMEOUT_VISITOR_PING', 60); // seconds
define('TIMEOUT_OPERATOR_PING', 60); // seconds
define('TIMEOUT_REFRESH', 60); // seconds
define('INVITE_ANIMATION_DURATION', 90); // seconds
define('TIMEOUT_EXACT_OPERATOR', 60 * 3);// seconds
define('TIMEOUT_OPERATOR_NOANSWER', 25);// seconds


define('INVITATION_UNINITIALIZED',  0);
define('INVITATION_CAN_BE_SENT',    1);
define('INVITATION_SENT',           2);
define('INVITATION_ACCEPTED',       3);
define('INVITATION_REJECTED',       4);
define('INVITATION_TIMEOUT',        5);
define('INVITATION_MISSED',         6);

define('VISITED_PAGE_LOADING',      0);
define('VISITED_PAGE_OPENED',       1);
define('VISITED_PAGE_CLOSED',       2);

define('ONE_IP_MAX_SESSIONS',        5);

define('PROCESS_THREADS_DELAY', 30); // seconds


define('WEBIM_COOKIE_VISITOR_NAME',       'WEBIM_VISITOR_NAME');   // 1.0.9+
define('WEBIM_COOKIE_VISITOR_ID',         'WEBIM_VISITOR_ID');
define('WEBIM_COOKIE_PARTNER_REF',        'WEBIM_PARTNER_REFERENCE');
define('WEBIM_COOKIE_VISITOR_IN_CHAT',       'WEBIM_VISITOR_IN_CHAT');
define('WEBIM_COOKIE_AUTOINVITE_STATS',   'WEBIM_STATS');
define('WEBIM_COOKIE_TOTAL_TIME_ON_SITE', 'WEBIM_TOTAL_TIME_ON_SITE');

define('OPERATOR_STATUS_OFFLINE', 0);
define('OPERATOR_STATUS_ONLINE',  1);

define('WEBIM_WHOIS_LINK', 'https://www.nic.ru/whois/?query=');

require_once 'config.php';
require_once 'version.php';

define('ONLINE_FILES_DIR', preg_replace('#/$#', '', $_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'webim'.DIRECTORY_SEPARATOR.'online');
define(
    'TRACKER_FILES_DIR',
    ONLINE_FILES_DIR.DIRECTORY_SEPARATOR.

    'tracker_pages'
);
define('VISITSESSION_FILE_TTL', 2 * 24 * 60 * 60);

define(
    'OPERATOR_ONLINE_FILES_DIR',
    ONLINE_FILES_DIR.DIRECTORY_SEPARATOR.

    'online_operators'
);

define('OPERATOR_ONLINE_FILE_EXT', 'online');

define(
    'OPERATOR_ONLINE_STATS_FILES_DIR',
    ONLINE_FILES_DIR.DIRECTORY_SEPARATOR.

    'online_stats'
);

define('OPERATOR_ONLINE_STATS_FILE_MAX_SIZE', 4098);

define('OPERATOR_ONLINE_STATS_FILE_EXT', 'time');

define(
    'HAS_THREADS_FILE',
    ONLINE_FILES_DIR.DIRECTORY_SEPARATOR.

    'has.threads');

define(
    'OPERATOR_VIEW_TRACKER_FILE',
    ONLINE_FILES_DIR.DIRECTORY_SEPARATOR.

    'tracker');
define('HAS_MESSAGES_OPERATOR_FILE_POSTFIX', '.operator.thread');
define('HAS_MESSAGES_VISITOR_FILE_POSTFIX', '.visitor.thread');

if (!defined('KEY')) {
    if (!strstr($_SERVER['REQUEST_URI'], 'install')) {
        header('Location: '.WEBIM_ROOT.'/install');
        exit();
    }
}

if (!defined('WEBIM_ENCODING') || WEBIM_ENCODING == 'CP1251') {
    define('SITE_DB_CHARSET', 'cp1251');
    define('SITE_DB_COLLATION', 'cp1251_general_ci');
    define('BROWSER_CHARSET', 'windows-1251');
    define('MAIL_ENCODING', 'CP1251');
} else {
    define('SITE_DB_CHARSET', 'utf8');
    define('SITE_DB_COLLATION', 'utf8_general_ci');
    define('BROWSER_CHARSET', 'UTF-8');
    define('MAIL_ENCODING', 'UTF-8');
}

//$invitation_encoding = "cp1251";
//define('INVITATION_ENCODING', 'cp1251');


define('DEFAULT_LOCALE', 'ru');

define('WEBIM_ORIGINAL_ENCODING', 'CP1251');

//$online_timeout = 30;
define('ONLINE_TIMEOUT', '30');

//  Maximum uploaded file size.
define('MAX_UPLOADED_FILE_SIZE', 100000);

define('DEFAULT_ITEMS_PER_PAGE', 15);

define('USE_X_FORWARDED_FOR', false);

define('MAIL_STATISTICS_FILE', $_SERVER['DOCUMENT_ROOT'].WEBIM_ROOT.'/online/ts.txt');
define('MAIL_STATISTICS_HOUR', 20);

function getAvailableLocalesForChat()
{
    $available_locales_for_chat = array(
    'ru',
    'en',
  );

    $locales = array();
    foreach ($available_locales_for_chat as $l) {
        $locales[] = array('localeid' => $l, 'localename' => $l);
    }

    return $locales;
}

$date_formats = array(
  'ru' => 'd.m.Y',
  'en' => 'm/d/Y',
);

$datetime_formats = array(
  'ru' => 'd.m.Y H:i',
  'en' => 'm/d/Y H:i',
);
function getDateFormat($lang = null)
{
    global $date_formats;
    if ($lang == null) {
        $lang = Resources::getCurrentLocale();
    }
    if (isset($date_formats[$lang])) {
        return $date_formats[$lang];
    }

    return;
}

function getDateTimeFormat($lang = null)
{
    global $datetime_formats;
    if ($lang == null) {
        $lang = Resources::getCurrentLocale();
    }

    if (isset($datetime_formats[$lang])) {
        return $datetime_formats[$lang];
    }

    return;
}

$webim_as_service = false;

if ($webim_as_service) {
    require_once dirname(__FILE__).'/service.php';
}

//  if (!isset($_SESSION['WEBIM_TRACELOG'])) {
//      $_SESSION['WEBIM_TRACELOG'] = '';
//  }
//  $_SESSION['WEBIM_TRACELOG'] .= $message;


//function PRINTSESSIONLOG() {
//  echo $_SESSION['WEBIM_TRACELOG'];
//}


require_once 'class.resources.php';

WebIMInit::Init();

class WebIMInit
{
    public static function Init()
    {
        Resources::SetLocaleLanguage();

        session_start();

//    if (isset($_SESSION['WEBIM_TRACELOG']) && strlen($_SESSION['WEBIM_TRACELOG']) > 200*200) {

//    }

    $locale = Resources::getCurrentLocale();
        define('WEBIM_CURRENT_LOCALE', $locale);

        self::initRequests();

        if (function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')) {
            @date_default_timezone_set(@date_default_timezone_get());
        }
    }

    private static function recursiveStripSlashes($value)
    {
        $result = $value;

        if (is_string($value)) {
            $result = stripslashes($value);
        }

        if (is_array($value)) {
            $result = array();
            foreach ($value as $i => $v) {
                $result[$i] = self::recursiveStripSlashes($v);
            }
        }

        return $result;
    }

    private static function initRequests()
    {
        if ((function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) ||
        (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase')) != 'off'))) {
            $_GET = self::recursiveStripSlashes($_GET);
            $_POST = self::recursiveStripSlashes($_POST);
            $_COOKIE = self::recursiveStripSlashes($_COOKIE);
            $_REQUEST = self::recursiveStripSlashes($_REQUEST);
        }
    }
}

?>