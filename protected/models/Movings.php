<?php
class Movings extends ModuleRecord {
    
    public $linkName = array(
        'enterprise',
        'time'
    );
    public $type = null;
    private $_typename = '';
    private $product_movings = null;
    private $products = null;    
    public $time = '';    
    public static $table = '{{movings}}';    
    public $tableAlias = false;
    public $warehouse_id = 0;
    public function init() {
        parent::init();
        $this->setSName();
        $this->time = time();
    }
    
    public function initak($type) {
        if ($type) {
            $this->type = $type;
            $this->setType($type);
            $this->setSName();
        }
    }
    
    public function setType($type) {
        $this->scondition.= " AND type = '$type' ";
    }
    
    public function setSName() {
        $this->_typename = Tak::getMovingsType($this->type);
        $this->sName = Tk::g($this->_typename);
    }
    
    public function getTypeName() {
        return $this->_typename;
    }
    
    public function getProductMovings() {
        if ($this->product_movings === null) {
            $dataProvider = new CActiveDataProvider('ProductMoving', array(
                'criteria' => array(
                    'condition' => '  movings_id=' . $this->itemid,
                ) ,
            ));
            $this->product_movings = $dataProvider;
        }
        // Tak::KD($this->product_movings,1);
        return $this->product_movings;
    }

    public function rules() {
        return array(
            array(
                'typeid, time, enterprise, warehouse_id',
                'required'
            ) ,
            array(
                'type, status, warehouse_id',
                'numerical',
                'integerOnly' => true
            ) ,
            array(
                'itemid, add_us, modified_us',
                'length',
                'max' => 25
            ) ,
            array(
                'fromid, time, typeid, time_stocked, add_time, add_ip, modified_time, modified_ip',
                'length',
                'max' => 10
            ) ,
            array(
                'numbers, enterprise, us_launch',
                'length',
                'max' => 100
            ) ,
            array(
                'note',
                'length',
                'max' => 255
            ) ,
            array(
                'itemid, fromid, type, numbers, time, typeid, enterprise, us_launch, time_stocked, add_time, add_us, add_ip, modified_time, modified_us, modified_ip, note, status,warehouse_id',
                'safe',
                'on' => 'search'
            ) ,
            
            array(
                'time',
                'checkTime'
            ) ,
        );
    }
    /**
     * @return array relational rules. 表的关系，外键信息
     */
    public function relations() {
        $condition = array(
            "item='" . strtolower($this->_typename) . "-type'"
        );
        $condition[] = 'fromid=0';
        return array(
            'iType' => array(
                self::BELONGS_TO,
                'TakType',
                'typeid',
                'select' => 'typename',
                'condition' => join(" AND ", $condition)
            ) ,
        );
    }
    public function attributeLabels() {
        $stype = Yii::app()->getController()->id;
        if ($stype == 'purchase') {
            $stype = 'Purchase';
        } elseif ($stype == 'sell') {
            $stype = 'Sell';
        } else {
            $stype = $this->_typename;
        }
        $enterprise = Tk::g($stype . ' enterprise');
        $typeid = Tk::g($stype . ' typeid');
        $time = Tk::g($stype) . '日期';
        $numbers = Tk::g($stype . ' numbers');
        // Tak::KD($time);
        // Tak::KD(Yii::app()->getController()->id,1);
        return array(
            'itemid' => '编号',
            'fromid' => '平台会员ID',
            'warehouse_id' => '仓库',
            'type' => '类型', /*(1:入库|2:出库)*/
            'numbers' => $numbers,
            'time' => $time,
            'typeid' => $typeid,
            'enterprise' => '来源对象', /*$enterprise单位名称'*/
            'us_launch' => '经手人',
            'time_stocked' => '确认操作日期',
            'add_time' => '添加时间',
            'add_us' => '添加人',
            'add_ip' => '添加IP',
            'modified_time' => '修改时间',
            'modified_us' => '修改人',
            'modified_ip' => '修改IP',
            'note' => '备注',
            'status' => '状态', /*(0:回收站,1:正常)*/
        );
    }
    
    public function search() {
        $cActive = parent::search();
        $criteria = $cActive->criteria;
        $criteria->compare('itemid', $this->itemid);
        $criteria->compare('fromid', $this->fromid);
        $criteria->compare('type', $this->type);
        if ($this->warehouse_id>0) {
           $criteria->compare('warehouse_id', $this->warehouse_id);
        }        
        if ($this->typeid >= 0) {
            $criteria->compare('typeid', $this->typeid);
        }        
        $criteria->compare('numbers', $this->numbers);        
        $this->setCriteriaTime($criteria, array(
            'time',
            'add_time',
            'modified_time'
        ));
        
        $criteria->compare('enterprise', $this->enterprise, true);        
        $criteria->compare('us_launch', $this->us_launch, true);
        $criteria->compare('time_stocked', $this->time_stocked, true);        
        $criteria->compare('add_us', $this->add_us, true);
        $criteria->compare('add_ip', $this->add_ip, true);        
        $criteria->compare('modified_us', $this->modified_us, true);
        $criteria->compare('modified_ip', $this->modified_ip, true);
        $criteria->compare('note', $this->note, true);
        $criteria->compare('status', $this->status);
        return $cActive;
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    //默认继承的搜索条件
    public function defaultScope() {
        $arr = parent::defaultScope();
        $condition = array(
            $arr['condition']
        );
        // $condition[] = 'display>0';
        $sql = join(" AND ", $condition);
        $t = explode("AND type = '1'", $sql);
        if (count($t)>1&&strpos($t[1], 'type') !== false) {
            $sql = join($t);
        }
        
        $sql = str_replace("type = '1' AND", "type", $sql);
        $arr['condition'] = $sql;
        
        return $arr;
    }
    //保存数据前
    protected function beforeSave() {
        $result = parent::beforeSave();
        
        if ($result && $this->status != TakType::STATUS_DELETED) {
            $proucts = isset($_POST['Product']['number']) ? $_POST['Product']['number'] : false;
            if (!$proucts || !is_array($proucts) || count($proucts) == 0) {
                $result = false;
                $this->addError('', "请填入入库产品明细");
            } else {
                $iserr = false;
                $mproducts = array();
                foreach ($proucts as $key => $value) {
                    if (!is_numeric($value) || !is_numeric($key) || $value <= 0 || $key <= 0) {
                        $iserr = true;
                        break;
                    }
                }
                
                $mproducts = Product::model()->findAllByPk(array_keys($proucts));
                if (!$iserr && count($mproducts) != count($proucts)) {
                    $iserr = true;
                }
                
                if ($iserr) {
                    $result = false;
                    $this->addError('', "入库产品明细输入不正确");
                    $_arr = array();
                    foreach ($mproducts as $key => $value) {
                        $_arr[$value->itemid] = array(
                            'name' => $value->name,
                            'numbers' => $proucts[$value->itemid],
                            'price' => $proucts['price'][$value->itemid],
                            'note' => $proucts['note'][$value->itemid],
                        );
                    }
                    $_POST['Product'] = $_arr;
                } else {
                    $this->products = $_POST['Product'];
                }
                return $result;
            }
        }
        return $result;
    }
    //保存数据后
    protected function afterSave() {
        parent::afterSave();
        if ($this->products != null) {
            ProductMoving::model()->deleteAllByAttributes(array(
                'type' => $this->type,
                'movings_id' => $this->itemid
            ));
            $tags = $this->products['number'];
            $m = new ProductMoving;
            $m->type = $this->type;
            $m->movings_id = $this->itemid;
            foreach ($tags as $key => $value) {
                $m->setIsNewRecord(true);
                $m->itemid = Tak::fastUuid();
                $m->product_id = $key;
                $m->numbers = $value;
                $m->price = isset($this->products['price'][$key]) ? $this->products['price'][$key] : '0.00';
                $m->note = isset($this->products['note'][$key]) ? $this->products['note'][$key] : '';
                if (!$m->save()) {
                    Tak::KD($m->getErrors());
                }
            }
        }
    }
    
    public function affirm() {
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try {
            $time = Tak::now();
            $itemid = $this->itemid;
            $arr = array(
                ':time' => $time,
                ':itemid' => $itemid,
                ':operate' => $this->type == 1 ? '+' : '-',
                ':movings' => '{{movings}}',
                ':stocks' => '{{stocks}}',
                ':product_moving' => '{{product_moving}}',
            );
            $sql = "UPDATE :movings SET time_stocked=:time WHERE itemid=:itemid";
            $sql = strtr($sql, $arr);
            $connection->createCommand($sql)->execute();
            
            $sql = "UPDATE :stocks AS s ,:product_moving AS pm SET s.stocks=stocks :operate pm.numbers , s.modified_time = :time WHERE s.product_id = pm.product_id AND  pm.movings_id=:itemid AND pm.time_stocked=0";
            $sql = strtr($sql, $arr);
            $connection->createCommand($sql)->execute();
            
            $sql = "UPDATE :product_moving SET time_stocked=:time WHERE movings_id=:itemid AND time_stocked=0 ";
            $sql = strtr($sql, $arr);
            $connection->createCommand($sql)->execute();
            
            $sql = strtr($sql, $arr);
            $connection->createCommand($sql)->execute();
            
            $transaction->commit();
        }
        catch(Exception $e) // 如果有一条查询失败，则会抛出异常
        {
            $transaction->rollBack();
        }
    }
    
    public function getLink($itemid = false, $action = 'view') {
        if (!$itemid) {
            $itemid = $this->primaryKey;
        }
        $link = Yii::app()->createUrl(strtolower($this->_typename) . '/' . $action, array(
            'id' => $itemid
        ));
        return $link;
    }
    //删除信息后
    protected function afterDelete() {
        parent::afterDelete();
    }
}
