<?php
class Category extends LRecord {
    private $scondition = false; /*默认搜索条件*/
    public static $table = '{{category}}';
    public static $models = array(
        'product' => '产品分类'
    );
    
    private $oldParentid = null;
    
    private static $cates = array();
    private static function getCates($module) {
        if (!isset(self::$cates[$module])) {
            $sql = "SELECT * FROM :table WHERE  fromid=:fromid AND module=':module' ORDER BY listorder DESC,catid ASC";
            // $sql = "SELECT * FROM :table WHERE  fromid=:fromid AND module=':module' ORDER BY catid DESC,listorder DESC";
            $sql = strtr($sql, array(
                ':table' => self::$table,
                ':module' => $module,
                ':fromid' => Tak::getFormid() ,
            ));
            // Tak::KD($sql);
            $tags = Tak::getDb('db')->createCommand($sql)->queryAll();
            $result = array();
            foreach ($tags as $key => $value) {
                $result[$value['catid']] = $value;
            }
            self::$cates[$module] = $result;
        }
        return self::$cates[$module];
    }
    
    public static function getName($catid, $type,$isall = false) {
        $data = self::getCates($type);
        $result = '';
        if (isset($data[$catid])) {
            if ($isall&&$data[$catid]['arrparentid']) {
                $list = explode(",", $data[$catid]['arrparentid']);
                $arr = array();
                foreach ($list as $value) {
                    if (isset($data[$value])) {
                        $arr[] =  $data[$value]['catename'];
                    }
                }
               $result = join("  - ",$arr) ;
            }else{
                $result = $data[$catid]['catename'];    
            }            
        }
        return $result;
    }
    public static function getProductName($catid,$isall=false) {
        return self::getName($catid,'product',$isall);
    }
    public static function getCatsProduct() {
        return self::getCates('product');
    }
    
    public function setModel($module) {
        $module = strtolower($module);
        if (self::$models[$module]) {
            $this->module = $module;
        }
    }
    
    public static function getModel($module = false) {
        $module = strtolower($module);
        $result = isset(self::$models[$module]) ? $module : '';
        return $result ? $result : false;
    }
    
    public function getTypeName($module) {
        return self::getModel($module);
    }
    
    public function primaryKey() {
        return 'catid';
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function rules() {
        return array(
            array(
                'catid',
                'addNow',
                'on' => 'create'
            ) ,
            array(
                'arrparentid',
                'formCatid',
                'on' => 'create'
            ) ,
            array(
                'fromid',
                'autoID'
            ) ,
            array(
                'itemid, fromid, catename',
                'required'
            ) ,
            array(
                'level, parentid, child, listorder,itemid,fromid',
                'numerical',
                'integerOnly' => true
            ) ,
            
            array(
                'catid, fromid',
                'length',
                'max' => 10
            ) ,
            array(
                'item',
                'length',
                'max' => 20
            ) ,
            array(
                'catename',
                'length',
                'max' => 50
            ) ,
            array(
                'arrparentid',
                'length',
                'max' => 255
            ) ,
            array(
                'catid, fromid, item, catename, level, parentid, arrparentid, child, arrchildid, listorder',
                'safe',
                'on' => 'search'
            ) ,
        );
    }
    
    public function formCatid($attribute, $params) {
        $this->arrparentid = $this->catid;
    }
    //默认继承的搜索条件
    public function defaultScope() {
        $arr = array(
            'order' => 'listorder DESC'
        );
        $condition = array();
        $condition[] = 'fromid=' . Tak::getFormid();
        if ($this->module) {
            $condition[] = "module='" . $this->module . "'";
        }
        $arr['condition'] = join(" AND ", $condition);
        return $arr;
    }
    
    public function attributeLabels() {
        return array(
            'catid' => '分类编号',
            'fromid' => '平台会员ID',
            'module' => '模块',
            'item' => '信息数量',
            'catename' => '分类名',
            'level' => '级别',
            'parentid' => '上级分类',
            'arrparentid' => '上级所有ID',
            'child' => '是否有子分类',
            'arrchildid' => '子分类所有ID',
            'listorder' => '排序',
        );
    }
    
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('catename', $this->catename, 1);
        if ($this->parentid > 0) {
            $criteria->compare('parentid', $this->parentid);
        }
        $pageSize = 99;
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $pageSize,
            ) ,
        ));
    }
    
    protected function beforeSave() {
        $result = parent::beforeSave();
        if ($result) {
            if ($this->isNewRecord) {
                // Tak::KD($this->attributes,1);
                
                
            }
        }
        return $result;
    }
    
    public function setChangePid($pid) {
        $this->oldParentid = $pid;
    }
    
    protected function afterSave() {
        parent::afterSave();
        $CATEGORY = self::getCatsProduct();
        $catid = $this->catid;
        $arr = array(
            ':table' => self::$table,
            ':module' => $this->module,
            ':fromid' => Tak::getFormid() ,
            ':catid' => $catid,
        );
        if ($this->isNewRecord) {
            $childs = '';
            $childs.= ',' . $catid;
            if ($this->parentid) {
                //update cure
                $CATEGORY[$catid] = $this->attributes;
                $arrparentid = $this->get_arrparentid($catid, $CATEGORY);
                // Tak::KD($arrparentid,1);
                
            } else {
                $arrparentid = 0;
            }
            $sql = "UPDATE :table SET arrparentid=':arrparentid' WHERE catid=:catid AND  fromid=:fromid AND module=':module' ";
            $arr[':arrparentid'] = $arrparentid;
            $sql = strtr($sql, $arr);
            self::$db->createCommand($sql)->execute();
            // Tak::KD($sql,1);
            if ($this->parentid) {
                $parents = array();
                $cid = $this->parentid;
                $parents[] = $cid;
                while (1) {
                    if ($CATEGORY[$cid]['parentid']) {
                        $parents[] = $cid = $CATEGORY[$cid]['parentid'];
                    } else {
                        break;
                    }
                }
                foreach ($parents as $catid) {
                    $arrchildid = $CATEGORY[$catid]['child'] ? $CATEGORY[$catid]['arrchildid'] . $childs : $catid . $childs;
                    $arr[':arrchildid'] = $arrchildid;
                    $arr[':catid'] = $catid;
                    $sql = "UPDATE :table SET child=1 , arrchildid=':arrchildid' WHERE  catid=:catid AND  fromid=:fromid AND module=':module'";
                    $sql = strtr($sql, $arr);
                    // Tak::KD($sql,1);
                    self::$db->createCommand($sql)->execute();
                }
            }
        }
    }


    public function isDel()
    {
        $data = self::getCates($this->module);
        // Tak::KD($data);
        $ids = self::get_arrchildid($this->catid,$data);
        $sql = " SELECT count(s.itemid) FROM :table  AS s
                      WHERE typeid in(:ids) ";
             $sql = strtr($sql, array(
                ':table' => Product::$table,
                ':fromid' => Tak::getFormid(),
                ':ids' => $ids,
             ));
             // Tak::KD($sql,1);
             $count = self::$db->createCommand($sql)->queryScalar();
             // Tak::KD($count);
             // exit;
             return $count;
    }    

    public function del() {
        $result = false;
        $count = $this->isDel();
        if ($count>0) {
            $result =  '分类下已经有产品，不能进行删除';
        }else{
            if ($this->parentid) {
                $data = self::getCates($this->module);
                $ids = self::get_arrchildid($this->catid,$data);
                $this->deleteAll("catid in($ids)");
            }else{
                $this->delete();
            }
        }
        return $result;        
    }    
    
    public function get_arrparentid($catid, $CATEGORY) {
        if ($CATEGORY[$catid]['parentid'] && $CATEGORY[$catid]['parentid'] != $catid) {
            $parents = array();
            $cid = $catid;
            while ($catid) {
                if ($CATEGORY[$cid]['parentid']) {
                    $parents[] = $cid = $CATEGORY[$cid]['parentid'];
                } else {
                    break;
                }
            }
            $parents[] = 0;
            return implode(',', array_reverse($parents));
        } else {
            return '0';
        }
    }
    public function get_arrchildid($catid, $CATEGORY) {
        $arrchildid = '';
        foreach ($CATEGORY as $category) {
            if (strpos(',' . $category['arrparentid'] . ',', ',' . $catid . ',') !== false) $arrchildid.= ',' . $category['catid'];
        }
        return $arrchildid ? $catid . $arrchildid : $catid;
    }
}
