<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X17
    $res = $wmxi->X17b(
        1  # РќРѕРјРµСЂ РєРѕРЅС‚СЂР°РєС‚Р°
    );

    print_r($res->toObject());
