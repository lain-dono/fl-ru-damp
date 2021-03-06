<?php

/**
 * П-1, П-2 (При подтверждении публикации вакансии зарегистрированным работодателем)
 * При публикации админом.
 */

/**
 * Тема письма.
 */
$smail->subject = 'Подтверждение публикации вакансии на сайте FL.ru';

$activate_url = sprintf('%s/guest/activate/%s/', $GLOBALS['host'], $code);
$unsubscribe_uri = $GLOBALS['host'].$unsubscribe_uri;

?>
Вы разместили вакансию – <a href="<?=$link?>"><?=$link?></a>

Предлагаем вам разместить эту же вакансию на самой крупной бирже удаленной работы FL.ru, где зарегистрировано более 1 млн исполнителей.

На бирже FL.ru для оценки исполнителя вам доступно его портфолио и реальные отзывы от его работодателей, что позволяет более полно оценить кандидата.

Чтобы разместить вакансию на сайте FL.ru, перейдите по ссылке <a href="<?=$activate_url?>"><?=$activate_url?></a> или скопируйте ее в адресную строку браузера.

Если вы не хотите получать подобные предложения от сайта FL.ru – нажмите <a href="<?=$unsubscribe_uri?>">Отписаться</a>.