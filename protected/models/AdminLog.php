<?php
/**
 * This is the model class for table "{{Admin_Log}}".
 *
 * The followings are the available columns in table '{{Admin_Log}}':
 * @property long $itemid
 * @property string $fromid
 * @property string $user_name
 * @property string $qstring
 * @property string $info
 * @property string $ip
 * @property string $add_time
 */
class AdminLog extends CActiveRecord {
    
    public static $isLog = true;
    protected $_bycu = true; //搜索自己
    public static $table = '{{admin_log}}';
    
    public function tableName() {
        $m = get_class($this);
        return $m::$table;
    }
    public function rules() {
        return array(
            array(
                'itemid, manageid,fromid, user_name',
                'required'
            ) ,
            array(
                'user_name',
                'length',
                'max' => 60
            ) ,
            array(
                'qstring, info',
                'length',
                'max' => 255
            ) ,
            array(
                'ip',
                'safe'
            ) ,
            array(
                'itemid, fromid, user_name, qstring, info, ip, add_time',
                'safe',
                'on' => 'search'
            ) ,
        );
    }
    
    public function relations() {
        return array();
    }
    
    public function setGetCU($isTure = false) {
        $this->_bycu = $isTure;
        return $this;
    }
    protected function getCu() {
        $result = false;
        if ($this->hasAttribute('manageid') && $this->_bycu && !Tak::checkSuperuser()) {
            $result = true;
        }
        return $result;
    }
    public function attributeLabels() {
        return array(
            'itemid' => '编号',
            'fromid' => '平台会员',
            'manageid' => '操作人编号',
            'user_name' => '操作人',
            'qstring' => '地址',
            'info' => '描述',
            'ip' => 'Ip',
            'add_time' => '时间',
        );
    }
    
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.
        
        $criteria = new CDbCriteria;
        $criteria->compare('itemid', $this->itemid);
        $criteria->compare('fromid', $this->fromid);
        if ($this->manageid > 0) {
            $criteria->compare('manageid', $this->manageid);
        }
        
        $criteria->compare('user_name', $this->user_name, true);
        $criteria->compare('qstring', $this->qstring, true);
        $criteria->compare('info', $this->info, true);
        $criteria->compare('ip', $this->ip, true);
        
        $add_time = $this->add_time;
        if ($add_time) {
            if ($add_time > 0 && Tak::isTimestamp($add_time)) {
                $criteria->compare('add_time', $add_time);
            } elseif ($add_time == 0) {
            } else {
                $start = strtotime($add_time);
                $end = TaK::getDayEnd($start);
                $criteria->addBetweenCondition('add_time', $start, $end);
            }
        }
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function recently($limit = 5, $pcondition = false, $order = 'add_time DESC') {
        $condition = $this->defaultScope(false);
        
        if (is_string($pcondition)) {
            $condition[] = $pcondition;
        } elseif (is_array($pcondition)) {
            $condition = array_merge_recursive($condition, $pcondition);
        }
        $criteria = new CDbCriteria(array(
            'condition' => implode(" AND ", $condition) ,
            'order' => $order
        ));
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $limit,
            ) ,
        ));
    }
    //默认继承的搜索条件
    public function defaultScope() {
        $arr = array();
        if (true) {
            $arr['order'] = 'add_time DESC';
        }
        $condition = array();
        if ($this->hasAttribute('fromid')) {
            $condition[] = 'fromid=' . Tak::getFormid();
        }
        if ($this->getCu() && Tak::getManageid()) {
            $condition[] = 'manageid=' . Tak::getManageid();
        }
        
        $arr['condition'] = implode(" AND ", $condition);
        return $arr;
    }
    //保存日志操作
    public static function log($info = '') {
        if (!self::$isLog) {
            return false;
        }
        $m = new self('create');
        $m->info = $info;
        $m->qstring = Yii::app()->request->getUrl();
        $arr = Tak::getOM();
        $arr['user_name'] = Tak::getManame();
        if (func_num_args() > 1 && is_array(func_get_arg(1))) {
            foreach (func_get_arg(1) as $key => $value) {
                if (isset($arr[$key])) {
                    $arr[$key] = $value;
                }
            }
        }
        $m->fromid = $arr['fromid'];
        $m->manageid = $arr['manageid'];
        $m->user_name = $arr['user_name'];
        $m->itemid = $arr['itemid'];
        $m->add_time = $arr['time'];
        $m->ip = $arr['ip'];
        $m->save();
    }
}
