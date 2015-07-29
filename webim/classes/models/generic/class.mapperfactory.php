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

class MapperFactory
{
    protected static $mappers = array();

    protected static $db = null;

    protected function __construct()
    {
    }

    public static function getMapper($model_class)
    {
        if (!isset(self::$mappers[$model_class])) {
            $mapper_class = $model_class.'Mapper';

            $include_file = dirname(__FILE__).'/../'.strtolower(SITE_DB_TYPE).'/class.'.strtolower($mapper_class).'.php';

            if (!include_once($include_file)) {
                throw new Exception("Cound't load mapper class $mapper_class file $include_file");
            }

            if (!self::$db) {
                $class = 'DBDriver'.ucfirst(SITE_DB_TYPE);

                $include_file = dirname(__FILE__).'/../dbdriver/class.'.strtolower($class).'.php';

                if (!include_once($include_file)) {
                    throw new Exception("Couldn't load dbdriver ".$class." file $include_file");
                }

                self::$db = new $class();
            }

            $mapper = new $mapper_class(self::$db, $model_class);
            self::$mappers[$model_class] = $mapper;
        }

        return self::$mappers[$model_class];
    }
}
?>