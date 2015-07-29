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


//  $resource_type, $resource_name

function smarty_core_is_secure($params, &$smarty)
{
    if (!$smarty->security || $smarty->security_settings['INCLUDE_ANY']) {
        return true;
    }

    if ($params['resource_type'] == 'file') {
        $_rp = realpath($params['resource_name']);
        if (isset($params['resource_base_path'])) {
            foreach ((array) $params['resource_base_path'] as $curr_dir) {
                if (($_cd = realpath($curr_dir)) !== false &&
                     strncmp($_rp, $_cd, strlen($_cd)) == 0 &&
                     substr($_rp, strlen($_cd), 1) == DIRECTORY_SEPARATOR) {
                    return true;
                }
            }
        }
        if (!empty($smarty->secure_dir)) {
            foreach ((array) $smarty->secure_dir as $curr_dir) {
                if (($_cd = realpath($curr_dir)) !== false) {
                    if ($_cd == $_rp) {
                        return true;
                    } elseif (strncmp($_rp, $_cd, strlen($_cd)) == 0 &&
                        substr($_rp, strlen($_cd), 1) == DIRECTORY_SEPARATOR) {
                        return true;
                    }
                }
            }
        }
    } else {
        // resource is not on local file system
        return call_user_func_array(
            $smarty->_plugins['resource'][$params['resource_type']][0][2],
            array($params['resource_name'], &$smarty));
    }

    return false;
}

?>
