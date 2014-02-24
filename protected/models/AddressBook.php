<?php
class AddressBook extends ModuleRecord
{
	public $linkName = 'name';	
	public static $table = '{{address_book}}';
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('sex, display, status', 'numerical', 'integerOnly'=>true),
			array('itemid, groups_id, add_us, modified_us', 'length', 'max'=>25),
			array('fromid, longitude, latitude, add_time, add_ip, modified_time, modified_ip', 'length', 'max'=>10),
			array('name', 'length', 'max'=>64),
			array('telephone,email, phone, address, location, note', 'length', 'max'=>255),
			array('department, position', 'length', 'max'=>100),
	
			array('itemid, groups_id, fromid, name, email, phone, address, department, position, sex, longitude, latitude, location, display, add_time, add_us, add_ip, modified_time, modified_us, modified_ip, note, status', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'groups' => array(self::BELONGS_TO
				, 'AddressGroups'
				, 'groups_id'
				,'condition'=>''
				,'order'=>''
				),
		);
	}

	public function attributeLabels()
	{
		return array(
				'itemid' => '编号',
				'groups_id' => '部门',
				'fromid' => '平台会员ID',
				'name' => '名字',
				'email' => 'Email',
				'phone' => '电话',
				'telephone' => '座机',
				'address' => '联系地址',
				'department' => '部门',
				'position' => '职位',
				'sex' => '性别',
				'longitude' => '经度',
				'latitude' => '纬度',
				'location' => '位置',
				'display' => '显示', /*(0:自己,1：公共)*/
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
		$criteria->compare('groups_id',$this->groups_id,true);
		$criteria->compare('fromid',$this->fromid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('department',$this->department,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('display',$this->display);
		$criteria->compare('add_time',$this->add_time,true);
		$criteria->compare('add_us',$this->add_us,true);
		$criteria->compare('add_ip',$this->add_ip,true);
		$criteria->compare('modified_time',$this->modified_time,true);
		$criteria->compare('modified_us',$this->modified_us,true);
		$criteria->compare('modified_ip',$this->modified_ip,true);
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
    	$arr = parent::defaultScope();
    	$condition = array($arr['condition']);
    	// $condition[] = 'display>0';
    	$arr['condition'] = join(" AND ",$condition);
    	// echo 1;
    	return $arr;
    }
		//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave(true);
	    if(!$isok&&$result){
	        //添加数据时候
	        $arr = Tak::getOM();
	        if ( $this->isNewRecord ){
	        	$this->itemid = $arr['itemid'];
	        	$this->add_us = $arr['manageid'];
	        	$this->add_time = $arr['time'];
	        	$this->fromid = $arr['fromid']; 
	        }else{
	        	//修改数据时候
	        	$this->modified_us = $arr['manageid'];
	        	$this->modified_time = $arr['time'];
	        	$this->modified_ip = $arr['ip'];
	        }
	    }
	    return $result;
	}

	//保存数据后
	protected function afterSave(){
		parent::afterSave();
	}	

	//删除信息后
	protected function afterDelete(){
		parent::afterDelete();
	}	
}
