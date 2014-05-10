<?php
$tak_time = time();
class Ak {
    public static function numAdd($n1, $n2) {
        return bcadd($n1, $n2);
    }
    public static function K($msg, $file = 'main.log') {
        if (is_array($msg)) {
            $string = var_export($msg, TRUE); /*输出数组*/
        } else {
            $string = $msg;
        }
        $dir = Yii::getPathOfAlias('webroot');
        
        $fileName = sprintf('%s\log\%s', $dir, $file);
        $file = fopen($fileName, "a");
        fwrite($file, $string);
        fclose($file);
    }
    public static function KD($msg, $exit = false) {
        $data = array(
            ':debug' => '',
            ':data' => '',
        );
        $str = ':data';
        if (PHP_SAPI === 'cli') {
        } else {
            if (!YII_DEBUG) {
                $debug = ' class="hide" style="display:none;"';
                $data[':debug'] = $debug;
            }
        }
        
        if (is_object($msg) || is_array($msg)) {
            $data[':data'] = var_export($msg, true);
            if (PHP_SAPI === 'cli') {
            } else {
                $str = "<pre :debug>:data</pre>";
            }
        } elseif (is_array($msg)) {
            foreach ($msg as $key => $value) {
                self::KD($value);
            }
        } else {
            $data[':data'] = $msg;
            /* $str = mb_convert_encoding($str,'gbk','UTF-8');*/
            if (PHP_SAPI === 'cli') {
                $str = "\n:data\n";
            } else {
                $str = '<h1 :debug>:data</h1>';
            }
        }
        $str = strtr($str, $data);
        echo $str;
        if ($exit > 0) exit;
    }
    /*
        trace: 这是在 Yii::trace 中使用的级别。它用于在开发中 跟踪程序的执行流程。
        info: 这个用于记录普通的信息。
        profile: 这个是性能概述（profile）。下面马上会有更详细的说明。
        warning: 这个用于警告（warning）信息。
        error: 这个用于致命错误（fatal error）信息。
    */
    public static function log($title, $type = 'info', $note = '') {
        Yii::log($title, $type, $note);
    }
    public static function checkSuperuser() {
        return Yii::app()->user->checkAccess(self::getSuper());
    }
    public static function getSuper() {
        $result = 'Admin';
        if (isset(Yii::app()->modules['rights'])) {
            $result = Rights::module()->superuserName;
        }
        return $result;
    }
    public static function checkAccess($operation, $params = array() , $allowCaching = true) {
        return Yii::app()->user->checkAccess($operation, $params);
    }
    public static function isGuest() {
        return Yii::app()->user->isGuest;
    }
    public static function logout() {
        Yii::app()->user->logout();
    }
    public static function loginUrl() {
        return Yii::app()->createUrl(Yii::app()->user->loginUrl[0]);
    }
    #end
    public static function end($status = 0, $exit = true) {
        Yii::app()->end($status, $exit);
    }
    
    public static function getF($id) {
        $result = sprintf("%s-%s", self::getFormid() , $id);
        return $result;
    }
    public static function getFCache($id) {
        $id = self::getF($id);
        $result = Yii::app()->cache->get($id);
        return $result;
    }
    public static function setFCache($id, $value, $expire = 0, $dependency = null) {
        $id = self::getF($id);
        $result = Yii::app()->cache->set($id, $value, $expire, $dependency);
        return $result;
    }
    public static function getU($id) {
        $result = sprintf("%s-%s", self::getManageid() , $id);
        return $result;
    }
    public static function getUCache($id) {
        $id = self::getU($id);
        $result = Yii::app()->cache->get($id);
        return $result;
    }
    public static function deleteUCache($id) {
        $id = self::getU($id);
        $result = Yii::app()->cache->delete($id);
        return $result;
    }
    public static function setUCache($id, $value, $expire = 0, $dependency = null) {
        $id = self::getU($id);
        $result = Yii::app()->cache->set($id, $value, $expire, $dependency);
        return $result;
    }
    public static function getQuery($name, $defaultValue = null) {
        return Yii::app()->request->getQuery($name, $defaultValue);
    }
    public static function getPost($name, $defaultValue = null) {
        return Yii::app()->request->getPost($name, $defaultValue);
    }
    public static function getParam($name, $defaultValue = null) {
        return Yii::app()->request->getParam($name, $defaultValue);
    }
    
    public static function getState($name, $defaultValue = null) {
        return Yii::app()->user->getState($name, $defaultValue);
    }
    public function setState($name, $value) {
        Yii::app()->user->setState($name, $value);
    }
    
    public static function getAdmin() {
        return self::getFormid() == 1 && self::getManame() == 'admin';
    }
    
    public static function getManame() {
        $result = - 1;
        if (isset(Yii::app()->user->name)) {
            $result = Yii::app()->user->name;
        }
        return $result;
    }
    /*获取操作数*/
    public static function getOM() {
        $ip = Yii::app()->user->getState('ip') != '' ? Yii::app()->user->getState('ip') : false;
        if (!$ip) {
            $ip = self::getIps();
            Yii::app()->user->setState('ip', $ip);
        }
        // self::KD($ip);
        // self::KD($ip,1);
        $arr = array(
            'time' => self::now() ,
            'ip' => $ip,
            'itemid' => self::fastUuid() ,
            'manageid' => self::getManageid() ,
            'fromid' => self::getFormid()
        );
        return $arr;
    }
    public static function getManageid() {
        $result = 0;
        if (isset(Yii::app()->user->id)) {
            $result = Yii::app()->user->id;
        }
        return $result;
    }
    public static function getFormid() {
        $result = - 1;
        if (isset(Yii::app()->user->fromid)) {
            $result = Yii::app()->user->fromid;
        }
        return $result;
    }
    public static function formatNumber($num) {
        $result = number_format($num, 0, '', ',');
        return $result;
    }
    //随机数
    public static function createCode($codelen = 4, $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789') {
        //随机因子
        $_len = strlen($charset) - 1;
        $code = '';
        for ($i = 0;$i < $codelen;$i++) {
            $code.= $charset[mt_rand(0, $_len) ];
        }
        return $code;
    }
    // 会员传入就不做修改
    public static function setCryptNum($str, $rand = false) {
        $result = is_numeric($str);
        if ($result) {
            // 你给的字符串是任意的，但是这个函数只认你给定的进制的符号，你给定的是10，所以只认0-9的字符转换为36进制
            $result = base_convert($str, 10, 36);
            $arr = str_split($result);
            $length = count($arr);
            if ($rand) {
                $str1 = $rand;
            } else {
                $str1 = self::createCode(1);
            }
            
            $strb = $arr[$length - 1];
            $arr[$length] = $strb;
            $arr[$length - 1] = $str1;
            $result = join($arr);
            $result = strtoupper($result);
        }
        return $result;
    }
    
    public static function getCryptNum($str, $rand = 1) {
        // $result = !is_numeric($str)?strtolower($str):false;
        $result = strtolower($str);
        if ($result) {
            // self::KD($result);
            $arr = str_split($result);
            $length = count($arr) - 1;
            $_length = $length - strlen($rand);
            $arr[$_length] = $arr[$length];
            for ($i = $_length + 1;$i <= $length;$i++) {
                unset($arr[$i]);
            }
            $result = join($arr);
            // self::KD($result);
            $result = base_convert($result, 36, 10);
            if (!is_numeric($result)) {
                $result = false;;
            }
            /*self::KD($result,1);*/
        }
        return $result;
    }
    
    public static function getRandTime() {
        //新时间截定义,基于世界未日2012-12-21的时间戳。
        $endtime = 1356019200; //2012-12-21时间戳
        $curtime = time(); //当前时间戳
        $newtime = $curtime - $endtime; //新时间戳
        $rand = rand(0, 99); //两位随机
        $all = $rand . $newtime;
        return $all;
    }
    /*加密字符串*/
    public static function encrypt($child) {
        $crypt = new SysCrypt();
        return $crypt->encrypt($child);
    }
    /*解密字符串*/
    public static function decrypt($child) {
        $crypt = new SysCrypt();
        return $crypt->decrypt($child);
    }
    /*加密数字*/
    public static function setCryptKey($str, $time_to_live = 0) {
        $key = new TakCrypt($time_to_live);
        return $key->encode($str);
    }
    /*解密数字*/
    public static function getCryptKey($str) {
        $key = new TakCrypt();
        return $key->decode($str);
    }
    /*日期显示*/
    public static function timetodate($time = 0, $type = 0) {
        if (!$time) return '';
        $types = array(
            'Y-m-d',
            'Y',
            'm-d',
            'Y-m-d',
            'm-d H:i',
            'Y-m-d H:i',
            'Y-m-d H:i:s'
        );
        if (isset($types[$type])) $type = $types[$type];
        return date($type, $time);
    }
    public static function format_price($val = "0.00", $currency = "￥", $ifval = false) {
        $result = Yii::app()->numberFormatter->formatCurrency($val, $currency);
        return $result;
    }
    /*获取iP数字*/
    public static function getIps() {
        $ip = self::getip();
        $ip = self::IP2Num($ip);
        return $ip;
    }
    /*获取当前时间*/
    public static function now() {
        global $tak_time;
        if (!$tak_time) {
            $tak_time = time();
        }
        return $tak_time;
    }
    /*获取时间结束一天*/
    public static function getDayEnd($time = false) {
        if (!$time) {
            $time = time();
        }
        $dayEnd = false;
        if (is_numeric($time)) {
            $date = date("Y-m-d", $time);
            $dayEnd = strtotime($date . " 23:59:59");
        }
        return $dayEnd;
    }
    public static function isDayOver($active_time, $day) {
        $time = self::now();
        $e1 = mktime(23, 59, 59, date("m", $active_time) , date("d", $active_time) + $day, date("Y", $active_time));
        return $time > $e1;
    }
    
    public static function getDateTop($key = false) {
        $t = time();
        $t1 = mktime(0, 0, 0, date("m", $t) , date("d", $t) , date("Y", $t));
        $t2 = mktime(0, 0, 0, date("m", $t) , 1, date("Y", $t));
        $t3 = mktime(0, 0, 0, date("m", $t) - 1, 1, date("Y", $t));
        $t4 = mktime(0, 0, 0, 1, 1, date("Y", $t));
        $e1 = mktime(23, 59, 59, date("m", $t) , date("d", $t) , date("Y", $t));
        $e2 = mktime(23, 59, 59, date("m", $t) , date("t") , date("Y", $t));
        $e3 = mktime(23, 59, 59, date("m", $t) - 1, date("t", $t3) , date("Y", $t));
        $e4 = mktime(23, 59, 59, 12, 31, date("Y", $t));
        $datas = array(
            'd' => array(
                'name' => '今天',
                'start' => $t1,
                'end' => $e1,
            ) ,
            'm' => array(
                'name' => '这个月',
                'start' => $t2,
                'end' => $e2,
            ) ,
            'y' => array(
                'name' => '今年',
                'start' => $t4,
                'end' => $e4,
            ) ,
        );
        
        if ($key) {
            return isset($datas[$key]) ? $datas[$key] : false;
        } else {
            return $datas;
        }
    }
    /*唯一数字*/
    public static function fastUuid($suffix_len = 3) {
        //! 计算种子数的开始时间
        static $being_timestamp = 1336681180; // 2012-5-10
        $time = explode(' ', microtime());
        $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
        if ($suffix_len > 0) {
            $id.= substr(sprintf('%010u', mt_rand()) , 0, $suffix_len);
        }
        return $id;
    }
    /*判断字符串是不是MD5加密的*/
    public static function isValidMd5($md5 = '') {
        return preg_match('/^[a-f0-9]{32}$/', $md5);
    }
    /*网址判断*/
    public static function isUrl($str) {
        return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $str);
    }
    /*判断一个数字是不是 时间数
         return ( 1 === preg_match( '~^[1-9][0-9]*$~', $string ) );
         return if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $timestamp));
         return date("Y") - date("Y", strtotime($date));
         return preg_match('/[^\d]/', $str)&&strtotime($str)
    */
    public static function isTimestamp($timestamp) {
        // self::KD(strtotime(date('Y-m-d H:i:s',$timestamp)));
        $result = false;
        if (is_numeric($timestamp)) {
            $timestamp = (int)$timestamp;
            if (strtotime(date('Y-m-d H:i:s', $timestamp)) === $timestamp) {
                $result = $timestamp;
            }
        }
        return $result;
    }
    /*IP转成无符号数值*/
    public static function IP2Num($ip) {
        $result = '';
        if ($ip != '') {
            $result = bindec(decbin(ip2long($ip)));
        }
        return $result;
    }
    /*无符号转成IP地址*/
    public static function Num2IP($num) {
        $result = '';
        if ($num != '' && $num > 0) {
            $result = long2ip($num);
        }
        return $result;
    }
    public static function getFlash($key = 'source', $defaultValue = null, $delete = true) {
        $result = Yii::app()->user->getFlash($key, $defaultValue, $delete);
        return $result;
    }
    public static function setFlash($value, $key = 'source', $defaultValue = null) {
        $result = Yii::app()->user->setFlash($key, $value, $defaultValue);
        return $result;
    }
    public static function getFlashes($delete = true) {
        $result = Yii::app()->user->getFlashes();
        return count($result) > 0 ? $result : false;
    }
    public static function registerMetaTag($content, $name = null, $httpEquiv = null, $options = array() , $id = null) {
        Yii::app()->clientScript->registerMetaTag($content, $name, $httpEquiv, $options, $id);
    }
    
    public static function regScriptFile($arrUrl, $pf = 'base', $path = null, $position = null, array $htmlOptions = array()) {
        if (!is_array($arrUrl)) {
            $arrUrl = array(
                $arrUrl
            );
        }
        switch ($pf) {
            case 'base':
                $pf = yii::app()->theme->baseUrl . '/';
            break;
            case 'static':
                $pf = Yii::app()->params['staticUrl'];
            break;
            default:
            break;
        }
        if ($path !== null) {
            $pf.= $path . '/';
        }
        foreach ($arrUrl as $url) {
            if (strpos($url, 'http://') !== false) {
            } elseif ($pf != '') {
                $url = $pf . $url;
            }
            Yii::app()->clientScript->registerScriptFile($url, $position, $htmlOptions);
        }
    }
    public static function regScript($id, $script, $position = null, array $htmlOptions = array()) {
        Yii::app()->clientScript->registerScript($id, $script, $position, $htmlOptions);
        return __CLASS__;
    }
    
    public static function regCssFile($arrUrl, $pf = 'base', $path = null, $media = '') {
        if (!is_array($arrUrl)) {
            $arrUrl = array(
                $arrUrl
            );
        }
        switch ($pf) {
            case 'base':
                $pf = yii::app()->theme->baseUrl . '/';
            break;
            case 'static':
                $pf = Yii::app()->params['staticUrl'];
            break;
            default:
            break;
        }
        if ($path !== null) {
            $pf.= $path . '/';
        }
        foreach ($arrUrl as $url) {
            if ($pf != '') {
                $url = $pf . $url;
            }
            Yii::app()->clientScript->registerCssFile($url, $media);
        }
    }
    public static function searchData($key = false) {
        $nowDate = date("Y") . '-' . date("m") . '-' . date("d");
        $now = self::getDayEnd();
        $datas = array(
            '10' => array(
                'name' => '当天',
                'start' => strtotime($nowDate) ,
                'end' => $now,
            ) ,
            '20' => array(
                'name' => '最近三天',
                'start' => strtotime("$nowDate -3 day") ,
                'end' => $now,
            ) ,
            '30' => array(
                'name' => '最近一周',
                'start' => strtotime("$nowDate -1 week") ,
                'end' => $now,
            ) ,
            '40' => array(
                'name' => '最近半月',
                'start' => strtotime("$nowDate -15 day") ,
                'end' => $now,
            ) ,
            '50' => array(
                'name' => '最近一月',
                'start' => strtotime("$nowDate -1 month") ,
                'end' => $now,
            ) ,
            '60' => array(
                'name' => '最近两月',
                'start' => strtotime("$nowDate -2 month") ,
                'end' => $now,
            ) ,
            '70' => array(
                'name' => '最近三月',
                'start' => strtotime("$nowDate -3 month") ,
                'end' => $now,
            ) ,
            '80' => array(
                'name' => '最近六月',
                'start' => strtotime("$nowDate -6 month") ,
                'end' => $now,
            ) ,
        );
        
        if ($key) {
            return isset($datas[$key]) ? $datas[$key] : false;
        } else {
            return $datas;
        }
    }
    
    public static function getip() {
        static $realip = NULL;
        if ($realip !== NULL) {
            return $realip;
        }
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        
        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        
        return $realip;
    }
    // 输出信息,日期就输出全部
    public static function getDataView($value) {
        if (self::isTimestamp($value) && $value > 1000000) {
            $n = $value % 800 == 0 ? 0 : 6;
            $value = self::timetodate($value, $n);
        }
        return $value;
    }
    /*
       获取文件路径
        48d4cb4ef423f858a9576a4e75ecd598ae966a1d -- 48/d4/cb/4e/48d4cb4ef423f858a9576a4e75ecd598ae966a1d
    */
    public static function getPathBySplitStr($str) {
        $parts = str_split(substr($str, 0, 8) , 2);
        $path = implode("/", $parts);
        $path = $path . "/" . $str;
        return $path;
    }
    /*
    生成目录
    */
    public static function MkDirs($dir, $mode = 0700, $recursive = true) {
        if (is_null($dir) || $dir == "") {
            return false;
        }
        if (is_dir($dir) || $dir == "/") {
            return true;
        }
        self::MkDirs(dirname($dir) , $mode, $recursive);
        mkdir($dir, $mode);
        return false;
    }
    public static function getUserDir($uid = false) {
        $dir = Yii::app()->params['uploadUser'];
        if (YII_DEBUG && false) {
            $dir = Yii::app()->getBaseUrl() . $dir;
        }
        if (!$uid) {
            $uid = self::getFormid();
        }
        $dir.= self::setCryptNum($uid, 'JU') . '/';
        return $dir;
    }
    public static function getDb($type = 'db2') {
        return Yii::app()->$type;
    }
    
    public static function category_select($name = 'catid', $title = '', $catid = 0, $moduleid = 1, $extend = '') {
        $option = self::cache_read('catetree-' . $moduleid . '.php', '', true);
        if ($option) {
            if ($catid) $option = str_replace('value="' . $catid . '"', 'value="' . $catid . '" selected', $option);
            $select = '<select name="' . $name . '" ' . $extend . ' id="catid_1">';
            if ($title) $select.= '<option value="0">' . $title . '</option>';
            $select.= $option ? $option : '</select>';
            return $select;
        }
    }
    public static function modulesToJson(array $models, $attributeNames = false) {
        if (count($models) == 0) {
            return '[]';
        }
        if ($attributeNames) {
            $attributeNames = explode(',', $attributeNames);
        } else {
            $m = current($models);
            $attributeNames = array_keys($m->getAttributes());
        }
        
        $rows = array(); //the rows to output
        foreach ($models as $model) {
            $row = array(); //you will be copying in model attribute values to this array
            foreach ($attributeNames as $name) {
                $name = trim($name); //in case of spaces around commas
                $row[$name] = CHtml::value($model, $name); //this function walks the relations
                
                
            }
            $rows[] = $row;
        }
        return CJSON::encode($rows);
    }
    public static function isNumeric($var) {
        $result = false;
        if ((is_numeric($var) && (intval($var) == floatval($var)))) {
            $result = intval($var);
        }
        return $result;
    }
    public static function isPrice($str) {
        // $pattern = '/^\d+(:?[.]\d{1,2})$/';
        $pattern = '/^\d{0,8}\.{0,1}(\d{1,2})?$/';
        $result = true;
        if (preg_match($pattern, $str) == '0' || (strpos($str, '.') !== false && strpos($str, '.') == (strlen($str) - 1))) {
            $result = false;
        }
        return $result;
    }
    
    public static function getMovingsType($type) {
        $types = array(
            1 => 'Purchase',
            2 => 'Sell'
        );
        if (isset($types[$type])) {
            $type = $types[$type];
        } else {
            $type = current($types);
        }
        return $type;
    }
    
    public static function uhtml($str) {
        $farr = array(
            "/\s+/", //过滤多余空白
            //过滤 <script>等可能引入恶意内容或恶意改变显示布局的代码,如果不需要插入flash等,还可以加入<object>的过滤
            "/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU", /*过滤javascript的on事件*/
        );
        $tarr = array(
            " ",
            "＜\1\2\3＞", //如果要直接清除不安全的标签，这里可以留空
            "\1\2",
        );
        $str = preg_replace($farr, $tarr, $str);
        return $str;
    }
    /*检测是否是供应商*/
    public static function getSupplier($supplier = false) {
        $id = $supplier ? $supplier : self::getState('branch');
        return $id == '800';
    }
}
/*
  $id = "922222222222222";
//$id = '131970169159123104640404064868224';

$ids = "131970169159123138416918185882704";
$ids_s = base_convert($ids,10,36);
echo $ids_s ;
echo "\n";
$ids_v = base_convert($ids_s,36,10);
echo $ids_v;
echo "\n";
$str = Ak::setCryptNum($id);
echo $str;
echo "\n";
echo Ak::getCryptNum($str);
*/
// echo Tak::setCryptKey(61741284720117273);


if (isset($_GET['id']) && !is_numeric($id) && strlen($id) >= 30) {
    $_GET['id'] = $_GET['id'];
}
