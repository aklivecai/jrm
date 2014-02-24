<?php
/*
	部门属下信息调整，查询
	只查询属下们的客户情况
*/

class Subordinate extends CActiveRecord
{
	public static $table = '{{subordinate}}';
	public function tableName()
	{
		$m = get_class($this);
		 return $m::$table;
	}	

	public static function getSubManageSql()
	{
		$sql = strtr('manageid IN (SELECT manageid FROM :tabl WHERE fromid=:fromid AND branch=:branch AND isbranch=0)',
			array(':tabl'=>Manage::$table
				, ':fromid' =>Tak::getFormid()
				, ':branch' =>Tak::getState('branch',-1)
			)
		);
		return $sql;		
	}

	//默认继承的搜索条件
    public function defaultScope()
    {
    	$condition = array();
    	$condition[] = 'fromid='.Tak::getFormid();
    	// $condition[] = 'manageid='.Tak::getManageid();
    	$arr['condition'] = join(" AND ",$condition);
    	return $arr;
    }
	public function rules()
	{
		return array(
			array('manageid,mid', 'required'),
		);
	}    
	public function attributeLabels()
	{
		return array(
			'fromid' => '平台会员ID',
			'manageid' => '经理',
			'mid' => '下属',
		);
	}	
	//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave(true);
	    if($result){
	    	$this->fromid = Tak::getFormid();
	    }

	}
	public static function getDb()
	{
		if(self::$db==null){
			self::$db = Tak::getDb('db');
		}
		return self::$db;
	}
	
	public static function getUsers()
	{
		$sql = strtr('SELECT manageid,user_nicename FROM :tabl WHERE fromid=:fromid AND branch=:branch AND isbranch=0',
			array(':tabl'=>Manage::$table
				, ':fromid' =>Tak::getFormid()
				, ':branch' =>Tak::getState('branch',53763899612601129)
			)
		);
		$tags = self::getDb()->createCommand($sql)->queryAll();

		$result = array();
		foreach ($tags as $key => $value) {
			$result[$value['manageid']] = $value['user_nicename'];
		}		
		return $result;
	}
}