<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X10
    $res = $wmxi->X10(
        ANOTHER_WMID,  # WM-РёРґРµРЅС‚РёС„РёРєР°С‚РѕСЂ, РєРѕС‚РѕСЂРѕРјСѓ Р±С‹Р» РІС‹РїРёСЃР°РЅ СЃС‡РµС‚(СЃС‡РµС‚Р°) РЅР° РѕРїР»Р°С‚Сѓ
        0,             # РЅРѕРјРµСЂ СЃС‡РµС‚Р° (РІ СЃРёСЃС‚РµРјРµ WebMoney)
        DATE_A,        # РјРёРЅРёРјР°Р»СЊРЅРѕРµ РІСЂРµРјСЏ Рё РґР°С‚Р° СЃРѕР·РґР°РЅРёСЏ СЃС‡РµС‚Р°
        DATE_B         # РјР°РєСЃРёРјР°Р»СЊРЅРѕРµ РІСЂРµРјСЏ Рё РґР°С‚Р° СЃРѕР·РґР°РЅРёСЏ СЃС‡РµС‚Р°
    );

    print_r($res->toObject());
