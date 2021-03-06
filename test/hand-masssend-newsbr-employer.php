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

// Только фрилансерам, активированным и неактивированным, незабаненным (is_banned = B'0'), с включенными рассылками

$sql = "SELECT uid, email, login, uname, usurname, subscr FROM employer WHERE is_banned = B'0'
        AND (substring(subscr from 8 for 1)::integer = 1)"; //employer

//$sql = "SELECT uid, email, login, uname, usurname FROM users WHERE login = 'land_e2'"; 

$pHost = str_replace('http://', '', $GLOBALS['host']);
if (defined('HTTP_PREFIX')) {
    $pHttp = str_replace('://', '', HTTP_PREFIX); // Введено с учетом того планируется включение HTTPS на серверах (для писем в ЛС)
} else {
    $pHttp = 'http';
}
$eHost = $GLOBALS['host'];

$pMessage = "
Здравствуйте!

Мы стремимся сделать работу на сайте максимально комфортной и безопасной. В связи с этим с сегодняшнего дня вся работа на сайте будет осуществляться только через сервис {$pHttp}:/{«Сделка Без Риска»}/{$pHost}/sbr/?utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer. Теперь вы можете быть уверены в том, что работа будет выполнена фрилансером в срок и согласно техническому заданию.

Расскажем коротко о преимуществах сервиса.<ul><li>Исполнитель не пропадет и не получит гонорар до тех пор, пока вы не примете его работу.</li><li>Если вас не устраивает результат сотрудничества, то вы можете обратиться в независимый {$pHttp}:/{Арбитраж Free-lance.ru}/{$pHost}/help/?q=1023&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer и вернуть деньги.</li><li>Вы можете {$pHttp}:/{работать по модели аккредитива}/{$pHost}/help/?q=1032&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer, в этом случае весь документооборот происходит онлайн, а налоги вы выплачиваете самостоятельно по завершению сделки.</li><li>При работе по договору подряда все необходимые налоги {$pHttp}:/{мы заплатим за вас}/{$pHost}/help/?q=1024&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer.</li></ul>
Комиссия за использование сервиса составляет 7% от общей суммы сделки. 
Подробности читайте в соответствующем разделе {$pHttp}:/{«Помощи»}/{$pHost}/help/?q=1016&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer.

Пожалуйста, обратите внимание на то, что с сегодняшнего дня на сайте запрещен обмен контактными данными в личных сообщениях, блогах и сообществах.

Вы можете отключить уведомления на странице {$pHttp}:/{странице «Уведомления/Рассылка»}/{$pHost}/users/%USER_LOGIN%/setup/mailer/ вашего аккаунта.

Приятной работы!
Команда {$pHttp}:/{Free-lance.ru}/{$pHost}/
";

$eSubject = 'Переходим на безопасное сотрудничество';

$eMessage = "<p>Здравствуйте!</p>
<p>
Мы стремимся сделать работу на сайте максимально комфортной и безопасной. В связи с этим с сегодняшнего дня вся работа на сайте будет осуществляться только через сервис <a href='{$eHost}/sbr/?utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer' target='_blank'>«Сделка Без Риска»</a>.
Теперь вы можете быть уверены в том, что работа будет выполнена фрилансером в срок и согласно техническому заданию.
</p>
<p>Расскажем коротко о преимуществах сервиса.</p>
<ul>
<li>Исполнитель не пропадет и не получит гонорар до тех пор, пока вы не примете его работу.</li>
<li>Если вас не устраивает результат сотрудничества, то вы можете обратиться в независимый <a href='{$eHost}/help/?q=1023&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer' target='_blank'>Арбитраж Free-lance.ru</a> и вернуть деньги.</li>
<li>Вы можете <a href='{$eHost}/help/?q=1032&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer' target='_blank'>работать по модели аккредитива</a>, в этом случае весь документооборот происходит онлайн, а налоги вы выплачиваете самостоятельно по завершению сделки.</li>
<li>При работе по договору подряда все необходимые налоги <a href='{$eHost}/help/?q=1024&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer' target='_blank'>мы заплатим за вас</a>.</li>
</ul>
<p>Комиссия за использование сервиса составляет 7% от общей суммы сделки.</p> 
<p>Подробности читайте в соответствующем разделе <a href='{$eHost}/help/?q=1016&utm_source=newsletter4&utm_medium=email&utm_campaign=SBR_19_employer' target='_blank'>«Помощи»</a>.</p>
<br/>
<p>Пожалуйста, обратите внимание на то, что с сегодняшнего дня на сайте запрещен обмен контактными данными в личных сообщениях, блогах и сообществах.</p>

<p>Вы можете отключить уведомления на странице <a href='{$eHost}/users/%USER_LOGIN%/setup/mailer/' target='_blank'>странице «Уведомления/Рассылка»</a> вашего аккаунта.</p>
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
