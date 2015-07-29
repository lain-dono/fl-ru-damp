<?php

define('IS_SITE_ADMIN', 1);
$no_banner = 1;
	$rpath = '../../';
	require_once $_SERVER['DOCUMENT_ROOT'].'/classes/users.php';
	session_start();
	get_uid();

	if (!(hasPermissions('adm') && (hasPermissions('stats') || hasPermissions('tmppayments')))) {
	    header ('Location: /404.php');
	    exit;
	}

$content = '../content.php';

$inner_page = trim($_GET['page']);
if (!$inner_page) {
    $inner_page = 'charts';
}

$aMonthes[1] = 'январь';
$aMonthes[2] = 'февраль';
$aMonthes[3] = 'март';
$aMonthes[4] = 'апрель';
$aMonthes[5] = 'май';
$aMonthes[6] = 'июнь';
$aMonthes[7] = 'июль';
$aMonthes[8] = 'август';
$aMonthes[9] = 'сентябрь';
$aMonthes[10] = 'октябрь';
$aMonthes[11] = 'ноябрь';
$aMonthes[12] = 'декабрь';

$inner_page = 'inner_'.$inner_page.'.php';

$header = $rpath.'header.php';
$footer = $rpath.'footer.html';

include $rpath.'template.php';

?>
