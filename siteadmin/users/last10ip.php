<?php
define('IS_SITE_ADMIN', 1);
require_once '../../classes/config.php';
require_once '../../classes/users.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/static_compress.php';
session_start();
get_uid();

$DB = new DB('master');

if (!(hasPermissions('adm') && hasPermissions('users'))) {
    header ('Location: /404.php');
    exit;
}

$stc = new static_compress();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /> 
	<title>Последние 10 IP</title> 
	<?php $stc->Send(); ?>
	</head> 
 
<body bgcolor="#FFFFFF" text="#000000"> 
<strong>Последние 10 IP: <?=htmlspecialchars($_GET['usurname'])?> <?=htmlspecialchars($_GET['uname'])?> [<?=htmlspecialchars($_GET['login'])?>]</strong>
<br/><br/>
<table width="100%" cellpadding="5" cellspacing="5">
    <tr bgcolor="#eeeeee">
        <td><strong>IP</strong></td>
        <td><strong>Дата</strong></td>
    </tr>
<?php
$sql = 'SELECT * FROM users_loginip_log WHERE uid=?i ORDER BY date desc';
$res = $DB->rows($sql, $_GET['uid']);

if ($res) {
    foreach ($res as $log) {
        ?>
    <tr>
        <td><a href="https://www.nic.ru/whois/?query=<?=long2ip($log['ip'])?>" target="_blank"><?=long2ip($log['ip'])?></a></td>
        <td><?=$log['date']?></td>
    </tr>
<?php

    }
} else {
    ?>
    <tr>
        <td colspan="2" align="center">Данных не найдено</td>
    </tr>
<?php

}
?>
</table>
</body>
</html> 

