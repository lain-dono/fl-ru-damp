<?php

/**
 * П-1 (При подтверждении публикации проекта зарегистрированным работодателем).
 */

/**
 * Тема письма.
 */
$smail->subject = 'Подтверждение публикации проекта на сайте FL.ru';

$activate_url = sprintf('%s/guest/activate/%s/', $GLOBALS['host'], $code);

?>
Вы получили это письмо, т.к. ваш e-mail адрес был указан на сайте FL.ru при публикации нового проекта.

Чтобы завершить процесс публикации проекта, пожалуйста, перейдите по ссылке <a href="<?=$activate_url?>"><?=$activate_url?></a> или скопируйте ее в адресную строку браузера.

Если вы не публиковали проект на сайте FL.ru и не указывали свой e-mail – просто проигнорируйте письмо. Вероятно, один из наших пользователей ошибся адресом.