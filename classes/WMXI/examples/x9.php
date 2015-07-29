<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X9
    $res = $wmxi->X9(
        PRIMARY_WMID  # WM-РёРґРµРЅС‚РёС„РёРєР°С‚РѕСЂ
    );

    print_r($res->toObject());
