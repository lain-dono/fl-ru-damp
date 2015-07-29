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
require_once 'common.php';
require_once 'models/generic/class.mapperfactory.php';
require_once 'functions.php';

class Operator
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function UpdateOperatorStatus($operator/*, $status, $departments, $locales*/)
    {
        MapperFactory::getMapper('Operator')->updateOperatorTime($operator['operatorid']);
      /*if(empty($locales)) {
          $avail_locales = getAvailableLocalesForChat();

          
          foreach ($avail_locales as $al) {
              $locales[] = $al['localeid']; 
          }
      } 
    
    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    $aOperator = $GLOBALS['mem_buff']->get( 'OPERATOR_ONLINE_FILES_DIR' );
    
    if ( !$aOperator ) {
        $aOperator = array();
    }
    //-------------------------------------------
    
    if (empty($departments)) {
        foreach ($locales as $l) {
            $filename = OPERATOR_ONLINE_FILES_DIR . DIRECTORY_SEPARATOR . 
                $l . DIRECTORY_SEPARATOR . 
                  $operator['operatorid'] . "." . OPERATOR_ONLINE_FILE_EXT;
                      
            if($status == OPERATOR_STATUS_ONLINE) {
                // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------
                //touch_online_file($filename);
                $aOperator[ $filename ] = time();
            } else {
                // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------
                //@unlink($filename);
                unset( $aOperator[$filename] );
            }	
        }
    } else { 
        foreach($departments as $d) {
          
            foreach ($locales as $l) {
                $filename = OPERATOR_ONLINE_FILES_DIR . DIRECTORY_SEPARATOR . 
                    $l . DIRECTORY_SEPARATOR .
                    $d['departmentkey'] . DIRECTORY_SEPARATOR . 
                    $operator['operatorid'] . "." . OPERATOR_ONLINE_FILE_EXT;
                if($status == OPERATOR_STATUS_ONLINE) {
                    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ ----
                      //touch_online_file($filename);
                      $aOperator[ $filename ] = time();
                } else {
                    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ ----
                      //@unlink($filename);
                      unset( $aOperator[$filename] );
                }	
            }   
        }
    }
    
    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    $GLOBALS['mem_buff']->set( 'OPERATOR_ONLINE_FILES_DIR', $aOperator, 3600 );*/
    }

    public function GetOperatorById($id)
    {
        $details = MapperFactory::getMapper('Operator')->getById($id);

        if (empty($details)) {
            return;
        }

        $lastAccess = MapperFactory::getMapper('OperatorLastAccess')->getById($id);

        return is_array($lastAccess) ? array_merge($details, $lastAccess) : $details;
    }

    public function updateOperatorOnlineStats()
    {
        $operator = $this->GetLoggedOperator();
        $this->updateOperatorOnlineTime($operator['operatorid']);
    //MapperFactory::getMapper("OperatorOnline")->updateOperatorOnlineTime($operator['operatorid']);

    if (is_has_threads(HAS_THREADS_FILE) && MapperFactory::getMapper('Thread')->operatorHasActiveThreads($operator['operatorid'])) {
        //MapperFactory::getMapper("OperatorOnline")->updateOperatorOnlineTime($operator['operatorid'],  -2);  
      $this->updateOperatorOnlineTime($operator['operatorid'],  -2);
    }
    }

    public function updateOperatorOnlineStatsForThread($threadid)
    {
        $operator = $this->GetLoggedOperator();
        $this->updateOperatorOnlineTime($operator['operatorid'], $threadid);
        //MapperFactory::getMapper("OperatorOnline")->updateOperatorOnlineTime($operator['operatorid'], $threadid);	
    }

    private function pushOnlineStatsToDB($operatorid)
    {
        $stats = array();
        $filename = self::OperatorOnlineStatsFilename($operatorid);
        if (file_exists($filename)) {
            $data = @file_get_contents($filename);
            if ($data !== false) {
                $tmp = @unserialize($data);
                if ($tmp !== false) {
                    $stats = $tmp;
                }
            }
        }

        if (empty($stats)) {
            return false;
        }

        MapperFactory::getMapper('OperatorOnline')->pushOnlineStatsForOperator($operatorid, $stats);
        @unlink($filename);
    }

    public function loadOnlineStatsIntoDB()
    {
        // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    /*$dh = @opendir(OPERATOR_ONLINE_STATS_FILES_DIR);
    if(!$dh)
      return;
    while($file = readdir($dh)) {
      $ext = pathinfo($file, PATHINFO_EXTENSION);
      if($ext == OPERATOR_ONLINE_STATS_FILE_EXT) {
        $operatorid = substr($file, 0, strlen($file) - strlen($ext) - 1);
        $this->pushOnlineStatsToDB($operatorid);    
      }
    }
    closedir($dh);*/
    $aStats = MapperFactory::getMapper('OperatorOnline')->getAllOperatorsMemStats();
        MapperFactory::getMapper('OperatorOnline')->truncateOperatorMemStats();

        if ($aStats) {
            foreach ($aStats as $aOne) {
                $stats = @unserialize($aOne['stats']);

                if ($stats !== false) {
                    MapperFactory::getMapper('OperatorOnline')->pushOnlineStatsForOperator($aOne['operator_id'], $stats);
                }
            }
        }
    }

    private function updateOperatorOnlineTime($operatorid, $threadid = -1)
    {
        $stats = array();

    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    /*$filename = self::OperatorOnlineStatsFilename($operatorid);
    if(file_exists($filename)) {*/
      //$data = @file_get_contents($filename);
      $data = MapperFactory::getMapper('OperatorOnline')->getOperatorMemStats($operatorid);

        if ($data !== false) {
            $tmp = @unserialize($data);
            if ($tmp !== false) {
                $stats = $tmp;
            }
        }
    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    /*} else {
      create_basedir($filename);
    }*/

    $cur_date = date('Y-m-d');
        if (isset($stats[$threadid], $stats[$threadid][$cur_date])) {
            $delta = time() - $stats[$threadid][$cur_date]['updated'];
            $stats[$threadid][$cur_date]['seconds'] = $stats[$threadid][$cur_date]['seconds'] + ($delta < TIMEOUT_OPERATOR_PING ? $delta : 1);
        } else {
            if (isset($stats[$threadid])) {
                $stats[$threadid] = array();
            }

            $stats[$threadid][$cur_date]['seconds'] = 1;
            $stats[$threadid][$cur_date]['threadid'] = $threadid;
            $stats[$threadid][$cur_date]['date'] = $cur_date;
        }

        $stats[$threadid][$cur_date]['updated'] = time();

    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    //@file_put_contents($filename, serialize($stats), LOCK_EX);
    $data = smarticonv('UTF-8', 'CP1251', serialize($stats));

    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    //if(filesize($filename) > OPERATOR_ONLINE_STATS_FILE_MAX_SIZE) {
    if (strlen($data) > OPERATOR_ONLINE_STATS_FILE_MAX_SIZE) {
        // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
      //$this->pushOnlineStatsToDB($operatorid);
      MapperFactory::getMapper('OperatorOnline')->delOperatorMemStats($operatorid);
        MapperFactory::getMapper('OperatorOnline')->pushOnlineStatsForOperator($operatorid, $stats);
    }
    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    else {
        MapperFactory::getMapper('OperatorOnline')->setOperatorMemStats($operatorid, $data);
    }
    }

    private static function OperatorOnlineStatsFilename($operatorid)
    {
        return OPERATOR_ONLINE_STATS_FILES_DIR.DIRECTORY_SEPARATOR.
      $operatorid.'.'.OPERATOR_ONLINE_STATS_FILE_EXT;
    }

    public function countOnlineOperators($operatorToSkip = null, $departmentkey = null, $locale = null)
    {
        // РїР°РїРєР° online РІ DB ------------------------
    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    //$operators = $this->getOnlineOperatorsFromFiles($operatorToSkip, $departmentkey, $locale);
    //$operators = $this->getOnlineOperatorsFromMemBuff($operatorToSkip, $departmentkey, $locale);
    $operators = $this->getOnlineOperatorsFromDB($operatorToSkip, $departmentkey, $locale);

        return count($operators);
    }

    public function getOnlineOperators($operatorToSkip = null, $departmentkey = null, $locale = null)
    {
        $onlineOperators = array();
    // РїР°РїРєР° online РІ DB ------------------------
    // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    //$operators = $this->getOnlineOperatorsFromFiles($operatorToSkip, $departmentkey, $locale);
    //$operators = $this->getOnlineOperatorsFromMemBuff($operatorToSkip, $departmentkey, $locale);
    $operators = $this->getOnlineOperatorsFromDB($operatorToSkip, $departmentkey, $locale);

        foreach ($operators as $id) {
            $operator = $this->GetOperatorById($id);
            if (!empty($operator)) {
                if (empty($operator['fullname'])) {
                    $operator['fullname'] = $operator['login'];
                }

                $onlineOperators[] = $operator;
            }
        }

        return $onlineOperators;
    }

    /**
     * РћРїРµСЂР°С‚РѕСЂС‹ РѕРЅР»Р°Р№РЅ.
     * 
     * РђРЅР°Р»РѕРі getOnlineOperatorsFromFiles С‚РѕР»СЊРєРѕ РґР»СЏ СЂР°Р±РѕС‚С‹ СЃ Р±Р°Р·РѕР№ РґР°РЅРЅС‹С…
     * 
     * @param int    $operatorIdToSkip РѕРїС†РёРѕРЅР°Р»СЊРЅРѕ. ID РѕРїРµСЂР°С‚РѕСЂР°, РєРѕС‚РѕСЂРѕРіРѕ РЅРµ РЅСѓР¶РЅРѕ РІРєР»СЋС‡Р°С‚СЊ РІ РёС‚РѕРіРѕРѕРІС‹Р№ СЃРїРёСЃРѕРє
     * @param string $departmentkey    РѕРїС†РёРѕРЅР°Р»СЊРЅРѕ. РєР»СЋС‡ РґРµРїР°СЂС‚Р°РјРµРЅС‚Р° РѕРїРµСЂР°С‚РѕСЂР°
     * @param string $locale           РѕРїС†РёРѕРЅР°Р»СЊРЅРѕ. Р»РѕРєР°Р»СЊ (ru, en) РѕРїРµСЂР°С‚РѕСЂР°
     *
     * @return array РјР°СЃСЃРёРІ ID РѕРїРµСЂР°С‚РѕСЂРѕРІ, СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёС… РІС…РѕРґСЏС‰РёРј РїР°СЂР°РјРµС‚СЂР°Рј 
     */
    public function getOnlineOperatorsFromDB($operatorIdToSkip = null, $departmentkey = null, $locale = null)
    {
        $aReturn = array();
        $nTime = time();
        $aOperator = MapperFactory::getMapper('Operator')->getOnlineOperatorsFromDB();

        if ($aOperator) {
            foreach ($aOperator as $aOne) {
                if (!empty($locale)) {
                    $aLocales = empty($aOne['locales']) ? array() : explode(',', $aOne['locales']);

                    if (!$aLocales || !in_array($locale, $aLocales)) {
                        continue;
                    }
                }

                if (!empty($departmentkey) && $aOne['departmentkey'] != $departmentkey) {
                    continue;
                }

                $bExpire = ($nTime - $aOne['operatortime'] > ONLINE_TIMEOUT);

                if (!$bExpire && $aOne['operatorid'] != $operatorIdToSkip) {
                    $aReturn[] = $aOne['operatorid'];
                }
            }
        }

        return array_unique($aReturn);
    }

    /**
     * РћРїРµСЂР°С‚РѕСЂС‹ РѕРЅР»Р°Р№РЅ.
     * 
     * РђРЅР°Р»РѕРі getOnlineOperatorsFromFiles С‚РѕР»СЊРєРѕ РґР»СЏ СЂР°Р±РѕС‚С‹ СЃ РјСЌРјРєСЌС€
     * 
     * @param int    $operatorIdToSkip РѕРїС†РёРѕРЅР°Р»СЊРЅРѕ. ID РѕРїРµСЂР°С‚РѕСЂР°, РєРѕС‚РѕСЂРѕРіРѕ РЅРµ РЅСѓР¶РЅРѕ РІРєР»СЋС‡Р°С‚СЊ РІ РёС‚РѕРіРѕРѕРІС‹Р№ СЃРїРёСЃРѕРє
     * @param string $departmentkey    РѕРїС†РёРѕРЅР°Р»СЊРЅРѕ. РєР»СЋС‡ РґРµРїР°СЂС‚Р°РјРµРЅС‚Р° РѕРїРµСЂР°С‚РѕСЂР°
     * @param string $locale           РѕРїС†РёРѕРЅР°Р»СЊРЅРѕ. Р»РѕРєР°Р»СЊ (ru, en) РѕРїРµСЂР°С‚РѕСЂР°
     *
     * @return array РјР°СЃСЃРёРІ ID РѕРїРµСЂР°С‚РѕСЂРѕРІ, СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёС… РІС…РѕРґСЏС‰РёРј РїР°СЂР°РјРµС‚СЂР°Рј 
     */
    public function getOnlineOperatorsFromMemBuff($operatorIdToSkip = null, $departmentkey = null, $locale = null)
    {
        $aReturn = array();
        $nTime = time();
        $aOperator = $GLOBALS['mem_buff']->get('OPERATOR_ONLINE_FILES_DIR');

        if (!$aOperator) {
            $aOperator = array();
        }

        if ($aOperator) {
            foreach ($aOperator as $sKey => $nStamp) {
                $sPath = preg_replace('#^'.OPERATOR_ONLINE_FILES_DIR.'#', '', $sKey);
                $aPath = explode(DIRECTORY_SEPARATOR, $sPath);

                if (!empty($locale) && $aPath[1] != $locale) {
                    continue;
                }

                if (!empty($departmentkey) && $aPath[2] != $departmentkey) {
                    continue;
                }

                if (count($aPath) == 3) {
                    $sId = $this->_processOnlineMemBuff($nTime, $operatorIdToSkip, $aPath[2], $nStamp, $bExpire);

                    if ($sId) {
                        $aReturn[] = $sId;
                    } elseif ($bExpire) {
                        unset($aOperator[$sKey]);
                    }
                } elseif ($departmentkey !== false) {
                    $sId = $this->_processOnlineMemBuff($nTime, $operatorIdToSkip, $aPath[3], $nStamp, $bExpire);

                    if ($sId) {
                        $aReturn[] = $sId;
                    } elseif ($bExpire) {
                        unset($aOperator[$sKey]);
                    }
                }
            }
        }

        $GLOBALS['mem_buff']->set('OPERATOR_ONLINE_FILES_DIR', $aOperator, 3600);

        return array_unique($aReturn);
    }

    /**
     * РћРїРµСЂР°С‚РѕСЂ РѕРЅР»Р°Р№РЅ.
     * 
     * РђРЅР°Р»РѕРі processOnlineFile С‚РѕР»СЊРєРѕ РґР»СЏ СЂР°Р±РѕС‚С‹ СЃ РјСЌРјРєСЌС€
     * 
     * @param int    $time                С‚РµРєСѓС‰Р°СЏ РјРµС‚РєР° РІСЂРµРјРµРЅРё
     * @param int    $operator_id_to_skip ID РѕРїРµСЂР°С‚РѕСЂР°, РєРѕС‚РѕСЂРѕРіРѕ РЅРµ РЅСѓР¶РЅРѕ РІРєР»СЋС‡Р°С‚СЊ РІ РёС‚РѕРіРѕРѕРІС‹Р№ СЃРїРёСЃРѕРє
     * @param string $file                "РёРјСЏ С„Р°Р№Р»Р°"
     * @param string $file_time           РјРµС‚РєР° РІСЂРµРјРµРЅРё РёР· РјСЌРјРєСЌС€
     * @param bool   $expire              РІРѕР·РІСЂР°С‰Р°РµС‚ true, РµСЃР»Рё РѕРїРµСЂР°С‚РѕСЂ РѕС„С„Р»Р°Р№РЅ
     *
     * @return int ID РѕРїРµСЂР°С‚РѕСЂР°, РµСЃР»Рё РѕРЅ РѕРЅР»Р°Р№РЅ Рё РµРіРѕ РЅРµ РЅСѓР¶РЅРѕ РїСЂРѕРїСѓСЃРєР°С‚СЊ, РёР»Рё 0
     */
    private function _processOnlineMemBuff($time, $operator_id_to_skip, $file, $file_time, &$expire)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $id = 0;

        if ($ext == OPERATOR_ONLINE_FILE_EXT) {
            $id = substr($file, 0, strlen($file) - strlen($ext) - 1);
            $expire = ($time - $file_time > ONLINE_TIMEOUT);

            if ($expire || $id == $operator_id_to_skip) {
                $id = 0;
            }
        }

        return $id;
    }

    public function getOnlineOperatorsFromFiles($operatorIdToSkip = null, $departmentkey = null, $locale = null)
    {
        $dir_name = OPERATOR_ONLINE_FILES_DIR;
        create_dir($dir_name);
        $dh = opendir($dir_name);
        $time = time();
        $result = array();

        if (!$dh) {
            return $result;
        }

        while (($file = readdir($dh)) !== false) {
            if (
        (!empty($locale) && $file != $locale) ||
        $file == '.' ||
        $file == '..' ||
        !is_dir($dir_name.DIRECTORY_SEPARATOR.$file)
      ) {
                continue;
            }
            $ldir_name = $dir_name.DIRECTORY_SEPARATOR.$file;
            $ldh = opendir($ldir_name);
            if (!$ldh) {
                continue;
            }

            while (($lfile = readdir($ldh)) !== false) {
                if (
          (!empty($departmentkey) && $lfile != $departmentkey) ||
          $lfile == '.' ||
          $lfile == '..'
         ) {
                    continue;
                }

                if (!is_dir($ldir_name.DIRECTORY_SEPARATOR.$lfile)) {
                    $id = $this->processOnlineFile($time, $operatorIdToSkip, $ldir_name, $lfile);
                    if ($id !== false) {
                        $result[] = $id;
                    }
                } elseif ($departmentkey !== false) {
                    $ddir_name = $ldir_name.DIRECTORY_SEPARATOR.$lfile;
                    $ddh = opendir($ddir_name);

                    if (!$ddh) {
                        continue;
                    }

                    while (($dfile = readdir($ddh)) !== false) {
                        $id = $this->processOnlineFile($time, $operatorIdToSkip, $ddir_name, $dfile);
                        if ($id !== false) {
                            $result[] = $id;
                        }
                    }
                    closedir($ddh);
                }
            }
            closedir($ldh);
        }
        closedir($dh);

        return array_unique($result);
    }

    private function processOnlineFile($time, $operator_id_to_skip, $dir_name, $file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($ext == OPERATOR_ONLINE_FILE_EXT) {
            $mtime = get_modified_time($dir_name.DIRECTORY_SEPARATOR.$file);
            $id = substr($file, 0, strlen($file) - strlen($ext) - 1);
            if (($time - $mtime) > ONLINE_TIMEOUT) {
                unlink($dir_name.DIRECTORY_SEPARATOR.$file);
            } else {
                if ($id !== $operator_id_to_skip) {
                    return $id;
                }
            }
        }

        return false;
    }

    public function GetAllAccessedOperators()
    {
        // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
    //$accesses = $this->getOnlineOperatorsFromFiles();
    $accesses = $this->getOnlineOperatorsFromMemBuff();
        $res = array();
        foreach ($accesses as $a) {
            $op = $this->GetOperatorById($a);

            if (!empty($op)) {
                $res[] = $op;
            }
        }

        return $res; // TODO: sort
    }

    public function UpdateOperator($id, $data)
    {
        $data['operatorid'] = $id;
        MapperFactory::getMapper('Operator')->save($data);
    }

    public function RefreshSessionOperator()
    {
        unset($_SESSION['operator_departments_keys']);
        unset($_SESSION['operator_departments_keys_operator_id']);
        unset($_SESSION['operator_locales']);
        unset($_SESSION['operator_locales_operator_id']);

        $op = $this->GetLoggedOperator(false);

        if (isset($op)) {
            $this->setOperatorToSessionById($op['operatorid']);
        }
    }

    private function setOperatorToSessionById($operatorId)
    {
        $_SESSION['operator'] = MapperFactory::getMapper('Operator')->getById($operatorId);
    }

    public function getLoggedOperatorDepartmentsKeys()
    {
        $op = $this->GetLoggedOperator();

        if (!isset($_SESSION['operator_departments_keys'])
      || !isset($_SESSION['operator_departments_keys_operator_id'])
      || $op['operatorid'] != $_SESSION['operator_departments_keys_operator_id']) {
            $_SESSION['operator_departments_keys'] = MapperFactory::getMapper('OperatorDepartment')->enumDepartmentKeysByOperator($op['operatorid']);
            $_SESSION['operator_departments_keys_operator_id'] = $op['operatorid'];
        }

        return $_SESSION['operator_departments_keys'];
    }

    public function getLoggedOperatorLocales()
    {
        $op = $this->GetLoggedOperator();
        if (!isset($_SESSION['operator_locales'])
      || !isset($_SESSION['operator_locales_operator_id'])
      || $_SESSION['operator_locales_operator_id'] != $op['operatorid']) {
            $data = MapperFactory::getMapper('OperatorLastAccess')->getById($op['operatorid']);
            $_SESSION['operator_locales'] = empty($data['locales']) ? array() : explode(',', $data['locales']);
            $_SESSION['operator_locales_operator_id'] = $op['operatorid'];
        }

        return $_SESSION['operator_locales'];
    }

    public function getOnlineOperatorsWithDepartments($operatorIdToSkip, $locale)
    { // TODO check for fullname
    $res['']['operators'] = $this->getOnlineOperators($operatorIdToSkip, false);
        $departments = MapperFactory::getMapper('Department')->enumDepartments($locale);

        foreach ($departments as $d) {
            $ops = $this->getOnlineOperators($operatorIdToSkip, $d['departmentkey']);

            foreach ($ops as $op) {
                $res[$d['departmentid']]['operators'][] = $op;
                $res[$d['departmentid']]['departmentname'] = $d['departmentname'];
            }
        }

        return $res;
    }

    public function isCurrentUserAdmin()
    {
        $op = $this->GetLoggedOperator();

        return $op['role'] == 'admin';
    }

    public function IsCurrentUserAdminOrRedirect()
    { // TODO redirect to access denied page
    if (!$this->isCurrentUserAdmin()) {
        die('access denied');
    }
    }

    public function GetLoggedOperator($redirect = true)
    {
        if (!isset($_SESSION['operator'])) {
            if (isset($_COOKIE['WEBIM_AUTH'])) {
                list($login, $pwd) = split(',', $_COOKIE['WEBIM_AUTH'], 2);
                $op = MapperFactory::getMapper('Operator')->getByLogin($login);

                if ($op && isset($pwd) && isset($op['password']) && md5($op['password']) == $pwd) {
                    $this->setOperatorToSessionById($op['operatorid']);

                    return $op;
                }
            }

            if ($redirect) {
                header('Location: '.WEBIM_ROOT.'/operator/login.php?redir='.urlencode($_SERVER['PHP_SELF']));
                exit;
            } else {
                Browser::SendXmlHeaders();
                Browser::displayAjaxError('agent.not_logged_in');
                exit;
            }
        }

        return SilentGetOperator();
    }

    public function DoLogin($login, $password, $remember = false)
    {
        $op = MapperFactory::getMapper('Operator')->getByLoginAndPassword($login, $password);

        if ($op) {
            $_SESSION['operator'] = $op;
            if ($remember) {
                $value = $op['login'].','.md5($op['password']);
                setcookie('WEBIM_AUTH', $value, time() + 60 * 60 * 24 * 1000, WEBIM_ROOT.'/');
            } else {
                if (isset($_COOKIE['WEBIM_AUTH'])) {
                    setcookie('WEBIM_AUTH', '', time() - 3600, WEBIM_ROOT.'/');
                }
            }

            return;
        } else {
            return Resources::Get('page_login.error');
        }
    }

    public function GetOperatorByLogin($login)
    {
        return MapperFactory::getMapper('Operator')->getByLogin($login);
    }

    private function generateToken($length = 16)
    {

    // start with a blank password
    $password = '';

    // define possible characters
    $possible = '0123456789bcdfghjkmnpqrstvwxyz';

    // set up a counter
    $i = 0;

    // add random characters to $password until $length is reached
    mt_srand();
        while ($i < $length) {
            // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

      // we don't want this character if it's already in the password
//      if (!strstr($password, $char)) {
        $password .= $char;
            ++$i;
//      }
        }

    // done!
    return $password;
    }

    public function SendRecoverPasswordMail($operatorid, $host)
    {
        $op = MapperFactory::getMapper('Operator')->getById($operatorid);

        $hash['recoverytoken'] = $this->generateToken(16);
        $hash['recoverytime'] = null; // MySQL auto update
    $hash['operatorid'] = $operatorid;
        MapperFactory::getMapper('Operator')->save($hash);

        $link = HTTP_PREFIX.$host.WEBIM_ROOT.'/operator/recover_password.php?act=recover&login='.$op['login'].'&token='.$hash['recoverytoken'];

        $subject = Resources::Get('mail.password_recover.subject', Resources::getCurrentLocale());
        $body = Resources::Get('mail.password_recover.body', array($op['fullname'], $link), Resources::getCurrentLocale());

        webim_mail($op['fullname'].'<'.$op['email'].'>', Settings::Get('from_email'), $subject, $body); // TODO send link
    }

    public function Logout()
    {
        unset($_SESSION['operator']);
        unset($_SESSION['operator_departments_keys']);
        unset($_SESSION['operator_locales']);
        if (isset($_COOKIE['WEBIM_AUTH'])) {
            setcookie('WEBIM_AUTH', '', time() - 3600, WEBIM_ROOT.'/');
        }
    }

    public function GetName($operator)
    {
        return $operator['fullname'];
    }

    public function DeleteOperator($id)
    {
        MapperFactory::getMapper('Operator')->delete($id);

        MapperFactory::getMapper('OperatorLastAccess')->delete($id);
        MapperFactory::getMapper('OperatorDepartment')->deleteByOperatorId($id);
    }

    public function UploadOperatorAvatar($operatorid, $requestFile)
    {
        $dir = '../images/avatar/';

        return uploadFile($requestFile, $dir, $operatorid);
    }

    public function getAvatarURL($operatorid, $requestFile)
    {
        $ext = strtolower(pathinfo($requestFile, PATHINFO_EXTENSION));

        return WEBIM_ROOT.'/images/avatar/'.$operatorid.'.'.$ext;
    }

    public function setOperatorDepartments($operatorid, $departments)
    {
        MapperFactory::getMapper('OperatorDepartment')->deleteByOperatorId($operatorid);
        $hash = array('operatorid' => $operatorid);
        foreach ($departments as $departmentid) {
            $hash['departmentid'] = $departmentid;
            MapperFactory::getMapper('OperatorDepartment')->save($hash);
        }
    }

    public function enumOperatorsWithOnlineStatus()
    {
        $ids = MapperFactory::getMapper('OperatorLastAccess')->getOnlineOperatorIds();
        $operators = MapperFactory::getMapper('Operator')->getAll();

        $res = array();
        foreach ($operators as $o) {
            $o['isonline'] = in_array($o['operatorid'], $ids);
            $res[] = $o;
        }

        return $res;
    }

    public function enumAvailableDepartmentsForOperator($operatorid, $locale)
    {
        $departmentsExist = MapperFactory::getMapper('Department')->departmentsExist();

        return MapperFactory::getMapper('OperatorDepartment')->enumAvailableDepartmentsForOperator($operatorid, Resources::getCurrentLocale(), $departmentsExist);
    }

    public function hasOnlineOperators($departmentkey = null, $locale = null)
    {
        return $this->countOnlineOperators(null, $departmentkey, $locale) > 0;
    }

    public function hasViewTrackerOperators()
    {
        // РїР°РїРєР° online РІ РјСЌРјРєСЌС€ --------------------
      //$time = get_modified_time(OPERATOR_VIEW_TRACKER_FILE);
      if (($time = $GLOBALS['mem_buff']->get('OPERATOR_VIEW_TRACKER_FILE')) === false) {
          return false;
      }

        if ($time < 0) {
            return false;
        }

        return time() - $time < ONLINE_TIMEOUT;
    }

    public function isOperatorsLimitExceeded()
    {
        return count(MapperFactory::getMapper('Operator')->getAll()) > 50;
    }

    public function ensureOperatorsAreInLastAccess()
    {
        $ids = MapperFactory::getMapper('OperatorOnline')->enumAllAccessedOperatorsWithoutLastAccess();

        foreach ($ids as $id) {
            MapperFactory::getMapper('OperatorLastAccess')->save(array('operatorid' => $id['operatorid'], 'lastvisited' => null));
        }
    }
}

?>
