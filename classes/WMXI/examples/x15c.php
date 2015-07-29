<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X15
    $res = $wmxi->X15c(
        ANOTHER_WMID,   # WMID
        PRIMARY_WMID,   # WMID
        PRIMARY_PURSE,  # РєРѕС€РµР»РµРє
        1,              # Р°С‚СЂРёР±СѓС‚ inv
        1,              # Р°С‚СЂРёР±СѓС‚ trans
        1,              # Р°С‚СЂРёР±СѓС‚ purse
        1,              # Р°С‚СЂРёР±СѓС‚ transhist
        1,              # СЃСѓС‚РѕС‡РЅС‹Р№ Р»РёРјРёС‚
        1,              # РґРЅРµРІРЅРѕР№ Р»РёРјРёС‚
        1,              # РЅРµРґРµР»СЊРЅС‹Р№ Р»РёРјРёС‚
        1               # РјРµСЃСЏС‡РЅС‹Р№ Р»РёРјРёС‚
    );

    print_r($res->toObject());
