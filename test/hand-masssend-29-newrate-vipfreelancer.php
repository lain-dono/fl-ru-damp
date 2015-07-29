<?php

/**
 * Уведомление работодателям.
 * */
ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

require_once '../classes/stdf.php';
require_once '../classes/memBuff.php';
require_once '../classes/smtp.php';

/*
 * Логин пользователя от кого осуществляется рассылка
 * 
 */
$sender = 'admin';

// Только VIP - фрилансерам, активированным (active = true), незабаненным (is_banned = B'0'), с включенными рассылками

$sql = "SELECT uid, email, login, uname, usurname, subscr FROM freelancer WHERE is_banned = B'0'
        AND (substring(subscr from 8 for 1)::integer = 1) AND login IN ('ATim', 'sashka77', 'OptimizationIM', 'SAndr', 'Sashkis', 'YourWebStyle', 'akvjob', 'Dimetrios', 'bolsunovskiy', 'SEOExpert', 'goword', 'miridea', 'dexter', 'nid', 'ShakkaR', ' Rudger', 'komiksar', 'webdelo-studio', 'KTIF', 'logotype', 'PetrT', 'Blixa', 'Janika', 'IMDT', 'sibirix-web', 'Brand-Book', 'icalipso', 'ArtHeads', 'olemskoi', 'zmeioka', 'marsh', 'seovia', 'itilect')"; //VIP - freelancer


 //$sql = "SELECT uid, email, login, uname, usurname FROM users WHERE login = 'land_e2'"; 

$pHost = str_replace('http://', '', $GLOBALS['host']);
if (defined('HTTP_PREFIX')) {
    $pHttp = str_replace('://', '', HTTP_PREFIX); // Введено с учетом того планируется включение HTTPS на серверах (для писем в ЛС)
} else {
    $pHttp = 'http';
}
$eHost = $GLOBALS['host'];

$pMessage = "Здравствуйте!

Рейтинг на нашем сайте – это показатель востребованности вас как профессионала и опытного специалиста в своем деле. Вы уже заработали себе высокий рейтинг, оставив далеко позади многих пользователей. 

Теперь же вы сможете увеличивать показатели, не прилагая к этому никаких усилий. А самое приятное – вам больше не придется тратить деньги на покупку рейтинга за FM: вы будете получать гораздо больше баллов, просто работая на сайте.

Мы перешли на новую систему расчета рейтинга: количество баллов = N % от суммы успешно завершенных этапов/30 (то есть на стоимость 1 FM). 

Проценты, которые пойдут в значение рейтинга, будут различными в зависимости от общей суммы всех успешно завершенных этапов проекта:
<ul>
<li>менее 5 000 рублей – 10%;</li><li>от 5001 рубля до 10 000 рублей – 15%;</li><li>от 10 001 рубля до 50 000 рублей – 20%;</li><li>более 50 001 рубля – 25%.</li></ul>
Сотрудничайте с заказчиками через сервис «<a href=\"{$eHost}/sbr/?utm_source=newsletter4&utm_medium=rassylka&utm_campaign=rating_freelancer\" target=\"_blank\">Сделка Без Риска</a>», и вы станете абсолютно недосягаемы – чем больше проектов и чем больше их бюджет, тем выше будет ваш рейтинг. Подробная информация находится в соответствующем <a href=\"{$eHost}/help/?q=1037&utm_source=newsletter4&utm_medium=rassylka&utm_campaign=rating_freelancer\" target=\"_blank\">разделе «Помощи»</a>

По всем возникающим вопросам вы можете обращаться в нашу http:/{службу поддержки}/{$pHost}/help/?all<i>.</i>
Вы можете отключить уведомления на http:/{странице &laquo;Уведомления/Рассылка&raquo;}/{$pHost}/users/%USER_LOGIN%/setup/mailer/<span> вашего</span> аккаунта.

Приятной работы,
Команда http:/{Free-lance.ru}/{$pHost}/
";

$eSubject = 'Free-lance.ru: высокий рейтинг без усилий';

$eMessage = "<p>Здравствуйте!</p>
<p>Рейтинг на нашем сайте – это показатель востребованности вас как профессионала и опытного специалиста в своем деле. Вы уже заработали себе высокий рейтинг, оставив далеко позади многих пользователей. </p>
<p>Теперь же вы сможете увеличивать показатели, не прилагая к этому никаких усилий. А самое приятное – вам больше не придется тратить деньги на покупку рейтинга за FM: вы будете получать гораздо больше баллов, просто работая на сайте.</p>
<p>Мы перешли на новую систему расчета рейтинга: количество баллов = N % от суммы успешно завершенных этапов/30 (то есть на стоимость 1 FM). </p>
<p>Проценты, которые пойдут в значение рейтинга, будут различными в зависимости от общей суммы всех успешно завершенных этапов проекта:</p>
<ul>
<li>менее 5 000 рублей – 10%;</li>
<li>от 5001 рубля до 10 000 рублей – 15%;</li>
<li>от 10 001 рубля до 50 000 рублей – 20%;</li>
<li>более 50 001 рубля – 25%.</li>
</ul>
<p>Сотрудничайте с заказчиками через сервис «<a href=\"{$eHost}/sbr/?utm_source=newsletter4&utm_medium=rassylka&utm_campaign=rating_freelancer\" target=\"_blank\">Сделка Без Риска</a>, и вы станете абсолютно недосягаемы – чем больше проектов и чем больше их бюджет, тем выше будет ваш рейтинг. Подробная информация находится в соответствующем <a href=\"{$eHost}/help/?q=1037&utm_source=newsletter4&utm_medium=rassylka&utm_campaign=rating_freelancer\" target=\"_blank\">разделе «Помощи»</a></p>
<p>По всем возникающим вопросам вы можете обращаться в нашу <a href=\"{$eHost}/help/?all\" target=\"_blank\">службу поддержки</a></p>
<p>Вы можете отключить уведомления на <a href=\"{$eHost}/users/%USER_LOGIN%/setup/mailer/\" target=\"_blank\">странице «Уведомления/Рассылка»</a> вашего аккаунта.</p>
<br/>
Приятной работы!<br/>
Команда <a href='{$eHost}/' target='_blank'>Free-lance.ru</a>";
// ----------------------------------------------------------------------------------------------------------------
// -- Рассылка ----------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------------
$DB = new DB('plproxy');
$master = new DB('master');
$cnt = 0;

$sender = $master->row('SELECT * FROM users WHERE login = ?', $sender);
if (empty($sender)) {
    die("Unknown Sender\n");
}

echo "Send personal messages\n";

// подготавливаем рассылку
$msgid = $DB->val("SELECT masssend(?, ?, '{}', '')", $sender['uid'], $pMessage);
if (!$msgid) {
    die('Failed!');
}

// допустим, мы получаем адресатов с какого-то запроса
$i = 0;
while ($users = $master->col("{$sql} LIMIT 30000 OFFSET ?", $i)) {
    $DB->query("SELECT masssend_bind(?, {$sender['uid']}, ?a)", $msgid, $users);
    $i = $i + 30000;
}
// Стартуем рассылку в личку
$DB->query('SELECT masssend_commit(?, ?)', $msgid, $sender['uid']);
echo "Send email messages\n";

$mail = new smtp();
$mail->subject = $eSubject;  // заголовок письма
$mail->message = $eMessage; // текст письма
$mail->recipient = ''; // свойство 'получатель' оставляем пустым
$spamid = $mail->send('text/html');
if (!$spamid) {
    die('Failed!');
}
// с этого момента рассылка создана, но еще никому не отправлена!
// допустим нам нужно получить список получателей с какого-либо запроса
$i = 0;
$mail->recipient = array();
$res = $master->query($sql);
while ($row = pg_fetch_assoc($res)) {
    $mail->recipient[] = array(
        'email' => $row['email'],
        'extra' => array('first_name' => $row['uname'], 'last_name' => $row['usurname'], 'USER_LOGIN' => $row['login']),
    );
    if (++$i >= 30000) {
        $mail->bind($spamid);
        $mail->recipient = array();
        $i = 0;
    }
    ++$cnt;
}
if ($i) {
    $mail->bind($spamid);
    $mail->recipient = array();
}

echo "OK. Total: {$cnt} users\n";
