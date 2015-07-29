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


//  $dirname, $level = 1, $exp_time = null

function smarty_core_rmdir($params, &$smarty)
{
    if (!isset($params['level'])) {
        $params['level'] = 1;
    }
    if (!isset($params['exp_time'])) {
        $params['exp_time'] = null;
    }

    if ($_handle = @opendir($params['dirname'])) {
        while (false !== ($_entry = readdir($_handle))) {
            if ($_entry != '.' && $_entry != '..') {
                if (@is_dir($params['dirname'].DIRECTORY_SEPARATOR.$_entry)) {
                    $_params = array(
                        'dirname' => $params['dirname'].DIRECTORY_SEPARATOR.$_entry,
                        'level' => $params['level'] + 1,
                        'exp_time' => $params['exp_time'],
                    );
                    smarty_core_rmdir($_params, $smarty);
                } else {
                    $smarty->_unlink($params['dirname'].DIRECTORY_SEPARATOR.$_entry, $params['exp_time']);
                }
            }
        }
        closedir($_handle);
    }

    if ($params['level']) {
        return @rmdir($params['dirname']);
    }

    return (bool) $_handle;
}

?>
