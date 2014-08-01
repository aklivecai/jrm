<?php
class DbiRecod extends CActiveRecord {
    public static $_db = null;
    public static $table = '';
    
    public $mName = ''; /*当前类名字*/
    public $sName = ""; /*显示名字*/
    //是否记录日志
    public $_isLog = false;
    public function tableName() {
        $m = get_class($this);
        return $m::$table;
    }
    public function init() {
        $this->mName = get_class($this);
        $this->sName = Tk::g($this->mName);
    }
    /**
     * 检测企业是否存在数据库配置
     * @return [type] $db
     */
    public function getDbConnection() {
        if (self::$_db !== null) return self::$_db;
        else {
            if ($db = Ak::db()) {
                self::$_db = $db;
                self::$_db->setActive(true);
            } else {
                self::$_db = self::$db;
            }
        }
        return self::$_db;
    }
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    protected function afterSave() {
        parent::afterSave();
        if (!$this->_isLog) {
            return true;
        }
        $url = Yii::app()->request->getUrl();
        if (strpos($url, 'delete') > 0) {
            $this->logDel();
        } elseif (strpos($url, 'del') > 0) {
            AdminLog::log(Tk::g('Deletes') . $this->sName);
        } elseif (strpos($url, 'restore') > 0) {
            AdminLog::log(Tk::g('Restore') . $this->sName);
        } elseif ($this->isNewRecord) {
            AdminLog::log(Tk::g('Create') . $this->sName . ' - 编号:' . $this->primaryKey);
        } else {
            AdminLog::log(Tk::g('Update') . $this->sName);
        }
    }
    protected function getRepetition($attribute) {
        return array();
    }
    /**
     * 检验重复
     */
    public function checkRepetition($attribute, $params) {
        $sql = $this->getRepetition($attribute);
        $sql[] = "LOWER(:col)=:val";
        $arr = array(
            ':col' => $attribute,
        );
        if ($this->primaryKey > 0) {
            $sql[] = ':ikey<>:itemid';
            $arr[':ikey'] = $this->primaryKey();
            $arr[':itemid'] = $this->primaryKey;
        }
        $sql = implode(' AND ', $sql);
        // 查找满足指定条件的结果中的第一行
        $sql = strtr($sql, $arr);
        $m = $this->find($sql, array(
            ':val' => strtolower($this->$attribute)
        ));
        // Tak::KD($m,1);
        $result = true;
        if ($m != null) {
            $err = sprintf('%s <i class="label label-warning">%s</i>   已经存在', $this->getAttributeLabel($attribute) , $this->$attribute);
            $this->addError($attribute, $err);
            $result = false;
        }
        return $result;
    }
    
    public function checkTime($attribute, $params) {
        $time = $this->$attribute;
        if (!$time) {
            $this->$attribute = 0;
            return true;
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $time = Ak::isTimestamp($time);
        if ($time) {
            $this->$attribute = $time;
        } else {
            $this->addError($attribute, sprintf('%s格式错误！', $this->getAttributeLabel($attribute)));
        }
    }
}
