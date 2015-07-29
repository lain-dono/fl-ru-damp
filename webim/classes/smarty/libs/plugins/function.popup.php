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


function smarty_function_popup($params, &$smarty)
{
    $append = '';
    foreach ($params as $_key => $_value) {
        switch ($_key) {
            case 'text':
            case 'trigger':
            case 'function':
            case 'inarray':
                $$_key = (string) $_value;
                if ($_key == 'function' || $_key == 'inarray') {
                    $append .= ','.strtoupper($_key).",'$_value'";
                }
                break;

            case 'caption':
            case 'closetext':
            case 'status':
                $append .= ','.strtoupper($_key).",'".str_replace("'", "\'", $_value)."'";
                break;

            case 'fgcolor':
            case 'bgcolor':
            case 'textcolor':
            case 'capcolor':
            case 'closecolor':
            case 'textfont':
            case 'captionfont':
            case 'closefont':
            case 'fgbackground':
            case 'bgbackground':
            case 'caparray':
            case 'capicon':
            case 'background':
            case 'frame':
                $append .= ','.strtoupper($_key).",'$_value'";
                break;

            case 'textsize':
            case 'captionsize':
            case 'closesize':
            case 'width':
            case 'height':
            case 'border':
            case 'offsetx':
            case 'offsety':
            case 'snapx':
            case 'snapy':
            case 'fixx':
            case 'fixy':
            case 'padx':
            case 'pady':
            case 'timeout':
            case 'delay':
                $append .= ','.strtoupper($_key).",$_value";
                break;

            case 'sticky':
            case 'left':
            case 'right':
            case 'center':
            case 'above':
            case 'below':
            case 'noclose':
            case 'autostatus':
            case 'autostatuscap':
            case 'fullhtml':
            case 'hauto':
            case 'vauto':
            case 'mouseoff':
            case 'followmouse':
            case 'closeclick':
                if ($_value) {
                    $append .= ','.strtoupper($_key);
                }
                break;

            default:
                $smarty->trigger_error("[popup] unknown parameter $_key", E_USER_WARNING);
        }
    }

    if (empty($text) && !isset($inarray) && empty($function)) {
        $smarty->trigger_error("overlib: attribute 'text' or 'inarray' or 'function' required");

        return false;
    }

    if (empty($trigger)) {
        $trigger = 'onmouseover';
    }

    $retval = $trigger.'="return overlib(\''.preg_replace(array("!'!", "![\r\n]!"), array("\'", '\r'), $text).'\'';
    $retval .= $append.');"';
    if ($trigger == 'onmouseover') {
        $retval .= ' onmouseout="nd();"';
    }

    return $retval;
}

?>
