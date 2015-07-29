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
require_once 'common.php';

class Resources
{
    private static $AVAILABLE_LOCALES = 'en,ru';

    private static $threadStateKeys = array(
    STATE_QUEUE => 'chat.thread.state_wait',
    STATE_QUEUE_EXACT_OPERATOR => 'chat.thread.state_wait_for_exact_agent',
    STATE_CHATTING_OPERATOR_BROWSER_CLOSED_REFRESHED => 'chat.thread.state_wait_for_another_agent',
    STATE_CHATTING => 'chat.thread.state_chatting_with_agent',
    STATE_CLOSED => 'chat.thread.state_closed',
    STATE_LOADING => 'chat.thread.state_loading',
    STATE_LOADING_FOR_EXACT_OPERATOR => 'chat.thread.state_loading',
    STATE_INVITE => 'chat.thread.state_invite',
    STATE_CHAT_VISITOR_BROWSER_CLOSED_REFRESHED => 'chat.thread.state_chatting_with_agent',
    STATE_CHATTING_CLOSED_REFRESHED => 'chat.thread.state_chatting_with_agent',
    STATE_REDIRECTED => 'chat.thread.redirected',
  );

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private static function getAll()
    {
        static $res = null;
        if (isset($res)) {
            return $res;
        }
        $resources_ru = self::readResources('ru');
        $resources_en = self::readResources('en');
    //    $var_name = 'resources_'.$locale;
    //    $current_res = $$var_name;

    $res = array('en' => $resources_en,'ru' => $resources_ru);

        return $res;
    }

    public static function GetCurrentSet()
    {
        return self::getAll(self::getCurrentLocale());
    }

    private static function readResources($locale)
    {
        $hash = array();
        self::readResourceFile(dirname(__FILE__)."/../locales/$locale/properties.txt", $hash);

        $fileName = $_SERVER['DOCUMENT_ROOT'].WEBIM_ROOT.'/themes/'.Browser::getCurrentTheme().'/locales/'.$locale.'/resources.txt';
        if (is_file($fileName)) {
            self::readResourceFile($fileName, $hash);
        }

        return $hash;
    }

    private static function readResourceFile($fileName, &$hash)
    {
        $fp = fopen($fileName, 'r');
        if ($fp) {
            while (!feof($fp)) {
                $line = fgets($fp, 4096);
                $line = str_replace("\n", '', $line);
                $line = str_replace("\r", '', $line);
                $keyval = split('=', $line, 2);
                if (count($keyval) == 2) {
                    $key = $keyval[0];
                    $value = $keyval[1];
                    $hash[$key] = str_replace('\\n', "\n", $value);
                }
            }
        }
        fclose($fp);
    }

    public static function Get($key, $params = array(), $locale = null)
    {
        if (empty($locale)) {
            $locale = self::getCurrentLocale();
        }
        $resources = self::getAll();
        $current = $resources[$locale];
        $res = self::getResource($current, $key, $params);
        if (isset($res)) {
            return $res;
        }

        return '!'.$key;
    }

    private static function getResource($resources, $key, $params)
    {
        if (isset($resources[$key])) {
            return self::fillPlaceholders($resources[$key], $params);
        }
    }

    private static function fillPlaceholders($str, $params = null)
    {
        if (empty($params)) {
            return $str;
        }
        if (!is_array($params)) {
            $params = array($params);
        }

        $patterns = array();
        $replacements = array();

        foreach ($params as $key => $value) {
            $patterns[] = '{'.$key.'}';
            $replacements[] = $value;
        }

        return str_replace($patterns, $replacements, $str);
    }

    public static function compareEncodings($e1, $e2)
    {
        $_e1 = str_replace('-', '', strtolower($e1));
        $_e2 = str_replace('-', '', strtolower($e2));

        return $_e1 == $_e2;
    }

    public static function ConvertWebIMToEncoding($encoding, $dir)
    {
        if (self::compareEncodings(WEBIM_ORIGINAL_ENCODING, $encoding)) {
            return;
        }

        $resources = listConvertableFiles($dir);

        foreach ($resources as $item) {
            $content = file_get_contents($item);
            $w_content = smarticonv(WEBIM_ORIGINAL_ENCODING, $encoding, $content);
            $result = file_put_contents($item, $w_content);

            if ($result === false) {
                return self::Get('errors.write.failed', array($item));
            }
        }

        return;
    }

    public static function IsLocaleAvailable($locale)
    {
        $res = in_array($locale, self::GetAvailableLocales());

        return $res;
    }

    public static function GetAvailableLocales()
    {
        static $arr;
        $arr = explode(',', self::$AVAILABLE_LOCALES);

        return $arr;
    }

    public static function SetLocaleLanguage()
    {
        if (self::getCurrentLocale() == 'ru') {
            $locale = 'ru_RU';
        } else {
            $locale = 'en_EN';
        }

        if (setlocale(LC_ALL, $locale.'.'.(defined('WEBIM_ENCODING') ? WEBIM_ENCODING : 'UTF-8')) === false) {
            setlocale(LC_ALL, self::getCurrentLocale());
        }
    }

    public static function getCurrentLocale()
    {
        $lang = DEFAULT_LOCALE;
    // check get
    if (!empty($_REQUEST['lang']) && self::IsLocaleAvailable($_REQUEST['lang'])) {
        $lang = $_REQUEST['lang'];
        $_SESSION['lang'] = $_REQUEST['lang'];
        setcookie('WEBIM_LOCALE', $_REQUEST['lang'], time() + 60 * 60 * 24 * 1000, WEBIM_ROOT.'/');
    } elseif (isset($_SESSION['lang']) && self::IsLocaleAvailable($_SESSION['lang'])) { // check session
      $lang = $_SESSION['lang'];
    } elseif (isset($_COOKIE['WEBIM_LOCALE']) && self::IsLocaleAvailable($_COOKIE['WEBIM_LOCALE'])) { // check cookie
      $lang = $_COOKIE['WEBIM_LOCALE'];
    } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { // check accept language
      $requested_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($requested_langs as $requested_lang) {
            if (strlen($requested_lang) > 2) {
                $requested_lang = substr($requested_lang, 0, 2);
            }
            if (self::IsLocaleAvailable($requested_lang)) {
                $lang = $requested_lang;
                break;
            }
        }
    } elseif (self::IsLocaleAvailable(DEFAULT_LOCALE)) { // check the default locale
      $lang = DEFAULT_LOCALE;
    } else { // can't find lang
      $lang = 'ru';
    }

        return $lang;
    }

    public static function GetStateName($state)
    {
        $key = self::$threadStateKeys[$state];

        return self::Get($key);
    }
}
?>