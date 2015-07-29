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

class Settings
{
    private $settings = null;

    protected $tableName = 'chatconfig';
    protected $uniqueTableKey = 'configid';
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

    public static function Get($key, $defaultValue = null)
    {
        $res = self::getInstance()->_get($key);

        if (isset($defaultValue) && empty($res)) {
            $res = $defaultValue;
        }

        return $res;
    }

    public function Set($key, $value)
    {
        MapperFactory::getMapper('Config')->save(array(
        'configkey' => $key,
        'configvalue' => $value,
      )
    );
    }

    private function _get($key)
    {
        $this->ensureLoaded();

        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    public function GetAll()
    {
        $this->ensureLoaded();

        return $this->settings;
    }

    private function ensureLoaded()
    {
        if (!isset($this->settings)) {
            $this->settings = MapperFactory::getMapper('Config')->enumPairs();
        }
    }

   // need to setup config
//  function LoadSettingsPro() {
//    foreach (Resources::GetAvailableLocales() as $locale) {
//      $answers = Resources::Get("chat.predefined_answers", array(), $locale);
//      $this->Settings["answers_".$locale] = $answers;
//    }
//    return true;
//  }

  //====================================================================================================


  public static function GetProductName()
  {
      $product = Resources::Get('webim.pro.title');

      return $product;
  }

    public static function GetProduct()
    {
        $product = 'pro';

        return $product;
    }

    public static function GetProductURL()
    {
        $url = 'http://webim.ru/pro/?p=pro';

        return $url;
    }

    public static function GetProductAndVersion()
    {
        return self::GetProductName().' '.WEBIM_VERSION;
    }
}
?>
