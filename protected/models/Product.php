<?php

/**
 * 这个模块来自表 "{{product}}".
 *
 * 数据表的字段 '{{product}}':
 * @property string $itemid
 * @property string $fromid
 * @property string $name
 * @property string $typeid
 * @property string $material
 * @property string $spec
 * @property string $unit
 * @property integer $stockssds
 * @property string $add_time
 * @property string $add_us
 * @property string $add_ip
 * @property string $modified_time
 * @property string $modified_us
 * @property string $modified_ip
 * @property string $note
 */
class Product extends ModuleRecord
{

	public static $table = '{{product}}';
	
	public $linkName = 'name'; /*连接的显示的字段名字*/
	protected $_stocks = null;

	public $price = '0.00';
	private $total = '0.00';
	private $stock = 0;	

	public function tableName()
	{
		return self::$table;
	}


	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(' name, typeid', 'required'),
			array('stocks, status', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical',),			
			array('itemid, add_us, modified_us', 'length', 'max'=>25),
			array('fromid, typeid, unit, add_time, add_ip, modified_time, modified_ip', 'length', 'max'=>10),
			array('name, material, spec,color', 'length', 'max'=>100),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, fromid, name, typeid, material, spec, unit, stocks, add_time, add_us, add_ip, modified_time, modified_us, modified_ip, note, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules. 表的关系，外键信息
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
	    $condition = array("item='product'");
    	     $condition[] = 'fromid='.Tak::getFormid();
    	     $sqlStocks =$this->getConAlias('itemid=iStocks.product_id');
		return array(
			'iType' => array(
				self::BELONGS_TO
				, 'TakType'
				, 'typeid'
				, 'select' => 'typename'
				, 'condition'=> join(" AND ",$condition)
			),
			'iStocks' => array(self::HAS_ONE
				, 'Stocks'
				// , 'itemid'
				// , 'product_id'
				,''
				,'condition'=>''
				,'order'=>''
				, 'select' => 'itemid,stocks,modified_time'
				,'on'=>$sqlStocks
				),				
		);			
	}

	/**
	 * @return array customized attribute labels (name=>label) 字段显示的
	 */
	public function attributeLabels()
	{
		return array(
				'itemid' => '物料编号',
				'fromid' => '平台会员ID',
				'name' => '产品型号',
				'typeid' => '货物分类',
				'material' => '材料',
				'spec' => '规格',
				'color' => '颜色',
				'unit' => '单位',
				'stocks' => '库存', /*(可负)*/
				'price' => '单价', 
				'add_time' => '添加时间',
				'add_us' => '添加人',
				'add_ip' => '添加IP',
				'modified_time' => '修改时间',
				'modified_us' => '修改人',
				'modified_ip' => '修改IP',
				'note' => '介绍',
				'status' => '状态', /*(0:回收站,1:正常)*/
		);
	}

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;

		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('fromid',$this->fromid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('typeid',$this->typeid);
		$criteria->compare('material',$this->material,true);
		$criteria->compare('spec',$this->spec,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('color',$this->color,true);
		$criteria->compare('stocks',$this->stocks);
		$criteria->compare('add_time',$this->add_time);
		$criteria->compare('add_us',$this->add_us);
		$criteria->compare('add_ip',$this->add_ip);
		$criteria->compare('modified_time',$this->modified_time);
		$criteria->compare('modified_us',$this->modified_us);
		$criteria->compare('modified_ip',$this->modified_ip);
		$criteria->compare('note',$this->note,true);

		$criteria->compare('status',$this->status);

		return $cActive;
	}


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	//默认继承的搜索条件
    public function defaultScope()
    {
    	if ($this->getDefaultScopeDisabled()) {
    		return array();
    	}
    	$arr = array();
    	if (func_num_args()>0&&func_get_arg(0)) {
    		$arr['order'] = $this->getConAlias('add_time DESC ');
    	}
    	$condition = array();
    	if ($this->scondition&&$this->scondition!='') {
    		$condition[] = $this->getConAlias($this->scondition);
    	}

    	$condition[] = $this->getConAlias('fromid='.Tak::getFormid());

    	if ($this->getCu()&&Tak::getManageid()!='44720284384568199') {
    		$condition[] = $this->getConAlias('manageid='.Tak::getManageid());
    	}

    	$arr['condition'] = join(" AND ",$condition);
    	return $arr;
    }  

    public function getConAlias($sql){
    	$alias = false;
    	if (property_exists($this,'tableAlias')&&$this->tableAlias) {
    		$alias = $this->tableAlias;
    	}else{
    		// $alias = 't';
    	}
    	if ($alias) {
    		$sql = $alias.'.'.$sql;
    	}
    	return $sql;
    }      

	//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave();
	    if($result){
	        //添加数据时候
	        if ( $this->isNewRecord ){

	        }else{
	        	//修改数据时候
	        }
	    }
	    return $result;
	}

	//保存数据后
	protected function afterSave(){
		parent::afterSave();
		if ($this->isNewRecord) {
			$m = new Stocks;
			$m->product_id = $this->primaryKey;
			$m->stocks = 0;
			$m->status = 1;
			$m->save();
		}
	}	

	protected function loadStock($type=1){
		if ($this->_stocks === null) {
			$m = Stocks::model();
			if ($type=2) {
				$m->setRecycle();/*搜索回收站*/
			}
			$this->_stocks = $m->findByAttributes(array('product_id'=>$this->primaryKey));
		}

		return $this->_stocks;
	}

	public function del(){
		if (parent::del()) {
			$m = $this->loadStock();
			if ($m) {
				$m->del();
			}
		}
	}

	public function setRestore(){
		if (parent::setRestore()) {
			$m = $this->loadStock(2);
			if ($m) {
				$m->setRestore();
			}
		}
	}

	//删除信息后
	protected function afterDelete(){
		parent::afterDelete();
		$m = $this->loadStock();
		if ($m) {
			$m->delete();
		}
	}	
	protected function beforeDeletes(){
		$m = $this->loadStock();	
		Tak::KD($m,1);		
		return false;
		parent::afterDelete();

	}	

	public function getTotal(){
		$result = 0 ;
		if ($this->price>0&&$this->getStock()!=0) {
			$result = $this->price*$this->getStock();
		}
		return $result;
	}
	public function getStock(){
		$result = Stocks::getStocks($this->itemid);
		return $result;		
	}

	public static function getTotals($sql=false){
		$model = self::model();
		$condition = $model->defaultScope(false);
		if (is_array($condition)&&$condition['condition']) {
			$condition = array($condition['condition']);
		}else{
			$condition = array();
		}		
	        if ($sql) {
	            $condition[]= $sql;
	        }	
		$condition =  join(' AND ',$condition);
	       $sql = ' SELECT  SUM(s.stotals) AS stotal,SUM(p.price*s.stotals) AS ptotal FROM :product p
	       	,(SELECT SUM(stocks) AS stotals,product_id  FROM :stock WHERE product_id in ( 
					SELECT itemid FROM :product WHERE :condition 
	       		)  GROUP BY product_id) s
	        WHERE p.itemid = s.product_id ';
		
		$sql = strtr($sql,array(
			':stock'=> Stocks::$table,
			':product'=> self::$table,
			':productid'=>$productid,
			':condition' => $condition?$condition:' 1=1 '
		));
		
		$query = self::$db->createCommand($sql);
		$result = $query->queryRow();	
		return $result;
	}

}
