<?php

class Category extends CActiveRecord
{
	private $scondition = false;/*默认搜索条件*/
	public static $table = '{{category}}';

	public static $models = array('product'=>'产品分类');

	public $model = '';

	public function setModel($module){
		if (self::$models[$module]) {
			$this->model = $module;
		}		
	}

	public static function getModel($module=false)
	{
		$module = strtolower($module);		
		$result = isset(self::$models[$module])?$module:'';
		return  $result?$result:false;
	}

	public function getTypeName($module){
		return self::getModel($module);
	}
	public function primaryKey()
	{
		 return 'catid';
	}
	public function tableName()
	{
		$m = get_class($this);
		 return $m::$table;
	}	

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

   public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('catename', 'required'),
            array('level, parentid, child, listorder', 'numerical', 'integerOnly'=>true),
            array('catid, fromid', 'length', 'max'=>10),
            array('item', 'length', 'max'=>20),
            array('catename', 'length', 'max'=>50),
            array('arrparentid', 'length', 'max'=>255),

            array('catid, fromid, item, catename, level, parentid, arrparentid, child, arrchildid, listorder', 'safe', 'on'=>'search'),
        );
    }

    	//默认继承的搜索条件
    public function defaultScope()
    {
		$arr = array('order'=>'listorder DESC');
		$condition = array();    	
		$condition[] = 'fromid='.Tak::getFormid();
		if ($this->module) {
			$condition[] = "module='".$this->module."'";
		}
		
		$arr['condition'] = join(" AND ",$condition);

		return $arr;	
    }

    public function attributeLabels()
    {
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

	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('catename',$this->catename,1);
		if ($this->parentid>0) {
			$criteria->compare('parentid',$this->parentid);
		}		
		$pageSize = 99;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array( 
				'pageSize' => $pageSize, 
			), 
		));
	}    
}
