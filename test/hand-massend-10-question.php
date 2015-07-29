<?php

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

// Только фрилансерам
$sql = "SELECT uid, email, login, uname, usurname, subscr FROM freelancer WHERE substring(subscr from 8 for 1)::integer = 1 AND is_banned = B'0'";

$pHost = str_replace('http://', '', $GLOBALS['host']);
$eHost = $GLOBALS['host'];
$pMessage = "
Здравствуйте!

Рекомендации – это отличный способ продемонстрировать заказчикам ваш высокий профессиональный уровень и богатый опыт работы. Чем больше у вас рекомендаций от работодателей, тем выше ваша репутация. Сегодня мы хотим дать ответы на самые часто задаваемые вопросы по http:/{сервису «Рекомендации»}/{$pHost}/users/%USER_LOGIN%/opinions/?utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq<i>.</i>

1. Кому нужна услуга «Рекомендации»?
<i>И работодателям, и фрилансерам – для того, чтобы показать серьезное отношение к работе на ресурсе и повысить свой рейтинг.</i>

2. Чем рекомендация отличается от мнения?
<i>Мнения отражают субъективные – как положительные, так и отрицательные –  суждения других пользователей о вас, которые могут быть ни на чем не основаны. Рекомендация – это отзыв о вашей работе по результатам документально подтвержденного факта сотрудничества, который будет одобрен модераторами только после тщательной проверки.</i>

3. Почему нельзя оставить отрицательную рекомендацию?
<i>Рекомендация отражает ваше желание выразить удовлетворение фактом сотрудничества с другим пользователем.</i>

4. Почему нужен договор для подтверждения рекомендации?
<i>Перед тем как одобрить рекомендацию, мы должны ознакомиться с условиями, на которых состоялось сотрудничество, и проверить, были ли они соблюдены в полной мере.</i>

5. Где взять пример договора для сервиса «Рекомендации?
<i>Образцы документов находятся в разделе «Шаблоны документов», рубрика «http:/{Шаблоны договоров}/{$pHost}/service/docs/section/?id=71&utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq<span>»</span>.</i>

6. Как должно выглядеть ТЗ для подтверждения рекомендации?
<i>Нет никакой строгой формы, однако в ТЗ обязательно должно быть прописано то, что необходимо сделать фрилансеру в рамках сотрудничества с заказчиком.</i>

7. Что делать, если нет возможности предоставить результат работы?
<i>К сожалению, в данном случае принять рекомендацию не получится.</i>

8. Как мне нужно будет оплатить рекомендацию?
<i>Вы можете сделать это в любой момент с вашего личного счета на Free-lance.ru.</i>

9. Рекомендации могут получать только резиденты РФ, Украины, Беларуси и Казахстана?
<i>Нет, сервис доступен для всех пользователей сайта.</i>

10. Как посчитать, сколько баллов рейтинга приносит рекомендация?
<i>Нет необходимости считать самостоятельно. При заполнении формы получения рекомендации количество баллов будет посчитано автоматически.</i>

Если вы хотите оставить рекомендацию другому пользователю прямо сейчас, зайдите на вкладку «Отзывы» в его профиле и нажмите на кнопку «Новая рекомендация». Более подробно об услуге «Рекомендации» можно узнать в http:/{соответствующем разделе помощи}/{$pHost}/help/?q=1002&utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq<i>.</i>

По всем возникающим вопросам вы можете обращаться в нашу http:/{службу поддержки}/{$pHost}/help/?all&utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq<i>.</i>
Вы можете отключить уведомления на http:/{странице «Уведомления/Рассылка»}/{$pHost}/users/%USER_LOGIN%/setup/mailer/?utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq вашего аккаунта.

Приятной работы!
Команда Free-lance.ru";

$eSubject = '10 фактов о рекомендациях на Free-lance.ru';

$eMessage = "<p>Здравствуйте!</p>

<p>
Рекомендации – это отличный способ продемонстрировать заказчикам ваш высокий профессиональный уровень и богатый опыт работы. Чем больше у вас рекомендаций от работодателей, тем выше ваша репутация. Сегодня мы хотим дать ответы на самые часто задаваемые вопросы по <a href='{$eHost}/users/%USER_LOGIN%/opinions/?utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq'>сервису «Рекомендации»</a>.
</p>

<p>
<ol>
    <li>
        Кому нужна услуга «Рекомендации»?<br/>
        <i>И работодателям, и фрилансерам – для того, чтобы показать серьезное отношение к работе на ресурсе и повысить свой рейтинг.</i>
    <br><br></li>
    <li>
        Чем рекомендация отличается от мнения?<br/>
        <i>Мнения отражают субъективные – как положительные, так и отрицательные –  суждения других пользователей о вас, которые могут быть ни на чем не основаны. Рекомендация – это отзыв о вашей работе по результатам документально подтвержденного факта сотрудничества, который будет одобрен модераторами только после тщательной проверки.</i>
    <br><br></li>
    <li>
        Почему нельзя оставить отрицательную рекомендацию?<br/>
        <i>Рекомендация отражает ваше желание выразить удовлетворение фактом сотрудничества с другим пользователем.</i>
    <br><br></li>
    <li>
        Почему нужен договор для подтверждения рекомендации?<br/>
        <i>Перед тем как одобрить рекомендацию, мы должны ознакомиться с условиями, на которых состоялось сотрудничество, и проверить, были ли они соблюдены в полной мере.</i>
    <br><br></li>
    <li>
        Где взять пример договора для сервиса «Рекомендации?<br/>
        <i>Образцы документов находятся в разделе «Шаблоны документов», рубрика «<a href='{$eHost}/service/docs/section/?id=71&utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq'>Шаблоны договоров</a>».</i>
    <br><br></li>
    <li>
        Как должно выглядеть ТЗ для подтверждения рекомендации?<br/>
        <i>Нет никакой строгой формы, однако в ТЗ обязательно должно быть прописано то, что необходимо сделать фрилансеру в рамках сотрудничества с заказчиком.</i>
    <br><br></li>
    <li>
        Что делать, если нет возможности предоставить результат работы?<br/>
        <i>К сожалению, в данном случае принять рекомендацию не получится.</i>
    <br><br></li>
    <li>
        Как мне нужно будет оплатить рекомендацию?<br/>
        <i>Вы можете сделать это в любой момент с вашего личного счета на Free-lance.ru.</i>
    <br><br></li>
    <li>
        Рекомендации могут получать только резиденты РФ, Украины, Беларуси и Казахстана?<br/>
        <i>Нет, сервис доступен для всех пользователей сайта.</i>
    <br><br></li>
    <li>
        Как посчитать, сколько баллов рейтинга приносит рекомендация?<br/>
        <i>Нет необходимости считать самостоятельно. При заполнении формы получения рекомендации количество баллов будет посчитано автоматически.</i><br/><br/>
        Если вы хотите оставить рекомендацию другому пользователю прямо сейчас, зайдите на вкладку «Отзывы» в его профиле и нажмите на кнопку «Новая рекомендация». Более подробно об услуге «Рекомендации» можно узнать в <a href='{$eHost}/help/?q=1002&utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq'>соответствующем разделе помощи</a>.
    <br><br></li>
</ol>        
</p>
     
<p>
По всем возникающим вопросам вы можете обращаться в нашу <a href='{$eHost}/help/?all&utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq'>службу поддержки</a>.<br/>
Вы можете отключить уведомления на <a href='{$eHost}/users/%USER_LOGIN%/setup/mailer/?utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq'>странице «Уведомления/Рассылка»</a> вашего аккаунта.
</p>

<p>
Приятной работы!<br/>
Команда <a href='{$eHost}/?utm_source=newsleter4&utm_medium=rassilka&utm_campaign=recomendations_faq'>Free-lance.ru</a>
</p>";

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
