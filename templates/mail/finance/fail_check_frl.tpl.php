<?php

/**
 * П-25 - Исполнителю о неуспешной проверке Финансов модератором.
 */

/**
 * Тема письма.
 */
$smail->subject = 'Ваши финансовые данные не прошли проверку модератором';

$fn_url = sprintf('%s/users/%s/setup/finance/', $GLOBALS['host'], $user['login']);

?>
Сожалеем, но модератор сайта FL.ru обнаружил, что данные на странице Финансы вашего аккаунта указаны некорректно:
<?=$data['reason']?>

Пожалуйста, укажите в Финансах корректные данные, чтобы они прошли проверку, и можно было вернуться к процессу выплаты суммы в Заказе.
<a href="<?=$fn_url?>">Перейти на страницу Финансы</a>
