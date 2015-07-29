<?php
/* 
 * 
 * Р”Р°РЅРЅС‹Р№ С„Р°Р№Р» СЏРІР»СЏРµС‚СЃСЏ С‡Р°СЃС‚СЊСЋ РїСЂРѕРµРєС‚Р° Р’РµР± РњРµСЃСЃРµРЅРґР¶РµСЂ.
 * 
 * Р’СЃРµ РїСЂР°РІР° Р·Р°С‰РёС‰РµРЅС‹. (c) 2005-2009 РћРћРћ "РўРћРџ".
 * Р”Р°РЅРЅРѕРµ РїСЂРѕРіСЂР°РјРјРЅРѕРµ РѕР±РµСЃРїРµС‡РµРЅРёРµ Рё РІСЃРµ СЃРѕРїСѓС‚СЃС‚РІСѓСЋС‰РёРµ РјР°С‚РµСЂРёР°Р»С‹
 * РїСЂРµРґРѕСЃС‚Р°РІР»СЏСЋС‚СЃСЏ РЅР° СѓСЃР»РѕРІРёСЏС… Р»РёС†РµРЅР·РёРё, РґРѕСЃС‚СѓРїРЅРѕР№ РїРѕ Р°РґСЂРµСЃСѓ
 * http://webim.ru/license.html
 * 
 */
?>
<?php 

    class CWebim
    {
        public function BeforeUserAdd(&$arFields)
        {
            if (strstr($_SERVER['HTTP_REFERER'], 'webim')) {
                $filter = array('STRING_ID' => 'webim');
                $by = 'c_sort';
                $order = 'DESC';
                $rsGroups = CGroup::GetList($by, $order, $filter);
                $is_filtered = $rsGroups->is_filtered;

                if ($row = $rsGroups->GetNext()) {
                    $arFields['GROUP_ID'][] = array('GROUP_ID' => $row['ID']);
                }
            }
        }

        public function UserDelete($id)
        {
            Operator::getInstance()->DeleteOperator($id);
        }

        public static function getAvatar($uid = false)
        {
            global $USER;

            if ($uid === false) {
                $uid = $USER->GetID();
            }

            $rsUser = CUser::GetByID($uid);
            $arResult = $rsUser->GetNext(false);
            if (!empty($arResult) && !empty($arResult['PERSONAL_PHOTO'])) {
                $db_img = CFile::GetByID($arResult['PERSONAL_PHOTO']);
                $db_img_arr = $db_img->Fetch();

                if (!empty($db_img_arr)) {
                    $strImageStorePath = COption::GetOptionString('main', 'upload_dir', 'upload');
                    $sImagePath = '/'.$strImageStorePath.'/'.$db_img_arr['SUBDIR'].'/'.$db_img_arr['FILE_NAME'];
                    $sImagePath = str_replace('//', '/', $sImagePath);

                    return $sImagePath;
                }
            }

            return false;
        }
    }

?>