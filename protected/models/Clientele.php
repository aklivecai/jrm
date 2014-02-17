<?php
class Clientele extends ModuleRecord
{
	
	public $linkName = 'clientele_name'; /*连接的显示的字段名字*/
	public $profession = 4;
	/**
	 * @return string 数据表名字
	 */
	public function tableName()
	{
		return '{{clientele}}';
	}

	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(' clientele_name, industry, profession', 'required'),
			array('annual_revenue, employees, display, status', 'numerical', 'integerOnly'=>true),
			array('itemid, manageid, add_us, modified_us', 'length', 'max'=>25),
			array('fromid, last_time, add_time, add_ip, modified_time, modified_ip', 'length', 'max'=>10),
			array('clientele_name, rating, industry, profession, origin, email', 'length', 'max'=>100),
			array('address, note', 'length', 'max'=>255),
			array('telephone, fax, web', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, fromid, manageid, clientele_name, rating, annual_revenue, qw, profession, origin, employees, email, address, telephone, fax, web, display, status, last_time, add_time, add_us, add_ip, modified_time, modified_us, modified_ip, note', 'safe', 'on'=>'search'),

			
			array('clientele_name','checkRepetition'),
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
					, 'Manage'
					, 'manageid'
					,'select'=>'user_nicename'
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
				'manageid' => '录入者',
				'clientele_name' => '客户名称',
				'rating' => '客户等级',
				'annual_revenue' => '年营业额',
				'industry' => '客户类型', /*(新客户,意向客户,潜在客户,正式客户,VIP客户)*/
				'profession' => '客户行业',
				'origin' => '来源', /*(电话营销,主动来电,老客户,朋友介绍,广告杂志,互联网,其它)*/
				'employees' => '员工数量',
				'email' => '邮箱',
				'address' => '地址',
				'telephone' => '电话',
				'fax' => '传真',
				'web' => '网站',
				'display' => '显示情况', /*(0:自己,1：公共)*/
				'status' => '状态', /*(0:回收站,1:正常)*/
				'last_time' => '最后联系时间', /*(客户联系记录中修改)*/
				'add_time' => '添加时间',
				'add_us' => '添加人',
				'add_ip' => '添加IP',
				'modified_time' => '修改时间',
				'modified_us' => '修改人',
				'modified_ip' => '修改IP',
				'note' => '备注',
		);
	}

	//默认继承的搜索条件
	public function defaultScope()
	{
		$arr = parent::defaultScope();
		$condition = array();
		if (isset($arr['condition'])) {
			$condition[] = $arr['condition'];
		}
		$arr['order'] = $this->getConAlias('last_time DESC ');
		$arr['condition'] = join(" AND ",$condition);
		
		return $arr;
	}

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;
		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('fromid',$this->fromid);
		if ($this->manageid) {
			$criteria->compare('manageid',$this->manageid);
		}
		
		$criteria->compare('clientele_name',$this->clientele_name,true);
		$criteria->compare('rating',$this->rating);
		$criteria->compare('annual_revenue',$this->annual_revenue);
		$criteria->compare('industry',$this->industry);
		$criteria->compare('profession',$this->profession);
		$criteria->compare('origin',$this->origin);
		$criteria->compare('employees',$this->employees);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('web',$this->web,true);
		
		$criteria->compare('display',$this->display);
		$criteria->compare('status',$this->status);

		$this->setCriteriaTime($criteria,
			array('last_time','add_time','modified_time')
		);
		$criteria->compare('note',$this->note,true);
		return $cActive;
	}
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	private $_prsons = null;
	public  function getProsons(){
		if ($this->_prsons===null) {
			$m = ContactpPrson::model()->setGetCU();
			$m->scondition = false;
			$this->_prsons = $m->findAllByAttributes(array('clienteleid'=>$this->itemid));
		}
		return $this->_prsons;
	}

	public function move(){
		$tags = $this->getProsons();
		$manageid = $tihs->manageid;
		foreach ($tags as $key => $value) {
			$value->move($manageid);
		}
		return true;
	}

	protected function _del(){
		$tags = $this->getProsons();
		foreach ($tags as $key => $value) {
			$value ->isLog = false;
			$value->del();
		}
		return true;		
	}
	public function del(){
		$result = parent::del();
		if ($result) {
			 $this->_del();
		}
		return $result;
	}	

	public function setRestore(){
		$result = parent::setRestore();
		if ($result) {	
			$tags = $this->getProsons();
			foreach ($tags as $key => $value) {
				$value ->isLog = false;
				$value->setRestore();
			}
		}
		return $result;
	}
	   // 进公海
    public function setSeas(){
		$result = false;
		if ($this->status!=3) {
			$this->isLog = false;
			$this->status = 3;
			if($this->save()){
				$result = true;
				AdminLog::log($this->sName.'-'.Tk::g('仍进公海').' - 编号:'.$this->primaryKey);
			}else{
				 $arr = $this->getErrors();
			}
		}
			return $result;
    }

    public function getBySeas($manageid=false){
    		$result = false;
    		if ($manageid==false) {
    			$manageid = Tak::getManageid();
    		}    		
		$this->isLog = false;
		$this->status = 1;
		if($this->save()){
			$m = new MovesForm();
			$m->attributes = array('fMid'=>$this->manageid,'tMid'=>$manageid);
			$arr = $m->moveClienteles($this->primaryKey);    				
			if ($arr&&count($arr)>0) {
				$result = true;
				AdminLog::log($this->sName.'-'.Tk::g('在公海捞起').' - 编号:'.$this->primaryKey);	
				
			}
		}else{
			Tak::KD(1,1);
		}
    		return $result;
    }

	protected function afterDelete(){
		 parent::afterDelete();
			$tags = $this->getProsons();
			foreach ($tags as $key => $value) {
				$value ->isLog = false;
				$value->delete();
			}		 
	}	
}
