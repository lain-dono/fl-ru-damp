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

require_once 'functions.php';

class Browser
{
    private static $knownAgents = array('chrome', 'opera', 'msie', 'safari', 'firefox', 'netscape', 'mozilla');

    public static function GetRemoteLevel($puseragent)
    {
        $useragent = strtolower($puseragent);

        foreach (self::$knownAgents as $agent) {
            if (strstr($useragent, $agent)) {
                if (preg_match('/'.$agent."[\\s\/]?(\\d+(\\.\\d+)?)/", $useragent, $matches)) {
                    $ver = $matches[1];

                    if (self::isAjaxBrowser($agent, $ver, $useragent)) {
                        return 'ajaxed';
                    } elseif (self::isOldBrowser($agent, $ver)) {
                        return 'old';
                    }

                    return 'simple';
                }
            }
        }

        return 'simple';
    }

    private static function isAjaxBrowser($browserid, $ver, $useragent)
    {
        if ($browserid == 'opera') {
            return $ver >= 8.02;
        }
        if ($browserid == 'safari') {
            return $ver >= 125;
        }
        if ($browserid == 'msie') {
            return $ver >= 5.5 && !strstr($useragent, 'powerpc');
        }
        if ($browserid == 'netscape') {
            return $ver >= 7.1;
        }
        if ($browserid == 'mozilla') {
            return $ver >= 1.4;
        }
        if ($browserid == 'firefox') {
            return $ver >= 1.0;
        }
        if ($browserid == 'chrome') {
            return $ver >= 0.1;
        }

        return false;
    }

    private static function isOldBrowser($browserid, $ver)
    {
        if ($browserid == 'opera') {
            return $ver < 7.0;
        }
        if ($browserid == 'msie') {
            return $ver < 5.0;
        }

        return false;
    }

    public static function GetBrowserAndVersion($userAgent)
    {
        $userAgent = strtolower($userAgent);
        foreach (self::$knownAgents as $agent) {
            if (strstr($userAgent, $agent)) {
                if (preg_match('/'.$agent."[\\s\/]?(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
                    $ver = $matches[1];
                    if ($agent == 'safari') {
                        if (preg_match("/version\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
                            $ver = $matches[1];
                        } else {
                            $ver = '1 or 2(build '.$ver.')';
                        }
                        if (preg_match("/mobile\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
                            $userAgent = 'iPhone '.$matches[1]."($agent $ver)";
                            break;
                        }
                    }

                    $userAgent = ucfirst($agent).' '.$ver;
                    break;
                }
            }
        }

        return $userAgent;
    }

    public static function SendNoCache()
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    public static function SendHtmlHeaders()
    {
        self::SendNoCache();
        header('Content-type: text/html; charset='.BROWSER_CHARSET);
    }

    public static function SendXmlHeaders()
    {
        self::SendNoCache();
        header('Content-type: text/xml; charset='.BROWSER_CHARSET);
        echo '<'.'?xml version="1.0" encoding="'.BROWSER_CHARSET.'"?'.'>';
    }

    public static function ChangeLocation($url, $params = null)
    {
        if (!empty($get)) {
            $url = $url.(strstr($url, '?') ? '&' : '?').'lang='.WEBIM_CURRENT_LOCALE;

            $chunks = array();
            foreach ($get as $key => $value) {
                $chunks[] = $key.'='.urlencode($value);
            }
            $url .= '&'.implode('&', $chunks);
        }
        header('Location: '.$url);
        if ($force) {
            exit;
        }
    }

    public static function displayAjaxError($text)
    {
        $message = Resources::Get('agent.not_logged_in');
        $message = self::AddCdata($message);
        echo '<error type="1"><descr>'.$message.'</descr></error>';
    }

    public static function AddCdata($text)
    {
        return '<![CDATA['.str_replace(']]>', ']]>]]&gt;<![CDATA[', $text).']]>';
    }

    public static function GetExtAddr()
    {
        if (USE_X_FORWARDED_FOR && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR']) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getCurrentTheme()
    {
        $theme = verify_param('theme', "/^\w+$/", 'default');

        return $theme;
    }

    public static function getOpener()
    {
        if (isset($_REQUEST['opener'])) {
            $referer = $_REQUEST['opener'];
        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        } else {
            $referer = null;
        }

        return $referer;
    }
}
?>