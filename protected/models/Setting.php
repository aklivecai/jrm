<?php
class Setting extends CActiveRecord {
    public static $table = '{{setting}}';
    public function tableName() {
        $m = get_class($this);
        return $m::$table;
    }
    public function init() {
        parent::init();
        $this->manageid = Tak::getManageid();
        $this->itemid = Tak::fastUuid();
    }
    public function rules() {
        return array(
            array(
                'itemid, manageid, item_value',
                'required'
            ) ,
            array(
                'itemid, manageid',
                'length',
                'max' => 25
            ) ,
            array(
                'item_key',
                'length',
                'max' => 100
            ) ,
            array(
                'itemid, manageid, item_key, item_value',
                'safe',
                'on' => 'search'
            ) ,
        );
    }
    public function attributeLabels() {
        return array(
            'itemid' => '编号',
            'manageid' => '会员ID',
            'item_key' => '键',
            'item_value' => '值',
        );
    }
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('itemid', $this->itemid);
        $criteria->compare('manageid', $this->manageid);
        $criteria->compare('item_key', $this->item_key, true);
        $criteria->compare('item_value', $this->item_value, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    public function scopes() {
        return array(
            'published' => array(
                'condition' => 'manageid=' . Tak::getManageid() ,
            ) ,
            'public' => array(
                'condition' => 'manageid=' . $this->manageid,
            ) ,
        );
    }
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function getSetings($key) {
        Yii::app()->user->getState('last_login_time', Tak::timetodate($user->last_login_time));
    }
    
    public function takSave() {
        $this->deleteAll(" manageid=:manageid AND item_key=:item_key", array(
            ':manageid' => $this->manageid,
            ":item_key" => $this->item_key
        ));
        return parent::save();
    }
    public function getThemes() {
        $sql = "item_key LIKE 'themeSettings_%'";
        $list = $this->published()->findAll($sql);
        return $list;
    }
    
    public static function getListByName($key, $manageid = false) {
        $sql = "SELECT item_key,item_value FROM :tableName WHERE manageid=:manageid AND  item_key LIKE :name";
        $sql = strtr($sql, array(
            ':tableName' => self::$table,
        ));
        if ($manageid > 0) {
            $uid = $manageid;
        } else {
            $uid = Tak::getManageid();
        }
        $command = Tak::getDb('db')->createCommand($sql);
        $dataReader = $command->queryAll(true, array(
            ':name' => "$key%",
            ':manageid' => $uid,
        ));
        $tags = array();
        foreach ($dataReader as $row) {
            $tags[$row['item_key']] = $row['item_value'];
        }
        return $tags;
    }
    public static function getStocks($manageid = false) {
        $list = self::getListByName('stocks_', $manageid);
        return $list;
    }
    public static function setStocks($arr, $manageid = false) {
    }
}
