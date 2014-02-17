<?php  
class Ak {  
    public static function KD($msg,$isexit=false){
        if (!YII_DEBUG) {
            $debug=" class='hide' style='display:none;'";
        }else{
            $debug='';
        }
        if (is_object($msg)||is_array($msg)){
            echo  "<pre '$debug'>";
            print_r($msg);
            echo  "</pre>";
        }elseif(is_array($msg)){
                foreach ($msg as $key => $value) {
                    self::KD($value);
                }
        }else{ 
            $str = $msg;
            // $str = mb_convert_encoding($str,'gbk','UTF-8');
            echo "<h1 $debug>$str</h1>";
        }
        if ($isexit) exit;
    }    
    public static function checkSuperuser(){
        return Yii::app()->user->checkAccess(self::getSuper());
    }
    public static function getSuper(){
        $result = 'Admin';
        if (isset(Yii::app()->modules['rights'])) {
            $result = Rights::module()->superuserName;
        }
        return $result;
    }
    public static function checkAccess($operation,$params=array(),$allowCaching=true){
        return Yii::app()->user->checkAccess($operation,$params);
     }
    public static function isGuest()
    {
        return Yii::app()->user->isGuest;
    }     
    public static function logout(){
        Yii::app()->user->logout();
    }
    public static function loginUrl()
    {
        return Yii::app()->createUrl(Yii::app()->user->loginUrl[0]);
    }     
#end  
    public static function end($status=0,$exit=true){
        Yii::app()->end($status,$exit);
    }
    public static function getQuery($name,$defaultValue=null)
    {
        return Yii::app()->request->getQuery($name,$defaultValue);
    }
    public static function getPost($name,$defaultValue=null){
      return Yii::app()->request->getPost($name,$defaultValue);  
    }
    public static function getParam($name,$defaultValue=null)
    {
        return Yii::app()->request->getParam($name,$defaultValue);
    }    

    public static function getState($name,$defaultValue=null)
    {
        return Yii::app()->user->getState($name,$defaultValue);
    }
    public function setState($name,$value)
    {
        Yii::app()->user->setState($name,$value);
    } 
       
    public static function getAdmin(){
        return self::getFormid()==1&&self::getManame()=='admin';
    }

    public static function getManame(){
        $result = -1;
        if (isset(Yii::app()->user->name)) {
            $result = Yii::app()->user->name;
        }
        return  $result;
    }
    public static function getManageid(){
        $result = 0;
        if (isset(Yii::app()->user->id)) {
            $result = Yii::app()->user->id;
        }
        return  $result;
    }
    public static function getFormid(){
        $result = -1;
        if (isset(Yii::app()->user->fromid)) {
            $result = Yii::app()->user->fromid;
        }
        return  $result;
    }
    public static function formatNumber($num){
        $result = number_format($num,0,'',',');
        return $result;
    }

    //随机数
   public static function createCode($codelen=4) {
        $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789'; //随机因子
        $_len = strlen($charset)-1;
        $code = '';
        for ($i=0;$i<$codelen;$i++) {
                $code .= $charset[mt_rand(0,$_len)];
        }
        return $code;
    }    
    
    // 会员传入就不做修改
    public static function setCryptNum($str,$rand=false){
        $result = is_numeric($str);
        if ($result) {
            $result = base_convert($str, 10, 36);
                $arr = str_split($result);
                $length = count($arr);
                if ($rand) {
                   $str1 = $rand;
                }else{
                    $str1 = self::createCode(1);
                }
                
                $strb = $arr[$length-1];
                $arr[$length] = $strb;
                $arr[$length-1] = $str1;
            $result = join($arr);
            $result = strtoupper($result);
        }
        return $result;
    }

    public static function getCryptNum($str,$rand=1){
        // $result = !is_numeric($str)?strtolower($str):false;
        $result = strtolower($str);
        if ($result) {
            // self::KD($result);
                $arr = str_split($result);
                $length = count($arr)-1;
                $_length =$length-strlen($rand);
                $arr[$_length] = $arr[$length];
                for ($i=$_length+1; $i <= $length ; $i++) { 
                    unset($arr[$i]);
                }
                
                $result = join($arr);
            // self::KD($result);
            $result = base_convert($result, 36, 10);
            if (!is_numeric($result)) {
                $result = false;;
            }
            // self::KD($result,1);
        }
        return $result;
    }

    public static function getRandTime(){
     //新时间截定义,基于世界未日2012-12-21的时间戳。 
        $endtime=1356019200;//2012-12-21时间戳 
        $curtime=time();//当前时间戳 
        $newtime=$curtime-$endtime;//新时间戳 
        $rand=rand(0,99);//两位随机 
        $all=$rand.$newtime; 
        return $all;
    }

    /*加密数字*/
    public static function setCryptKey($str){
        $key = new TakCrypt();
        return $key->encode($str);

    }
    /*解密数字*/
    public static function getCryptKey($str){
        $key = new TakCrypt();
        return $key->decode($str);
    }
    /*日期显示*/
    public static function timetodate($time = 0, $type = 0) {
        if(!$time) return '';
        $types = array('Y-m-d', 'Y', 'm-d', 'Y-m-d', 'm-d H:i', 'Y-m-d H:i', 'Y-m-d H:i:s');
        if(isset($types[$type])) $type = $types[$type];
        return date($type, $time);
    }
    public static function format_price($val="0.00",$currency="￥",$ifval=false){
        $result =  Yii::app()->numberFormatter->formatCurrency($val,$currency);
        return $result;
    }
    /*获取iP数字*/
    public static function getIps(){
            $ip = self::getip();
            $ip = self::IP2Num($ip);
            return $ip;
    }

    /*获取当前时间*/
    public static function now(){
        return time();
    }

    /*获取时间结束一天*/
    public static function getDayEnd($time=false){
        if (!$time) {
            $time = time();
        }
        $dayEnd = false;
        if (is_numeric($time)) {
            $date = date("Y-m-d",$time);
            $dayEnd = strtotime($date." 23:59:59");
        }           
        return $dayEnd;
    }
    public static function isDayOver($active_time,$day){
        $time = self::now();
        $e1 = mktime(23,59,59,date("m",$active_time),date("d",$active_time)+$day,date("Y",$active_time));
        return $time>$e1;
    }

    public static function getDateTop($key=false){
        $t = time(); 
        $t1 = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t)); 
        $t2 = mktime(0,0,0,date("m",$t),1,date("Y",$t)); 
        $t3 = mktime(0,0,0,date("m",$t)-1,1,date("Y",$t)); 
        $t4 = mktime(0,0,0,1,1,date("Y",$t)); 
        $e1 = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t)); 
        $e2 = mktime(23,59,59,date("m",$t),date("t"),date("Y",$t)); 
        $e3 = mktime(23,59,59,date("m",$t)-1,date("t",$t3),date("Y",$t)); 
        $e4 = mktime(23,59,59,12,31,date("Y",$t)); 
       $datas =array(
        'd'=>array(
            'name' =>'今天',
            'start' => $t1,
            'end' => $e1,
        ),
        'm'=>array(
            'name' =>'这个月',
            'start'=> $t2,
            'end' => $e2,
        ),
        'y'=>array(
            'name' =>'今年',
            'start'=> $t4,
            'end' => $e4,
        ),);  

        if ($key) {
            return isset($datas[$key])?$datas[$key]:false;
        }else{
            return $datas;
        }           

    }

    /*唯一数字*/
    public static function fastUuid($suffix_len=3){
        //! 计算种子数的开始时间
        static $being_timestamp = 1336681180;// 2012-5-10
        $time = explode(' ', microtime());
        $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
        if ($suffix_len > 0)
        {
            $id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
        }
        return $id;
    }   

    /*判断字符串是不是MD5加密的*/
    public static function isValidMd5($md5 ='')
    {
        return  preg_match('/^[a-f0-9]{32}$/', $md5);
    }

    /*网址判断*/
    public static function isUrl($str){
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
            if(strtotime(date('Y-m-d H:i:s',$timestamp)) === $timestamp) {
                $result = $timestamp;
            }
        }
        return $result;
    }         

    /*IP转成无符号数值*/
   public static function IP2Num($ip)
    {
        $result = '';
        if ($ip!='') {
            $result = bindec(decbin(ip2long($ip)));
        }
        return $result;
    }

    /*无符号转成IP地址*/
    public static function Num2IP($num){
        $result = '';
        if ($num!=''&&$num>0) {
            $result = long2ip($num); 
        }
        return $result;
    }      
    public static function getFlash($key='source',$defaultValue=null,$delete=true){
        $result = Yii::app()->user->getFlash($key,$defaultValue,$delete);
        return $result;
    }
    public static function setFlash($value,$key='source',$defaultValue=null){
        $result = Yii::app()->user->setFlash($key,$value,$defaultValue);
        return $result;
    }
    public static function getFlashes($delete=true){
        $result = Yii::app()->user->getFlashes();
        return count($result)>0?$result:false;
    }
    public static function registerMetaTag($content,$name=null,$httpEquiv=null,$options=array(),$id=null){
        Yii::app()->clientScript->registerMetaTag($content,$name,$httpEquiv,$options,$id);
    }
    
    public static function regScriptFile($arrUrl,$pf='base',$path=null,$position=null,array $htmlOptions=array()){
        if (!is_array($arrUrl)) {
            $arrUrl = array($arrUrl);
        }
        switch ($pf) {
            case 'base':
                $pf = yii::app()->theme->baseUrl.'/';                
                break;
            case 'static':
                $pf = Yii::app()->params['staticUrl'];
                break;            
            default:
                # code...
                break;
        }
        if ($path!==null) {
            $pf.= $path.'/';
        }
        foreach ($arrUrl as $url) {            
            if (strpos($url, 'http://')!==false) {      

            }elseif ($pf!='') {
                $url = $pf.$url;
            }
            Yii::app()->clientScript->registerScriptFile($url,$position,$htmlOptions);
        }
    }
    public static function  regScript($id,$script,$position=null,array $htmlOptions=array()){
        Yii::app()->clientScript->registerScript($id,$script,$position,$htmlOptions);
        return $this;
    }

    public function regCssFile($arrUrl,$pf='base',$path=null,$media='')
    {
        if (!is_array($arrUrl)) {
            $arrUrl = array($arrUrl);
        }        
        switch ($pf) {
            case 'base':
                $pf = yii::app()->theme->baseUrl.'/';
                break;
            case 'static':
                $pf = Yii::app()->params['staticUrl'];
                break;            
            default:
                # code...
                break;
        }       
        if ($path!==null) {
            $pf.= $path.'/';
        }
        foreach ($arrUrl as $url) {
            if ($pf!='') {
                $url = $pf.$url;
            }
            Yii::app()->clientScript->registerCssFile($url,$media);
        } 
    }
    public static function searchData($key=false)
    {
       $nowDate = date("Y").'-'.date("m").'-'.date("d");
       $now = self::getDayEnd();
       $datas =array(
        '10'=>array(
            'name' =>'当天',
            'start' => strtotime($nowDate),
            'end' => $now,
        ),
        '20'=>array(
            'name' =>'最近三天',
            'start'=> strtotime("$nowDate -3 day"),
            'end' => $now,
        ),
        '30'=>array(
            'name' =>'最近一周',
            'start'=> strtotime("$nowDate -1 week"),
            'end' => $now,
        ),
        '40'=>array(
            'name' =>'最近半月',
            'start'=> strtotime("$nowDate -15 day"),
            'end' => $now,
        ),
        '50'=>array(
            'name' =>'最近一月',
            'start'=> strtotime("$nowDate -1 month"),
            'end' => $now,
        ),
        '60'=>array(
            'name' =>'最近两月',
            'start'=> strtotime("$nowDate -2 month"),
            'end' => $now,
        ),
        '70'=>array(
            'name' =>'最近三月',
            'start'=> strtotime("$nowDate -3 month"),
            'end' => $now,
        ),
        '80'=>array(
            'name' =>'最近六月',
            'start'=> strtotime("$nowDate -6 month"),
            'end' => $now,
        ),);  

        if ($key) {
            return isset($datas[$key])?$datas[$key]:false;
        }else{
            return $datas;
        }
    }

    public static function getip()
    {
            static $realip = NULL;         
            if ($realip !== NULL){
                return $realip;
            }
            if (isset($_SERVER)){
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                {
                    $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                    /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                    foreach ($arr AS $ip)
                    {
                        $ip = trim($ip);         
                        if ($ip != 'unknown')
                        {
                            $realip = $ip;         
                            break;
                        }
                    }
                }
                elseif (isset($_SERVER['HTTP_CLIENT_IP']))
                {
                    $realip = $_SERVER['HTTP_CLIENT_IP'];
                }
                else
                {
                    if (isset($_SERVER['REMOTE_ADDR']))
                    {
                        $realip = $_SERVER['REMOTE_ADDR'];
                    }
                    else
                    {
                        $realip = '0.0.0.0';
                    }
                }
            }else{
                if (getenv('HTTP_X_FORWARDED_FOR')){
                    $realip = getenv('HTTP_X_FORWARDED_FOR');
                }elseif (getenv('HTTP_CLIENT_IP')){
                    $realip = getenv('HTTP_CLIENT_IP');
                }else{
                    $realip = getenv('REMOTE_ADDR');
                }
            }
         
            preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
            $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
         
            return $realip;   
    }  

    // 输出信息,日期就输出全部
    public static function getDataView($value){
        if (self::isTimestamp($value)) {
            $n = $value%800==0?0:6;
            $value = self::timetodate($value,$n) ;

        }
        return $value;
    }      

    /*
       获取文件路径
        48d4cb4ef423f858a9576a4e75ecd598ae966a1d -- 48/d4/cb/4e/48d4cb4ef423f858a9576a4e75ecd598ae966a1d
    */
    public static function getPathBySplitStr($str) {
        $parts = str_split(substr($str,0,8), 2);
        $path = join("/", $parts);
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
        self::MkDirs(dirname($dir), $mode, $recursive);
        mkdir($dir,$mode);
        return false;
    }
    public static function getUserDir($uid=false){
        $dir = Yii::app()->getBaseUrl().Yii::app()->params['uploadUser'];
        if (!$uid) {
            $uid = self::getFormid();
        }
        $dir .= self::setCryptNum($uid,'JU').'/';
        return $dir;
    }
    public static function getDb($type='db2'){
        return Yii::app()->$type;
    }    
}   