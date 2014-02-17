<?php

/**
 * 这个模块来自表 "{{order_flow}}".
 *
 * 数据表的字段 '{{order_flow}}':
 * @property string $itemid
 * @property string $order_id
 * @property string $status
 * @property string $action_user
 * @property string $add_time
 * @property string $add_us
 * @property string $add_ip
 * @property string $note
 */
class OrderFlow extends MRecord
{
	public $action_user = '系统';

	/**
	 * @return string 数据表名字
	 */
	public function tableName()
	{
		return '{{order_flow}}';
	}

	public function init(){
		parent::init();
		$itemid = Yii::app()->user->getState('order_flow_id');
		if (!$itemid) {
			$itemid = Tak::fastUuid();
			Yii::app()->user->setState('order_flow_id',$itemid);
		}
		$this->itemid = $itemid;
	}

	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id', 'required'),
			array('itemid, order_id, add_us', 'length', 'max'=>25),
			array('status, add_time, add_ip', 'length', 'max'=>10),
			array('action_user, name', 'length', 'max'=>100),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, order_id, status, action_user, add_time, add_us, add_ip, note', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label) 字段显示的
	 */
	public function attributeLabels()
	{
		return array(
				'itemid' => '编号',
				'order_id' => '订单编号',
				'name' => '流程名字',
				'status' => '订单状态', /*(0:删除,1:    您提交了订单-请等待系统确认-客户,101等待付款-通过审核,201取消订单-无效订单,102等待发货-已经付款,103等待收货,.客户自己定义.,999完成订单,)*/
				'action_user' => '操作人',
				'add_time' => '添加时间',
				'add_us' => '添加人',
				'add_ip' => '添加IP',
				'note' => '操作内容',
		);
	}

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;

		$criteria->compare('itemid',$this->itemid,true);
		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('action_user',$this->action_user,true);
		$criteria->compare('add_time',$this->add_time,true);
		$criteria->compare('add_us',$this->add_us,true);
		$criteria->compare('add_ip',$this->add_ip,true);
		$criteria->compare('note',$this->note,true);
		return $cActive;
	}


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	//默认继承的搜索条件
    public function defaultScope()
    {
    	$arr = parent::defaultScope();
    	$condition = array();	
    	if (isset($arr['condition'])) {
    		$condition[]=$arr['condition'];
    	}
    	$arr['condition'] = join(" AND ",$condition);
    	// $condition[] = 'display>0';
    	$arr['order'] = ' add_time ASC ';
    	return $arr;
    }

	//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave(true);
	    
	    if($result){
	    	$arr = Tak::getOM();
	        //添加数据时候
	        if ( $this->isNewRecord ){
	        	if (!$this->itemid) {
	        		$this->itemid = $arr['itemid'];
	        	}
	        	$this->add_us = $arr['manageid'];
	        	$this->add_time = $arr['time'];
	        	$this->add_ip = $arr['ip'];
	        }else{
	        	//修改数据时候
	        }
	    }
	    return $result;
	}

	//保存数据后
	protected function afterSave(){
		parent::afterSave();
		Yii::app()->user->setState('order_flow_id',false);
	}	

	//删除信息后
	protected function afterDelete(){
		parent::afterDelete();
	}	

	public $files = null;
	public function loadFiles(){
		if ($this->files==null) {
			$this->files = OrderFiles::model()->getListByActionID($this->itemid);
		}
		return $this->files;
	}

	public function getFilesImg(){
		$result = '';
		$files = $this->loadFiles();
		foreach ($files as $k1 => $v1) {
				$result .= $v1->getLink();
		}
		return $result;
	}

	public function getName(){
		$result = OrderType::item('order-status',$this->status);
		if (!$result) {
			if ($this->name) {
				$result = $this->name;
			}else{
				$result = TakType::item('order-flow',$this->status);
			}			
		}
		return $result;
	}

	public function getListByOrder($condition){
		$arr = array();
		foreach ($condition as $key => $value) {
			$arr[$key] = $value;
		}
		$list = $this->findAllByAttributes($arr);
		foreach ($list as $key => $value) {
			$value->loadFiles();
		}
		return $list;
	}	
}
