<?php


    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X3
    $res = $wmxi->X3(
        PRIMARY_PURSE,  # РЅРѕРјРµСЂ РєРѕС€РµР»СЊРєР° РґР»СЏ РєРѕС‚РѕСЂРѕРіРѕ Р·Р°РїСЂР°С€РёРІР°РµС‚СЃСЏ РѕРїРµСЂР°С†РёСЏ
        0,              # РЅРѕРјРµСЂ РѕРїРµСЂР°С†РёРё (РІ СЃРёСЃС‚РµРјРµ WebMoney)
        0,              # РЅРѕРјРµСЂ РїРµСЂРµРІРѕРґР°
        0,              # РЅРѕРјРµСЂ СЃС‡РµС‚Р° (РІ СЃРёСЃС‚РµРјРµ WebMoney) РїРѕ РєРѕС‚РѕСЂРѕРјСѓ РІС‹РїРѕР»РЅСЏР»Р°СЃСЊ РѕРїРµСЂР°С†РёСЏ
        0,              # РЅРѕРјРµСЂ СЃС‡РµС‚Р°
        DATE_A,         # РјРёРЅРёРјР°Р»СЊРЅРѕРµ РІСЂРµРјСЏ Рё РґР°С‚Р° РІС‹РїРѕР»РЅРµРЅРёСЏ РѕРїРµСЂР°С†РёРё
        DATE_B          # РјР°РєСЃРёРјР°Р»СЊРЅРѕРµ РІСЂРµРјСЏ Рё РґР°С‚Р° РІС‹РїРѕР»РЅРµРЅРёСЏ РѕРїРµСЂР°С†РёРё
    );

    print_r($res->Sort());
#	print_r($res->Sort(false));
#	print_r($res->toArray());
#	print_r($res->toObject());
#	print_r($res->toString());
;
