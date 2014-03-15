<?php
class Warehouse extends LRecord {
    public static $table = '{{warehouse}}';
    public static $datas = null;
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function primaryKey() {
        return 'itemid';
    }
    public function rules() {
        return array(
            array(
                'itemid',
                'addNow',
                'on' => 'create'
            ) ,
            array(
                'fromid',
                'autoID'
            ) ,
            array(
                'itemid, fromid, name',
                'required'
            ) ,
            array(
                'itemid, fromid',
                'length',
                'max' => 25
            ) ,
            array(
                'name,user_name',
                'length',
                'max' => 64
            ) ,
            array(
                'telephone',
                'length',
                'max' => 50
            ) ,
            array(
                'note',
                'length',
                'max' => 255
            ) ,
            array(
                'listorder',
                'length',
                'max' => 10
            ) ,
            
            array(
                'itemid, fromid, listorder',
                'numerical',
                'integerOnly' => true
            ) ,
            
            array(
                'name',
                'checkRepetition'
            ) ,
        );
    }
    
    public function attributeLabels() {
        return array(
            'itemid' => '编号',
            'fromid' => '平台会员ID',
            'name' => '名称',
            'user_name' => '负责人',
            'telephone' => '联系电话',
            'listorder' => '排序',
            'note' => '备注',
        );
    }
    
    public static function getDatas() {
        if (self::$datas === null) {
            $sql = 'SELECT * FROM :tabl WHERE :where ORDER BY listorder DESC,itemid DESC';
            $where = 'fromid=' . Tak::getFormid();
            $sql = strtr($sql, array(
                ':tabl' => self::$table,
                ':where' => $where,
            ));
            $tags = Tak::getDb('db')->createCommand($sql)->queryAll(true);
            self::$datas = $tags;
        }
        return self::$datas;
    }
    
    public static function getSelect() {
        $data = self::getDatas();
        $result = array();
        foreach ($data as $key => $value) {
            $result[$value['itemid']] = $value['name'];
        }
        return $result;
    }
    
    public static function toSelects($label = false) {
        $data = self::getDatas();
        $result = array();
        if ($label) {
            $result['-1'] = $label;
        }
        foreach ($data as $key => $value) {
            $result[$value['itemid']] = $value['name'];
        }
        return $result;
    }
    
    protected function beforeSave() {
        $result = parent::beforeSave();
        if ($result) {
            if ($this->isNewRecord) {
                $m = $this->getLast();
                if ($m) {
                    $this->listorder = $m['listorder'] + 5;
                } else {
                    $this->listorder = 1;
                }
            }
        }
        return $result;
    }
    
    protected function getLast() {
        $sql = 'SELECT * FROM :tabl WHERE :where ORDER BY listorder DESC,itemid DESC';
        $where = 'fromid=' . Tak::getFormid();
        $sql = strtr($sql, array(
            ':tabl' => self::$table,
            ':where' => $where,
        ));
        $tags = Tak::getDb('db')->createCommand($sql)->queryRow();
        return $tags;
    }
    
    public static function getDataProvider() {
        $data = self::getDatas();
        $dataProvider = new CArrayDataProvider($data, array(
            'id' => 'Warehouse',
            'sort' => array(
                'attributes' => array(
                    'listorder',
                ) ,
            ) ,
            'pagination' => array(
                'pageSize' => 999,
            ) ,
        ));
        return $dataProvider;
    }
    
    public function isDel() {
        $sql = " SELECT pt.itemid FROM :tabl-pm AS pt  LEFT JOIN :tabl-m AS m 
    	 			ON(pt.movings_id=m.itemid)
    	 		  WHERE m.fromid = :fromid AND pt.warehouse_id = :itemid";
        
        $sql = " SELECT count(s.itemid) FROM :table  AS s
    	 		  WHERE s.fromid = :fromid AND s.warehouse_id = :itemid ";
        $sql = strtr($sql, array(
            ':table' => Stocks::$table,
            ':fromid' => Tak::getFormid() ,
            ':itemid' => $this->itemid,
        ));
        $count = self::$db->createCommand($sql)->queryScalar();
        return $count;
    }
    public function del() {
        $result = false;
        $count = $this->isDel();
        if ($count > 0) {
            $result = '该仓库已经有出入库记录，不能进行删除';
        } else {
            $this->delete();
        }
        return $result;
    }
    
    protected function getOjb($opt = 'next', $isid = false) {
        $m = $this->mName;
        $result = null;
        $itemid = $this->getItemid();
        $arr = array(
            'pre' => array(
                'opt' => '<',
                'order' => 'DESC'
            ) ,
            'next' => array(
                'opt' => '>',
                'order' => 'ASC'
            ) ,
        );
        if ($itemid > 0 && isset($arr[$opt])) {
            $t = $arr[$opt];
        } else {
            return null;
        }
        $s1 = $this->defaultScope();
        $sqlWhere = array(
            $s1['condition']
        );
        $sqlWhere[] = ':itemid <> :current_id';
        
        $sqlWhere[] = 'listorder :opt :listorder';
        $sqlWhere = array_filter($sqlWhere);
        $sqlWhere = join(" AND ", $sqlWhere);
        
        $orderCol = " listorder :order ,:itemid DESC ";
        $col = $isid ? ':itemid' : '*';
        
        $sql = "SELECT $col FROM :tableName WHERE $sqlWhere ORDER BY $orderCol ";
        // Tak::KD($this->listorder);
        
        // LIMIT 1
        $sql = strtr($sql, array(
            ':tableName' => $this->tableName() ,
            ':itemid' => $this->primaryKey() ,
            ':current_id' => $itemid,
            ':listorder' => $this->listorder,
            ':opt' => $t['opt'],
            ':order' => $t['order']
        ));
        
        $dataReader = self::$db->createCommand($sql)->queryRow();
        if (count($dataReader) > 0) {
            $result = $dataReader;
        }
        return $result;
    }
    public function getNext($isid = false) {
        $result = $this->getOjb('next', $isid);
        if ($result && $isid) {
            $result = current($result);
        }
        return $result;
    }
    
    public function getPrevious($isid = false) {
        $result = $this->getOjb('pre', $isid);
        if ($result && $isid) {
            $result = current($result);
        }
        return $result;
    }
}
