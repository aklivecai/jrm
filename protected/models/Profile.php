<?php

/**
 * 这个模块来自表 "{{profile}}".
 *
 * 数据表的字段 '{{profile}}':
 * @property string $itemid
 * @property integer $sex
 * @property string $company
 * @property string $user_nicename
 * @property string $telephone
 * @property string $mobile
 * @property string $address
 * @property string $fax
 */
class Profile extends CActiveRecord
{
	
	/**
	 * @return string 数据表名字
	 */
	public function tableName()
	{
		return '{{profile}}';
	}

	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('company,user_nicename,address', 'required'),
			array('sex', 'numerical', 'integerOnly'=>true),
			array('itemid', 'length', 'max'=>25),
			array('company', 'length', 'max'=>100),
			array('user_nicename', 'length', 'max'=>64),
			array('telephone, mobile, fax', 'length', 'max'=>50),
			array('address', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.

			array('itemid, sex, company, user_nicename, telephone, mobile, address, fax', 'safe', 'on'=>'search'),

			array('telephone','cheTel'),
		);
	}
	public function cheTel($attribute,$params){
		$tel = $this->$attribute;
		if ($tel==''&&$this->mobile=='') {
			$this->addError($attribute,'电话和手机必须填写一个!');
		}
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
				'sex' => '性别', /*(0:不公开,1:男,2:女)*/
				'company' => '名称', /*(公司或者个人)*/
				'user_nicename' => '联系人',
				'telephone' => '电话',
				'mobile' => '手机',
				'address' => '地址',
				'fax' => '传真',
		);
	}

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;

		$criteria->compare('itemid',$this->itemid,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('user_nicename',$this->user_nicename,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('fax',$this->fax,true);
		return $cActive;
	}


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getOne($itemid=false){
		if (!$itemid) {
			$itemid = Tak::getManageid();
		}
		return self::model()->findByPk($itemid);
	}

	//默认继承的搜索条件
    public function defaultScope()
    {
    	$arr = parent::defaultScope();
    	$condition = array();
    	if(isset($arr['condition'])){
    		$condition[] = $arr['condition'];
    	}
    	// $condition[] = 'display>0';
    	$arr['condition'] = join(" AND ",$condition);
    	return $arr;
    }

	//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave(false);
	    if($result){
	    	$this->itemid = Tak::getManageid();
	    }
	    return $result;
	}

	//保存数据后
	protected function afterSave(){
		parent::afterSave();
		$m = Manage::model()->findByPk($this->itemid);
		$m->user_nicename = $this->company;
		$m->save();
		Tak::setFlash('操作成功！');
	}

	//删除信息后
	protected function afterDelete(){
		parent::afterDelete();
	}	
}
