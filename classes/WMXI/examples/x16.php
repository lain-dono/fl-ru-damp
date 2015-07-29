<?php

    include '_header.php';

    # http://wiki.webmoney.ru/wiki/show/Interfeys_X16
    $res = $wmxi->X16(
        PRIMARY_WMID,   # WMID РєРѕС€РµР»СЊРєР°
        'Z',            # С‚РёРї РєРѕС€РµР»СЊРєР°
        'Р•С‰С‘ РѕРґРёРЅ WMZ'  # РЅР°Р·РІР°РЅРёРµ РєРѕС€РµР»СЊРєР°
    );

    print_r($res->toObject());
