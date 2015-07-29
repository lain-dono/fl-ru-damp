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


//  $file_path, &$new_file_path

function smarty_core_get_include_path(&$params, &$smarty)
{
    static $_path_array = null;

    if (!isset($_path_array)) {
        $_ini_include_path = ini_get('include_path');

        if (strstr($_ini_include_path, ';')) {
            // windows pathnames
            $_path_array = explode(';', $_ini_include_path);
        } else {
            $_path_array = explode(':', $_ini_include_path);
        }
    }
    foreach ($_path_array as $_include_path) {
        if (@is_readable($_include_path.DIRECTORY_SEPARATOR.$params['file_path'])) {
            $params['new_file_path'] = $_include_path.DIRECTORY_SEPARATOR.$params['file_path'];

            return true;
        }
    }

    return false;
}

?>
