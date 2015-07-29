<?php

/*
 * Шаблон письма уведомление исполнителю об отмене заказа со стороны заказчика. (УВ-7)
 * Так же используется при отправле ЛС поэтому все переводы каретки (\n) будут заменены <br/> при выводе сообщения и при отправке письма
 */

$smail->subject = "Отмена заказа на типовую услугу «{$order['title']}»";

$order_url = $GLOBALS['host'].tservices_helper::getOrderCardUrl($order['id']);
$title = reformat(htmlspecialchars($order['title']), 30, 0, 1);

?>
Здравствуйте.

Сожалеем, но заказчик <?=$emp_fullname?> отменил свой заказ &laquo;<a href="<?=$order_url ?>"><?=$title?></a>&raquo;. 
Вы можете связаться с заказчиком и уточнить у него причину отказа.

<a href="<?=$order_url?>">Связаться с заказчиком</a>

С уважением, 
команда <a href="<?php echo "{$GLOBALS['host']}/{$params}"; ?>">FL.ru</a>