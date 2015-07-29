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


function smarty_core_get_php_resource(&$params, &$smarty)
{
    $params['resource_base_path'] = $smarty->trusted_dir;
    $smarty->_parse_resource_name($params, $smarty);

    if ($params['resource_type'] == 'file') {
        $_readable = false;
        if (file_exists($params['resource_name']) && is_readable($params['resource_name'])) {
            $_readable = true;
        } else {
            // test for file in include_path
            $_params = array('file_path' => $params['resource_name']);
            require_once SMARTY_CORE_DIR.'core.get_include_path.php';
            if (smarty_core_get_include_path($_params, $smarty)) {
                $_include_path = $_params['new_file_path'];
                $_readable = true;
            }
        }
    } elseif ($params['resource_type'] != 'file') {
        $_template_source = null;
        $_readable = is_callable($smarty->_plugins['resource'][$params['resource_type']][0][0])
            && call_user_func_array($smarty->_plugins['resource'][$params['resource_type']][0][0],
                                    array($params['resource_name'], &$_template_source, &$smarty));
    }

    if (method_exists($smarty, '_syntax_error')) {
        $_error_funcc = '_syntax_error';
    } else {
        $_error_funcc = 'trigger_error';
    }

    if ($_readable) {
        if ($smarty->security) {
            require_once SMARTY_CORE_DIR.'core.is_trusted.php';
            if (!smarty_core_is_trusted($params, $smarty)) {
                $smarty->$_error_funcc('(secure mode) '.$params['resource_type'].':'.$params['resource_name'].' is not trusted');

                return false;
            }
        }
    } else {
        $smarty->$_error_funcc($params['resource_type'].':'.$params['resource_name'].' is not readable');

        return false;
    }

    if ($params['resource_type'] == 'file') {
        $params['php_resource'] = $params['resource_name'];
    } else {
        $params['php_resource'] = $_template_source;
    }

    return true;
}

?>
