<?php

/**
 * Если создан заказ, предложен исполнителю, и в течение суток от него не было никаких действий 
 * (отказа или подтверждения), то отправляем исполнителю почтовое уведомление:.
 */
$title = reformat(htmlspecialchars($order['title']), 30, 0, 1);
$order_url = $GLOBALS['host'].tservices_helper::getOrderCardUrl($order['id']);

$order_price = tservices_helper::cost_format($order['order_price'], true, false, false);
$order_days = tservices_helper::days_format($order['order_days']);

$accept_url = $GLOBALS['host'].tservices_helper::getOrderStatusUrl($order['id'], 'accept', $order['frl_id']);
$decline_url = $GLOBALS['host'].tservices_helper::getOrderStatusUrl($order['id'], 'decline', $order['frl_id']);

?>
Напоминаем, что вчера Заказчик <?=$emp_fullname?> предложил вам заказ на услугу 
&laquo;<a href="<?=$order_url?>"><?=$title?></a>&raquo; на сумму <?php echo $order_price ?> со сроком выполнения <?=$order_days?>.<br/>
Вы можете перейти к заказу и обговорить условия сотрудничества, начать выполнение заказа или отказаться от него.
<br/>
<br/> 
<a href="<?=$order_url?>">Перейти к заказу</a> / 
<a href="<?=$accept_url?>">Начать его выполнение</a> / 
<a href="<?=$decline_url?>">Отказаться от выполнения</a>
<br/>
<br/>