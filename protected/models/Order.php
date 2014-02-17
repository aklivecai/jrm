<?php

/**
 * 这个模块来自表 "{{order}}".
 *
 * 数据表的字段 '{{order}}':
 * @property string $itemid
 * @property string $fromid
 * @property string $manageid
 * @property string $add_time
 * @property string $total
 * @property integer $status
 * @property string $pay_time
 * @property string $delivery_time
 * @property string $u_time
 * @property string $invoice_number
 * @property string $note
 */
class Order extends MRecord
{

	private $_orderinfo = null;

	public $status = 1; /*订单默认状态*/

	/**
	 * @return string 数据表名字
	 */
	public function tableName()
	{
		return '{{order}}';
	}

	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fromid', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('itemid, manageid', 'length', 'max'=>25),
			array('fromid, add_ip, add_time, total, pay_time, delivery_time, u_time', 'length', 'max'=>10),
			array('invoice_number', 'length', 'max'=>50),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, fromid, manageid, add_time, total, status, pay_time, delivery_time, u_time, invoice_number, note', 'safe', 'on'=>'search'),
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
			'iManage' => array(self::BELONGS_TO
				, 'Profile'
				, 'manageid'
				,'condition'=>''
				,'order'=>''
				// ,'on'=>'iClientele.itemid=clienteleid'
				),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label) 字段显示的
	 */
	public function attributeLabels()
	{
		$result = array(
				'itemid' => '订单编号',
				'fromid' => '商铺',
				'manageid' => '下单用户',
				'add_time' => '下单时间',
				'total' => '总金额',
				'status' => '订单状态', /*(-1:删除,1待审核,2已取消,3通过审核,4已完成)*/
				'pay_time' => '付款时间',
				'delivery_time' => '发货时间',
				'u_time' => '用户确认收货时间',
				'invoice_number' => '发货单号',
				'note' => '备注',
				'add_ip' => '下单IP',
		);
		$_ts = parent::attributeLabels();
		return $result;
	}

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;

		if ($this->itemid>0){
			if(is_numeric($this->itemid)) {
				$criteria->compare('itemid',$this->itemid,true);
			}
			$criteria->addCondition("itemid IN (SELECT order_id FROM {{order_product}} WHERE name LIKE '%{$this->itemid}%'  GROUP BY order_id )",'OR');
		}		

		$criteria->compare('fromid',$this->fromid);
		$criteria->compare('manageid',$this->manageid);
		$criteria->compare('add_time',$this->add_time);
		$criteria->compare('total',$this->total);
		
		$criteria->compare('pay_time',$this->pay_time);
		$criteria->compare('delivery_time',$this->delivery_time);
		$criteria->compare('u_time',$this->u_time);
		$criteria->compare('invoice_number',$this->invoice_number);
		$criteria->compare('note',$this->note,true);

		if ($this->status&&$this->getState($this->status)) {
			// Tak::KD($this->status,1);
			$criteria->compare('status',$this->status);	
		}		
		return $cActive;
	}


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	//默认继承的搜索条件
    public function defaultScope($iscompany=true)
    {
    	$arr = parent::defaultScope();
    	$arr['order'] = ' add_time DESC ';
    	$condition = array('status>0');
    	if ($iscompany) {
    		$condition[] = 'fromid='.Tak::getFormid();
    	}		
    	if(isset($arr['condition'])){
    		$condition[] = $arr['condition'];
    	}
    	$arr['condition'] = join(" AND ",$condition);
    	return $arr;
    }

	//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave(true);
	    if(!$isok&&$result){
	        //添加数据时候
	        $arr = Tak::getOM();
	        if ( $this->isNewRecord ){
	        	if (!$this->itemid) {
	        		$this->itemid = $arr['itemid'];
	        	}
	        	$this->add_time = $arr['time'];
	        	$this->add_ip = $arr['ip'];
	        	$this->manageid = $arr['manageid'];
	        }else{
	        	//修改数据时候
	        }
	    }
	    return $result;
	}

	//保存数据后
	protected function afterSave(){
		parent::afterSave();
		// 
		if ( $this->isNewRecord ){
			$msg = new  OrderFlow;
			$msg->order_id = $this->itemid;
			$msg->name = '';
			$msg->action_user = '客户';
			$msg->note = '您提交了订单-请等待系统确认';
			$msg->save();
		}
	}	

	private $_products = null;
	public function getProducts(){
		if($this->_products===null){
			$arr = array(
				'order_id' => $this->itemid,
				'fromid' => $this->fromid,
			);
			$this->_products = OrderProduct::model()->getListByOrder($arr);
		}
		return $this->_products;
	}
	public function wProducts(){

		$products = $this->getProducts();
		$html = '<ul class="wap-products li-product">';
		foreach ($products as $key => $value) {
			$pname = $value->name;
			$html .= '<li>';			
			if ($value->files!=null
				&&count($value->files)>0) {
				$html .= "<strong>".mb_substr($pname,0,20,'utf-8')."</strong>";
				$html .= '<div class="wap-pic">';

				$html .= $value->getFilesImg();
				$html .= "<dl><dt>型号:</dt><dd>&nbsp;{$value['model']}</dd>";
				$html .= "<dt>规格:</dt><dd>&nbsp;{$value['standard']}</dd>";
				$html .= "<dt>颜色:</dt><dd>&nbsp;{$value['color']}</dd>";
				$html .= "<dt>单位:</dt><dd>&nbsp;{$value['unit']}</dd>";
				$html .= "<dt>单价:</dt><dd>&nbsp;{$value['price']}</dd>";
				$html .= "<dt>总价:</dt><dd>&nbsp;{$value['sum']}</dd>";
				$html .= '</dl></div>';
			}else{
				$html .= "<div class='otitle'>{$value['name']}</div>";
			}
			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	}

	private $_flows = null;
	public function getFlows(){
		if($this->_flows===null){
			$arr = array(
				'order_id' => $this->itemid,
			);
			$this->_flows = OrderFlow::model()->getListByOrder($arr);
		}
		return $this->_flows;
	}
	public function getFlowLast(){
		return OrderFlow::model()->findByAttributes(array('order_id' => $this->itemid,),
				array('order'=>'itemid DESC')
			);
	}
	public function getListStatus(){
		$result = array();
		$temps = $this->getFlows();
		foreach ($temps as $key => $value) {
			$result[$value->status]=$value->status;
		}
		return $result;
	}

	//删除信息后
	protected function afterDelete(){
		parent::afterDelete();
	}

	public function upTotal(){
		$connection = Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try
		{
			$itemid = $this->itemid;
			$arr = array(
				':itemid'=>$itemid,
				':order'=>$this->tableName(),
				':product'=>'{{order_product}}',

			);
			$sql="
UPDATE :order SET
total = (SELECT SUM(sum) FROM :product WHERE order_id=':itemid') 
WHERE itemid = ':itemid'";
			$sql = strtr($sql,$arr);
		    $connection->createCommand($sql)->execute();
		    $transaction->commit();
		}
		catch(Exception $e) // 如果有一条查询失败，则会抛出异常
		{
		    $transaction->rollBack();
		}		
	}

	public function getState(){
		return OrderType::item('order-status',$this->status);
	}
	public static function getSearchStatus(){
		$temps = OrderType::items('order-status');		
		$arr = array(''=>'全部状态');
		foreach ($temps as $key => $value) {
			$arr[$key] = $value;
		}
		return $arr;
	}
	public static function getSearchTime(){
		$temps = Tak::searchData();
		$arr = array(''=>'全部时间');
		foreach ($temps as $key => $value) {
			$arr[$key] = $value['name'];
		}
		return $arr;
	}

	public function getOrderInfo(){
		if ($this->_orderinfo==null) {
			$this->_orderinfo = OrderInfo::model()->findByAttributes(array('itemid'=>$this->itemid));
		}
		return $this->_orderinfo;
	}
	public function getLinkUP(){
		$result = $this->getLink($this->itemid,'updates');
		return $result;
	}		

	public function saveStatus($status,$note=''){
		$this->status = $status;
		$time = Tak::now();
		if($status=='102'){
			$this->pay_time = $time;
		}
		if($status=='103'){
			$this->delivery_time = $time;
		}
			
		if ($this->save()) {
			$msg = new  OrderFlow;
			$msg->order_id = $this->itemid;
			$msg->name = '';
			$msg->status = $status;
			$msg->note = $note;
			if ($status==200) {
				$msg->action_user = '客户';
			}
			$msg->save();
		}
	}

}