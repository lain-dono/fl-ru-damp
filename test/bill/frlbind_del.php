<?php

$operations = array(
    '0' => array(
        'op_code' => 142,
        'op_count' => 3,
        'complete_time' => '2014-09-12 07:55:05.260197',
    ),
    '1' => array(
        'op_code' => 142,
        'op_count' => 1,
        'complete_time' => '2014-09-18 12:14:39.896808',
    ) ,
    '2' => array(
        'op_code' => 148,
        'op_count' => 2 ,
        'complete_time' => '2014-09-24 06:44:51.943145',
    ) ,
    '3' => array(
        'op_code' => 148 ,
        'op_count' => 1 ,
        'complete_time' => '2014-09-24 06:45:09.209584',
    ) ,
    '4' => array(
        'op_code' => 148 ,
        'op_count' => 2 ,
        'complete_time' => '2014-09-24 07:07:12.71022',
    ) ,
    '5' => array(
        'op_code' => 151 ,
        'op_count' => 1 ,
        'complete_time' => '2014-09-24 09:50:35.646021',
    ) ,
    '6' => array(
        'op_code' => 148 ,
        'op_count' => 1 ,
        'complete_time' => '2014-10-02 06:39:59.780318',
    ) ,
    '7' => array(
        'op_code' => 148 ,
        'op_count' => 1 ,
        'complete_time' => '2014-10-20 08:38:47.63684',
    ) ,
    '8' => array(
        'op_code' => 142 ,
        'op_count' => 1 ,
        'complete_time' => '2014-12-18 06:21:28.720786',
    ) ,
    '9' => array(
        'op_code' => 148 ,
        'op_count' => 3 ,
        'complete_time' => '2014-12-18 06:22:27.066099',
    ),
);

foreach ($operations as $o) {
    //Устанавливаем даты начала и окончания
    if ($o['op_code'] == 142 || !isset($date_start)) {
        echo '---<br>';
        $date_start = DateTime::createFromFormat('Y-m-d H:i:s.u', $o['complete_time']);
        $date_stop = clone $date_start;
        $date_stop->add(new DateInterval('P'.($o['op_count'] * 7).'D'));
    } else {
        //Если продление - продляем дату окончания, обнуляем начало
        if ($o['op_code'] == 148) {
            $date_stop->add(new DateInterval('P'.($o['op_count'] * 7).'D'));
        }
        $date_start = DateTime::createFromFormat('Y-m-d H:i:s.u', $o['complete_time']);
    }

    echo $date_start->format('d.m.Y').' - '.$date_stop->format('d.m.Y');
    echo '<br />';
}
