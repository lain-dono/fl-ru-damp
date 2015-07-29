<?php

require_once '../classes/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/smail.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/users.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/firstpage.php';

$mail = new smail();

// 1
$f_user_admin = users::GetUid($err, 'admin');

$user['uname'] = 'вася';
$user['usurname'] = 'Пупкин';
$user['login'] = 'vp';
$user['email'] = 'vishna-v-sahare@mail.ru';
$prof['name'] = 'nnnn';
$prof['id'] = 10;
$prof['cost'] = 15;
$days = 2;

$mail->subject = 'Недостаточно средств для автоматического продления на Free-lance.ru';  
$mail->recipient = "{$user['uname']} {$user['usurname']} [{$user['login']}] <{$user['email']}>";

	        $html = '';
	           $prof_name = $prof['name'];
    	       if ($prof['id'] == 0) {
    	           $prof_name = 'Все фрилансеры';
    	       }

	           $html .= "-&nbsp;<a href=\"{$GLOBALS['host']}/firstpage/?prof={$prof['id']}\">{$prof_name}</a> ({$prof['cost']} FM)<br/>";

	        $dev = 111;
                $date_dest = strtotime('+'.$days.' days');
                $date = date('d '.monthtostr(date('m', $date_dest)).' Y года', $date_dest);
	        $body = 'До активации функции автопродления '.ending($days, 'остался', 'осталось', 'осталось').' '.number2string($days, 1).' '.ending($days, 'день', 'дня', 'дней').". Через $days ".ending($days, 'день', 'дня', 'дней').", {$date}, должно быть автоматически продлено размещение в следующих разделах сайта Free-lance.ru:<br/>
{$html}
Всего с вашего счета должно быть списано {$val['sum_cost']} FM.<br/>
Сейчас на вашем Личном счету {$val['sum']} FM. Для срабатывания автоматического продления недостаточно средств.<br/><br/>
Напоминаем вам, что автоматическое продление происходит в случае, когда на вашем личном счету достаточно средств для оплаты продления всех указанных разделов.<br/> 
Пожалуйста, пополните счет или измените настройки автоматического продления.<br/>
<br/>
Счет можно пополнить на следующей странице: <a href=\"{$GLOBALS['host']}/bill/\">{$GLOBALS['host']}/bill/</a><br/>
Функцию автопродления можно настроить или отключить здесь: <a href=\"{$GLOBALS['host']}/firstpage/\">{$GLOBALS['host']}/firstpage/</a>";

	        $mail->message = $mail->GetHtml($user['uname'], $body, 'simple');
echo $mail->message;
	        $mail->SmtpMail('text/html');

?>
