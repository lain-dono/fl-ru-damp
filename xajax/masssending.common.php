<?phpdefine('XAJAX_DEFAULT_CHAR_ENCODING', 'windows-1251');require_once $_SERVER['DOCUMENT_ROOT'].'/xajax/xajax_core/xajax.inc.php';require_once $_SERVER['DOCUMENT_ROOT'].'/classes/config.php';require_once $_SERVER['DOCUMENT_ROOT'].'/classes/stdf.php';global $xajax;if (!$xajax) {
    $xajax = new xajax('/xajax/masssending.server.php');
    $xajax->configure('decodeUTF8Input', true);
    $xajax->configure('scriptLoadTimeout', XAJAX_LOAD_TIMEOUT);
    $xajax->register(XAJAX_FUNCTION, 'GetCities');
    $xajax->register(XAJAX_FUNCTION, 'DelFile');
    $xajax->register(XAJAX_FUNCTION, 'Calculate');
    $xajax->register(XAJAX_FUNCTION, 'CalculateFromSearch');    
    $xajax->register(XAJAX_FUNCTION, 'MasssendingEdit');
    $xajax->register(XAJAX_FUNCTION, 'MasssendingSave');

}
