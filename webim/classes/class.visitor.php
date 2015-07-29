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
require_once 'models/generic/class.mapperfactory.php';
require_once 'functions.php';

class Visitor
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function canVisitorChangeName()
    {
        return true;
    }

    public function getEmail($threadid = false)
    {
        if (!$threadid) {
            return '';
        }
        $firstMessage = MapperFactory::getMapper('Message')->getFirstMessage($threadid);
        if (sizeof($firstMessage) == 0) {
            return '';
        }
        preg_match("/mail:.*?(\S*?@\S*?\.\S*)/mix", $firstMessage['message'], $find);
        if ($find[1]) {
            return $find[1];
        }

        return '';
    }

    public function getPhone()
    {
        return '';
    }

    public function setVisitorNameCookie($visitorName)
    {
        setcookie(WEBIM_COOKIE_VISITOR_NAME, $visitorName, time() + 60 * 60 * 24 * 365, '/');
    }
}

?>
