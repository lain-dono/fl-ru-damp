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


function smarty_modifier_debug_print_var($var, $depth = 0, $length = 40)
{
    $_replace = array(
        "\n" => '<i>\n</i>',
        "\r" => '<i>\r</i>',
        "\t" => '<i>\t</i>',
    );

    switch (gettype($var)) {
        case 'array' :
            $results = '<b>Array ('.count($var).')</b>';
            foreach ($var as $curr_key => $curr_val) {
                $results .= '<br>'.str_repeat('&nbsp;', $depth * 2)
                    .'<b>'.strtr($curr_key, $_replace).'</b> =&gt; '
                    .smarty_modifier_debug_print_var($curr_val, ++$depth, $length);
                --$depth;
            }
            break;
        case 'object' :
            $object_vars = get_object_vars($var);
            $results = '<b>'.get_class($var).' Object ('.count($object_vars).')</b>';
            foreach ($object_vars as $curr_key => $curr_val) {
                $results .= '<br>'.str_repeat('&nbsp;', $depth * 2)
                    .'<b> -&gt;'.strtr($curr_key, $_replace).'</b> = '
                    .smarty_modifier_debug_print_var($curr_val, ++$depth, $length);
                --$depth;
            }
            break;
        case 'boolean' :
        case 'NULL' :
        case 'resource' :
            if (true === $var) {
                $results = 'true';
            } elseif (false === $var) {
                $results = 'false';
            } elseif (null === $var) {
                $results = 'null';
            } else {
                $results = htmlspecialchars((string) $var);
            }
            $results = '<i>'.$results.'</i>';
            break;
        case 'integer' :
        case 'float' :
            $results = htmlspecialchars((string) $var);
            break;
        case 'string' :
            $results = strtr($var, $_replace);
            if (strlen($var) > $length) {
                $results = substr($var, 0, $length - 3).'...';
            }
            $results = htmlspecialchars('"'.$results.'"');
            break;
        case 'unknown type' :
        default :
            $results = strtr((string) $var, $_replace);
            if (strlen($results) > $length) {
                $results = substr($results, 0, $length - 3).'...';
            }
            $results = htmlspecialchars($results);
    }

    return $results;
}

?>
