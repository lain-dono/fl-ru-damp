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


function smarty_core_assign_smarty_interface($params, &$smarty)
{
    if (isset($smarty->_smarty_vars) && isset($smarty->_smarty_vars['request'])) {
        return;
    }

    $_globals_map = array('g' => 'HTTP_GET_VARS',
                             'p' => 'HTTP_POST_VARS',
                             'c' => 'HTTP_COOKIE_VARS',
                             's' => 'HTTP_SERVER_VARS',
                             'e' => 'HTTP_ENV_VARS', );

    $_smarty_vars_request = array();

    foreach (preg_split('!!', strtolower($smarty->request_vars_order)) as $_c) {
        if (isset($_globals_map[$_c])) {
            $_smarty_vars_request = array_merge($_smarty_vars_request, $GLOBALS[$_globals_map[$_c]]);
        }
    }
    $_smarty_vars_request = @array_merge($_smarty_vars_request, $GLOBALS['HTTP_SESSION_VARS']);

    $smarty->_smarty_vars['request'] = $_smarty_vars_request;
}

?>
