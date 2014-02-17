<?php

/**
 * This is the model class for table "{{setting}}".
 *
 * The followings are the available columns in table '{{setting}}':
 * @property string $itemid
 * @property string $manageid
 * @property string $item_key
 * @property string $item_value
 */
class Setting extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{setting}}';
	}
	public function init()  
	{     
    	parent::init();
		$this->manageid = Tak::getManageid();
		$this->itemid = Tak::fastUuid();
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('itemid, manageid, item_value', 'required'),
			array('itemid, manageid', 'length', 'max'=>25),
			array('item_key', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, manageid, item_key, item_value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'itemid' => '编号',
			'manageid' => '会员ID',
			'item_key' => '键',
			'item_value' => '值',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('itemid',$this->itemid,true);
		$criteria->compare('manageid',$this->manageid,true);
		$criteria->compare('item_key',$this->item_key,true);
		$criteria->compare('item_value',$this->item_value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    public function scopes()
    {
        return array(
            'published'=>array(
                'condition'=>'manageid='.Tak::getManageid(),
            ),
            'public'=>array(
                'condition'=>'manageid='.$this->manageid,
            ),
        );
    }
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Setting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getSetings($key){
		Yii::app()->user->getState('last_login_time', Tak::timetodate($user->last_login_time));
	}
	
	public function takSave(){
		$this->deleteAll(" manageid=:manageid AND item_key=:item_key",array(':manageid'=>$this->manageid,":item_key"=>$this->item_key));
		return parent::save();
	}	

	public function getThemes(){
		$sql = "item_key LIKE 'themeSettings_%'";
		$list = $this->published()->findAll($sql);
		return $list;
	}
}
