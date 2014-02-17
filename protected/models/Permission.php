<?php
class Permission extends CActiveRecord
{
	public $name;
	public $description;
	public $type = 2;
	public $bizRule = '';
	public $data = null;

	public $fromid;

	public function tableName()
	{
		return '{{rbac_authitem}}';
	}
	private function getFid()
	{
		return Tak::getFormid();
	}
	public function gettitle(){
		return $this->description;
	}
	public function primaryKey()
	{
		return 'name';
	}
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}	
	public function rules()
	{
		return array(
			array('description','required'),
			array('description','checkRepetition'),
		);
	}
	/**
	 * 检验重复
	 */
	public function checkRepetition($attribute,$params)
	{
		$sql = array("LOWER(:col)=:val AND fromid=':fromid' ");
		$arr = array(
			':col' => $attribute,
			':fromid' => $this->getFid(),
		);
		if ($this->name<>'') {
			$sql[] = ':ikey<>:itemid';
			$arr[':ikey'] = 'name';
			$arr[':itemid'] = $this->name;
		}
		$sql = join(' AND ',$sql);
		$sql = strtr($sql,$arr);
		$m = $this->find($sql,array(':val'=>strtolower($this->$attribute)));
		
		if($m!=null){
			$err = $this->getAttributeLabel($attribute).' 已经存在 :';
			$this->addError($attribute,$err);
		}
	}		
	public function attributeLabels()
	{
		return array(
			'name'			=> "部门",
			'description'	=> "部门",
			'bizRule'		=> "业务规则",
			'data'			=> "数据",
		);
	}
	public function defaultScope()
	{
		$arr = array();
		return $arr;
	}
	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('fromid',$this->getFid());
		$criteria->compare('description',$this->description,true);
		$criteria->order = ' name ASC';
		$criteria->select = 'name,description';
		return new CActiveDataProvider($this, array(
			'criteria'=> $criteria,
		));		
	}



	protected function beforeSave()
	{
		$result = parent::beforeSave();
		if($result){
			if ( $this->isNewRecord ){
				$this->name = Tak::fastUuid();
			}			
			$this->fromid = $this->getFid();
		}
		return $result;
	}
	protected function afterDelete(){
		parent::afterDelete();
		$sql = "DELETE FROM {{rbac_authitemchild}} WHERE parent=:parent";
		$arr = array(
			':parent'=>$this->name,
		);		
		Yii::app()->db->createCommand(strtr($sql,$arr))->query();
	}	

	public static function getList()
	{
		$model = new self('search');
		$tags = $model->search()->getData();
		$reulst = array();
		foreach ($tags as $key => $value) {
			$reulst[$value['name']] = $value['description'];
		}
		return $reulst;
	}

	public function getChild(){
		$arr = array(
			':parent'=>$this->name,
			':fromid'=>$this->fromid,
		);
		$sql = "SELECT count(`parent`) FROM {{rbac_authitemchild}} WHERE `parent`=:parent";
		$count=Yii::app()->db->createCommand(strtr($sql,$arr))->queryScalar();
		$sql = 'SELECT C.*,P.* FROM {{rbac_authitemchild}} C,{{rbac_authitem}} P WHERE  C.child = P.name AND C.`parent`=:parent ORDER BY P.type DESC,P.description ASC';
		$dataProvider=new CSqlDataProvider(strtr($sql,$arr), array(
		     'keyField' => 'child',
		    'totalItemCount'=>$count,
		    'sort'=>array(
		        'attributes'=>array(
		             'parent', 'child',
		        ),
		    ),
		    'pagination'=>array(
		        'pageSize'=>100,
		    ),
		));		

		return $dataProvider;
	}
}