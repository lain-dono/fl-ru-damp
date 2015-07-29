<?php

/**
 * П-3 (При подтверждении регистрации нового пользователя и публикации вакансии).
 */

/**
 * Тема письма.
 */
$smail->subject = 'Подтверждение публикации вакансии на сайте FL.ru';

$activate_url = sprintf('%s/guest/activate/%s/', $GLOBALS['host'], $code);
$pro_url = $GLOBALS['host'].'/payed-emp/';

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/projects.php';
$vacancy_price = new_projects::getProjectInOfficePrice(false);
$vacancy_price_pro = new_projects::getProjectInOfficePrice(true);
?>
Вы получили это письмо, т.к. ваш e-mail адрес был указан на сайте FL.ru при регистрации профиля и публикации новой вакансии.

Чтобы завершить регистрацию и опубликовать вакансию за <?=$vacancy_price?> рублей, пожалуйста, перейдите по ссылке <a href="<?=$activate_url?>"><?=$activate_url?></a> или скопируйте ее в адресную строку браузера.

Если вы планируете разместить больше одной вакансии, рекомендуем сэкономить, <a href="<?=$pro_url?>">купив аккаунт PRO</a> – с ним вы можете размещать вакансии за <?=$vacancy_price_pro?> рублей.

Если вы не публиковали вакансию на сайте FL.ru и не указывали свой e-mail – просто проигнорируйте письмо. Вероятно, один из наших пользователей ошибся адресом.