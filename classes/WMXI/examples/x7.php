<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X7
    $res = $wmxi->X7(
        ANOTHER_WMID,  # WM-РёРґРµРЅС‚РёС„РёРєР°С‚РѕСЂ РєР»РёРµРЅС‚Р°, РєРѕС‚РѕСЂРѕРіРѕ РЅРµРѕР±С…РѕРґРёРјРѕ Р°СѓС‚РµРЅС‚РёС„РёС†РёСЂРѕРІР°С‚СЊ
        '00FF',        # РїРѕРґРїРёСЃСЊ СЃС‚СЂРѕРєРё, РїРµСЂРµРґР°РІР°РµРјРѕР№ РІ РїР°СЂР°РјРµС‚СЂРµ testsign\plan, СЃС„РѕСЂРјРёСЂРѕРІР°РЅРЅР°СЏ РєР»РёРµРЅС‚РѕРј, РєРѕС‚РѕСЂРѕРіРѕ РЅРµРѕР±С…РѕРґРёРјРѕ Р°СѓС‚РµРЅС‚РёС„РёС†РёСЂРѕРІР°С‚СЊ
        '123'          # СЃС‚СЂРѕРєР°, РєРѕС‚РѕСЂСѓСЋ РґРѕР»Р¶РµРЅ Р±С‹Р» РїРѕРґРїРёСЃР°С‚СЊ РєР»РёРµРЅС‚
    );

    print_r($res->toObject());
