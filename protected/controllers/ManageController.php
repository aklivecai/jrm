<?php

class ManageController extends Controller
{

	protected $branchs = null;

	public function init()  
	{     
    		parent::init();
    		$this->primaryName = 'manageid';
    		$this->modelName = 'Manage';
    		$this->branchs = Permission::getList();
    		if (count($this->branchs)==0) {
    			$this->redirect(array('/permission/create'));
    		}
	  // Yii::app()->clientScript->registerCoreScript('jquery');
  //   	$cs = Yii::app()->getClientScript();
		// $cs->enableJavaScript = false;
	}
	public function getBranch($key){
		$result = isset($this->branchs[$key])?$this->branchs[$key]:'用户';
		return $result;
	}
	public function allowedActions()
	{
	 	return 'actionSelectById,actionSelect';
	}		

	private function getJurisdiction($id){
		$types = array(
			'0' =>'操作',
			'1' =>'任务',
			'2' =>'部门',
		);
		$sql = ' SELECT name,t1.type,description,t1.bizrule,t1.data,weight 
			FROM  {{rbac_authitem}} t1 
			LEFT JOIN {{rbac_authassignment}} t2 ON name=t2.itemname 
			LEFT JOIN {{rbac_rights}} t3 ON name=t3.itemname 
			WHERE 
				userid=:userid   
			ORDER 
				BY t1.type DESC, weight ASC
			';
		$sql = strtr($sql,array(':userid'=>$id));
		$data =  Tak::getDb('db')->createCommand($sql)->queryAll();

		$result = array();
		$crypt = new SysCrypt();
		foreach ($data as $key => $value) {
			$id = $crypt->encrypt($value['name']);
			$result[$id] = array('title'=>$value['description'],'type'=>$types[$value['type']]);
		}
		return $result;
	}
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$dataJurisdiction = $this->getJurisdiction($id);
		$this->render('view',array(
			'model'=>$model,
			'dataJurisdiction' =>$dataJurisdiction,
		));
	}

	public function actionIndex()
	{
		$criteria = array();
		if (!Tak::getAdmin()) {
		   	$criteria['condition'] = "fromid=".Tak::getFormid();
		 }   
		$dataProvider = new CActiveDataProvider('Manage',array(
			'pagination'=>array(
				'pageSize'=>20,
			),			
 			'criteria'=> $criteria,
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$m = $this->modelName;		
		if(isset($_POST[$m]))
		{
			$data = $_POST[$m];
			if ($data['branch']!=$model->branch) {
				$model->changeBranch();
			}
			$model->attributes = $data;  
			if($model->save()){
				$this->redirect($this->returnUrl?$this->returnUrl:array('view','id'=>$model->primaryKey));
			}
		}
		$this->render($this->templates['update'],array(
			'model'=>$model,
		));
	}	

	protected function getSelectOption($q,$not = false) 
	{
		$result = parent::getSelectOption($q);
		$result['data']['attributes'][] = 'user_nicename';
		if ($q) {
			$result['data']['criteria']->addSearchCondition('user_nicename',$q,true,'OR');
		}
		return $result;
	}	

}
