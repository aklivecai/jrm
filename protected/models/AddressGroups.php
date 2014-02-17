<?php

/**
 * 这个模块来自表 "{{address_groups}}".
 *
 * 数据表的字段 '{{address_groups}}':
 * @property string $address_groups_id
 * @property string $fromid
 * @property string $name
 * @property integer $display
 * @property string $add_time
 * @property string $add_us
 * @property string $add_ip
 * @property string $modified_time
 * @property string $modified_us
 * @property string $modified_ip
 * @property string $note
 * @property integer $listorder
 * @property integer $status
 */
class AddressGroups extends ModuleRecord
{
	
	/**
	 * @return string 数据表名字
	 */
	public function tableName()
	{
		return '{{address_groups}}';
	}

	public function primaryKey()
	{
		return 'address_groups_id';
	} 	

	/**
	 * @return array validation rules for model attributes.字段校验的结果
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('display, listorder, status', 'numerical', 'integerOnly'=>true),
			array('address_groups_id, add_us, modified_us', 'length', 'max'=>25),
			array('fromid, add_time, add_ip, modified_time, modified_ip', 'length', 'max'=>10),
			array('name, note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('address_groups_id, fromid, name, display, add_time, add_us, add_ip, modified_time, modified_us, modified_ip, note, listorder, status', 'safe', 'on'=>'search'),
			array('name','authenticate'),
		);
	}
	/**
	 * 检验用户名是否重复
	 */
	public function authenticate($attribute,$params)
	{
		if ($this->primaryKey>0) {
			return ;
		}
		$sql = " SELECT COUNT(*) FROM :table WHERE fromid = :fromid
		     AND LOWER(name)=':name' ";	
		$arr = array(':name' => strtolower($this->name));
		$arr[':fromid'] = $this->fromid?$this->fromid:Tak::getFormid();	 
		$arr[':table'] = $this->tableName();	 
		$sql = strtr($sql,$arr);
		$query = Yii::app()->db->createCommand($sql);
		$count = $query->queryScalar();	
		if ($count>0) {
			$this->addError('name','组名称有重复');
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
				'address_groups_id' => '编号',
				'fromid' => '平台会员ID',
				'name' => '部门',
				'display' => '显示', /*(0:自己,1：公共)*/
				'add_time' => '添加时间',
				'add_us' => '添加人',
				'add_ip' => '添加IP',
				'modified_time' => '修改时间',
				'modified_us' => '修改人',
				'modified_ip' => '修改IP',
				'note' => '备注',
				'listorder' => '排序',
				'status' => '状态', /*(0:回收站,1:正常)*/
		);
	}

	public function search()
	{
		$cActive = parent::search();
		$criteria = $cActive->criteria;

		$criteria->compare('address_groups_id',$this->address_groups_id,true);
		$criteria->compare('fromid',$this->fromid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('display',$this->display);
		$criteria->compare('add_time',$this->add_time,true);
		$criteria->compare('add_us',$this->add_us,true);
		$criteria->compare('add_ip',$this->add_ip,true);
		$criteria->compare('modified_time',$this->modified_time,true);
		$criteria->compare('modified_us',$this->modified_us,true);
		$criteria->compare('modified_ip',$this->modified_ip,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('listorder',$this->listorder);
		$criteria->compare('status',$this->status);
		return $cActive;
	}
		//保存数据前
	protected function beforeSave(){
	    $result = parent::beforeSave();
	    return $result;
	}

	public function getList($display=false){
		$m = $this->findAll(array(
		    'select'=>'address_groups_id,name',
		    'condition'=>$display?'display>0':'',
		));
		$items = array();
		$items= CHtml::listData($m, 'address_groups_id', 'name');
		return $items;
	}
	public function getLink($itemid=false,$action='view')
	{
		$markup = CHtml::link($this->name, array(
			'addressGroups/update',
			'id'=>urlencode($this->address_groups_id),
		));
		// $markup .= $this->sortableId();
		return $markup;
	}
	public function getLinkAddress()
	{
		$markup = CHtml::link($this->name, array(
			'addressBook/admin',
			'id'=>urlencode($this->address_groups_id),
		));
		// $markup .= $this->sortableId();
		return $markup;
	}
	public function sortableId()
	{
	 	return ' ';
	}	

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	function del(){
		$this->Delete();
		return true;
	}	

	//默认继承的搜索条件
    public function defaultScope()
    {
    	
    	$arr = parent::defaultScope();
    	$condition = array($arr['condition']);
    	// $condition[] = 'display>0';
    	$arr['condition'] = join(" AND ",$condition);
    	$arr['order'] = ' listorder DESC ';
    	return $arr;
    }

	public function updateItemWeight($result)
	{
		foreach( $result as $weight=>$itemname )
		{
			$sql = "SELECT COUNT(*) FROM {$this->rightsTable}
				WHERE itemname=:itemname";
			$command = $this->db->createCommand($sql);
			$command->bindValue(':itemname', $itemname);

			// Check if the item already has a weight.
			if( $command->queryScalar()>0 )
			{
				$sql = "UPDATE {$this->rightsTable}
					SET weight=:weight
					WHERE itemname=:itemname";
				$command = $this->db->createCommand($sql);
				$command->bindValue(':weight', $weight);
				$command->bindValue(':itemname', $itemname);
				$command->execute();
			}
		}
	}
}
