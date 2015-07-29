<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X14
    $res = $wmxi->X14(
        1,   # РЅРѕРјРµСЂ С‚СЂР°РЅР·Р°РєС†РёРё
        0.1  # СЃСѓРјРјР° С‚СЂР°РЅР·Р°РєС†РёРё
    );

    print_r($res->toObject());
