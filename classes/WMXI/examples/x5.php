<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X5
    $res = $wmxi->X5(
        1,     # СѓРЅРёРєР°Р»СЊРЅС‹Р№ РЅРѕРјРµСЂ РїР»Р°С‚РµР¶Р° РІ СЃРёСЃС‚РµРјРµ СѓС‡РµС‚Р° WebMoney
        '123'  # РєРѕРґ РїСЂРѕС‚РµРєС†РёРё СЃРґРµР»РєРё
    );

    print_r($res->toObject());
