<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X8
    $res = $wmxi->X8(
        ANOTHER_WMID,  # WM-РёРґРµРЅС‚РёС„РёРєР°С‚РѕСЂ
        ANOTHER_PURSE  # РєРѕС€РµР»РµРє
    );

    print_r($res->toObject());
