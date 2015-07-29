<?php

class page_admin_flash_upload extends page_base
{
    public function getFileValue(&$str, $dir)
    {
        $pic = false;
        if ($str != '') {
            $b_file = new CFile('temp/'.$str);
            if ($b_file->id > 0) {
                $b_file->Rename($dir.$str);
                $pic = $str;
            } else {
                $b_file2 = new CFile($dir.$str);
                if ($b_file2->id > 0) {
                    $pic = $str;
                } else {
                    $pic = false;
                }
            }
        }

        return $pic;
    }
    public function testAction()
    {
        die('OK');
    }
    public function openFileAction()
    {
        $b_file = new CFile('temp/'.$this->uri[0]);
        if ($b_file->id > 0) {
            header('Location: '.WDCPREFIX.'/temp/'.$this->uri[0]);
            exit();
        } else {
            $b_file2 = new CFile(front::$_req['altDir'].$this->uri[0]);
            if ($b_file2->id > 0) {
                header('Location: '.WDCPREFIX.'/'.front::$_req['altDir'].$this->uri[0]);
                exit();
            }
        }
    }

    public function saveFileAction()
    {
        if (!($img = $_FILES['Filedata'])) {
            print(json_encode(array('success' => false, 'text' => 'РќРµ РїРѕР»СѓС‡РµРЅС‹ РґР°РЅРЅС‹Рµ', 'debug' => $_FILES['Filedata'])));

            return false;
        }

        if (front::$_req['type'] == 'img') {
            list($width, $height, $type, $attr) = getimagesize($img['tmp_name']);
            $ext = array('gif', 'jpeg', 'png');
            $ext_format = array('gif' => 1, 'jpeg' => 2,
                'png' => 3, );

            if ($type < 1 || $type > 3) {
                print(json_encode(array('success' => false, 'text' => 'Р¤РѕСЂРјР°С‚ РЅРµ РїРѕРґРґРµСЂР¶РёРІР°РµС‚СЃСЏ', 'debug' => $_FILES['Filedata'])));

                return false;
            }

            if (
                (front::$_req['width'] > 0 && $width > front::$_req['width'])
                || (front::$_req['height'] > 0 && $height > front::$_req['height'])
                ) {
                print(json_encode(array('success' => false, 'text' => 'РЎР»РёС€РєРѕРј Р±РѕР»СЊС€РѕРµ СЂР°Р·СЂРµС€РµРЅРёРµ', 'debug' => $_FILES['Filedata'])));

                return false;
            }
        }

        $b_file = new CFile($_FILES['Filedata']);
        $b_file->max_size = 1048576;
        $b_file->server_root = 1;
        $f_name = $b_file->MoveUploadedFile('temp/');

        echo json_encode(array('success' => true, 'id' => $f_name, 'path' => WDCPREFIX.'/temp/'.$f_name));
    }
}
