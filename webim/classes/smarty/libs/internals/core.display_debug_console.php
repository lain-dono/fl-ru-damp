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


function smarty_core_display_debug_console($params, &$smarty)
{
    // we must force compile the debug template in case the environment
    // changed between separate applications.

    if (empty($smarty->debug_tpl)) {
        // set path to debug template from SMARTY_DIR
        $smarty->debug_tpl = SMARTY_DIR.'debug.tpl';
        if ($smarty->security && is_file($smarty->debug_tpl)) {
            $smarty->secure_dir[] = realpath($smarty->debug_tpl);
        }
        $smarty->debug_tpl = 'file:'.SMARTY_DIR.'debug.tpl';
    }

    $_ldelim_orig = $smarty->left_delimiter;
    $_rdelim_orig = $smarty->right_delimiter;

    $smarty->left_delimiter = '{';
    $smarty->right_delimiter = '}';

    $_compile_id_orig = $smarty->_compile_id;
    $smarty->_compile_id = null;

    $_compile_path = $smarty->_get_compile_path($smarty->debug_tpl);
    if ($smarty->_compile_resource($smarty->debug_tpl, $_compile_path)) {
        ob_start();
        $smarty->_include($_compile_path);
        $_results = ob_get_contents();
        ob_end_clean();
    } else {
        $_results = '';
    }

    $smarty->_compile_id = $_compile_id_orig;

    $smarty->left_delimiter = $_ldelim_orig;
    $smarty->right_delimiter = $_rdelim_orig;

    return $_results;
}

?>
