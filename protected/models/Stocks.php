<?php

/**
 * 这个模块来自表 "{{stocks}}".
 *
 * 数据表的字段 '{{stocks}}':
 * @property string $itemid
 * @property string $fromid
 * @property string $product_id
 * @property integer $stocks
 * @property string $add_time
 * @property string $add_us
 * @property string $add_ip
 * @property string $modified_time
 * @property string $modified_us
 * @property string $modified_ip
 * @property string $note
 */
class Stocks extends ModuleRecord
{

	public static $table = '{{stocks}}';
	/**
	 * @return string 数据表名字
	 */
	public function tableName()
	{
		return self::$table;
	}

	public function init(){
		parent::init();
		$this->isLog = false;
	}	

	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id', 'required'),
			array('stocks', 'numerical', 'integerOnly'=>true),
			array('itemid, product_id, add_us, modified_us', 'length', 'max'=>25),
			array('fromid, add_time, add_ip, modified_time, modified_ip', 'length', 'max'=>10),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, fromid, product_id, stocks, add_time, add_us, add_ip, modified_time, modified_us, modified_ip, note', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules. 表的关系，外键信息
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'iProduct' => array(self::BELONGS_TO
				, 'Product'
				, 'product_id'
				,'condition'=>''
				,'order'=>''
				),			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label) 字段显示的
	 */
	public function attributeLabels()
	{
		return array(
				'itemid' => '编号',
				'fromid' => '平台会员ID',
				'product_id' => '产品',
				'stocks' => '结存数量', /*(可负)*/
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

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;
		$criteria->compare('itemid',$this->itemid,true);
		$criteria->compare('fromid',$this->fromid,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('stocks',$this->stocks);
		$criteria->compare('add_time',$this->add_time,true);
		$criteria->compare('add_us',$this->add_us,true);
		$criteria->compare('add_ip',$this->add_ip,true);
		$criteria->compare('modified_time',$this->modified_time,true);
		$criteria->compare('modified_us',$this->modified_us,true);
		$criteria->compare('modified_ip',$this->modified_ip,true);
		$criteria->compare('note',$this->note,true);
		return $cActive;
	}

	public  static function getStocks($productid){
		$sql = 	'SELECT SUM(stocks) FROM :tabl WHERE product_id=:productid';
		$sql = strtr($sql,array(
			':tabl'=> self::$table,
			':productid'=>$productid,
		));
     		$query = self::$db->createCommand($sql);
     		// $query->bindParam(":productid",$productid);
    		$count = $query->queryScalar();	
		return $count;
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	//默认继承的搜索条件
    public function defaultScope()
    {
    	// $arr = parent::defaultScope();
    	// $condition = array($arr['condition']);
    	 // $condition[] = ' product_id IN (SELECT itemid AS aid FROM {{product}} AS p WHERE p.status=1)';
    	// $arr['condition'] = join(" AND ",$condition);
    	$arr = array(
    		'condition' =>$this->getConAlias('fromid='.Tak::getFormid())
    	);
    	return $arr;
    	return array();
    }

	//删除信息后
	protected function afterDelete(){
		parent::afterDelete();
	}    
}
