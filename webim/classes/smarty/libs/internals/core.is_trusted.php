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


 // $resource_type, $resource_name

function smarty_core_is_trusted($params, &$smarty)
{
    $_smarty_trusted = false;
    if ($params['resource_type'] == 'file') {
        if (!empty($smarty->trusted_dir)) {
            $_rp = realpath($params['resource_name']);
            foreach ((array) $smarty->trusted_dir as $curr_dir) {
                if (!empty($curr_dir) && is_readable($curr_dir)) {
                    $_cd = realpath($curr_dir);
                    if (strncmp($_rp, $_cd, strlen($_cd)) == 0
                        && substr($_rp, strlen($_cd), 1) == DIRECTORY_SEPARATOR) {
                        $_smarty_trusted = true;
                        break;
                    }
                }
            }
        }
    } else {
        // resource is not on local file system
        $_smarty_trusted = call_user_func_array($smarty->_plugins['resource'][$params['resource_type']][0][3],
                                                array($params['resource_name'], $smarty));
    }

    return $_smarty_trusted;
}

?>
