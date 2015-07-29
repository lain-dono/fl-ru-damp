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


// $auto_base, $auto_source = null, $auto_id = null, $exp_time = null

function smarty_core_rm_auto($params, &$smarty)
{
    if (!@is_dir($params['auto_base'])) {
        return false;
    }

    if (!isset($params['auto_id']) && !isset($params['auto_source'])) {
        $_params = array(
            'dirname' => $params['auto_base'],
            'level' => 0,
            'exp_time' => $params['exp_time'],
        );
        require_once SMARTY_CORE_DIR.'core.rmdir.php';
        $_res = smarty_core_rmdir($_params, $smarty);
    } else {
        $_visitorname = $smarty->_get_auto_filename($params['auto_base'], $params['auto_source'], $params['auto_id']);

        if (isset($params['auto_source'])) {
            if (isset($params['extensions'])) {
                $_res = false;
                foreach ((array) $params['extensions'] as $_extension) {
                    $_res |= $smarty->_unlink($_visitorname.$_extension, $params['exp_time']);
                }
            } else {
                $_res = $smarty->_unlink($_visitorname, $params['exp_time']);
            }
        } elseif ($smarty->use_sub_dirs) {
            $_params = array(
                'dirname' => $_visitorname,
                'level' => 1,
                'exp_time' => $params['exp_time'],
            );
            require_once SMARTY_CORE_DIR.'core.rmdir.php';
            $_res = smarty_core_rmdir($_params, $smarty);
        } else {
            // remove matching file names
            $_handle = opendir($params['auto_base']);
            $_res = true;
            while (false !== ($_filename = readdir($_handle))) {
                if ($_filename == '.' || $_filename == '..') {
                    continue;
                } elseif (substr($params['auto_base'].DIRECTORY_SEPARATOR.$_filename, 0, strlen($_visitorname)) == $_visitorname) {
                    $_res &= (bool) $smarty->_unlink($params['auto_base'].DIRECTORY_SEPARATOR.$_filename, $params['exp_time']);
                }
            }
        }
    }

    return $_res;
}

?>
