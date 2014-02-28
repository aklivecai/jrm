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
		$result = Jurisdiction::getJurisdiction($id);
		$branch = $this->_model->branch;
		if ($branch>0&&$result[$branch]) {
			$temp = $result[$branch];
			$temp['active'] = true;
			unset($result[$branch]);
			array_unshift($result,$temp);
		}
		return $result;
	}

	public function getJUrl()
	{
		return $this->createUrl('view',array('id'=>$this->_model->primaryKey)).'#userAssignments';
	}

	private function loadJurisdiction($name){
		$m = Jurisdiction::getObj($name);
			if($m===null)
				$this->error();
		return $m;
	}

	public function getItemName()
	{
		$name = isset($_GET['name'])===true ? trim($_GET['name']) : null;
		if ($name) {
			$crypt = new SysCrypt();
			$name = $crypt->decrypt($name);
		}
		return $name;
	}

	public function actionRevoke($id)
	{
		$model = $this->loadModel($id);
		$name = $this->getItemName();
		$m = $this->loadJurisdiction($name);
		Jurisdiction::revoke($model->primaryKey,$model->fromid,$name);
		Tak::setFlash(
			Tk::g('成功撤销  :name 「:title」',array(':name'=>$m['description'])),
			'success'
		);		
		$this->redirect($this->getJUrl());
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$dataJurisdiction = $this->getJurisdiction($id);

		$assignSelectOptions = Jurisdiction::getSelectOptions($model);

		if( $assignSelectOptions!==array() )
		{
			$crypt = new SysCrypt();
			$formModel = new AssignmentForm();
		    // Form is submitted and data is valid, redirect the user
		    if( isset($_POST['AssignmentForm'])===true )
			{
				$formModel->attributes = $_POST['AssignmentForm'];
				if( $formModel->validate()===true )
				{
					$name = $formModel->itemname;
					if (!is_numeric($name)&&$name!=='Admin') {
						$name = $crypt->decrypt($formModel->itemname);
					}
					$m = $this->loadJurisdiction($name);
					Jurisdiction::create($model->primaryKey,$model->fromid,$name);
					// Tak::setFlash(
					// 	Rights::t('core', 'Permission :name assigned.', array(':name'=>$m['description'])),
					// 	'success'
					// );
					$this->redirect($this->getJUrl());
				}
			}	

			$childSelectOptions = array();

			$ts = Permission::getList();
			$_arr = array();
			if ($assignSelectOptions['部门']['Admin']) {
				$_arr['Admin'] = $assignSelectOptions['部门']['Admin'];
			}			
			foreach ($ts as $key => $value) {
				$_arr[$crypt->encrypt($key)] = $value;
			}


			// $childSelectOptions['部门'] = $_arr;
			unset($assignSelectOptions['部门']);
			// Tak::KD($assignSelectOptions);
			foreach ($assignSelectOptions as $key => $value) {
				$t = array();
				foreach ($value as $k1 => $v1) {
					if ($k1=='Site.*'
						||$k1=='Setting.*'
						||$k1=='PostUpdateOwn'
						||$k1=='Site.Logout'
						||$k1=='AddressBook.View'
						||$k1=='Subordinate.*'
						||$k1=='ContactpPrson.*'
						||$k1=='Contact.*'
					) {
						unset($childSelectOptions[$key][$k1]);
					}else{
						$t[$crypt->encrypt($k1)]	 = $v1;
					}					
				}
				$childSelectOptions[$key] = $t;				
			}
			$assignSelectOptions = $childSelectOptions;

		} else {
		 	$formModel = null;
		}

		// $formSub = new Subordinate;

		if (isset($_POST['Subordinate'])) {
			
		}


		$this->render('view',array(
			'model'=>$model,
			'dataJurisdiction' =>$dataJurisdiction,
			'formModel'=>$formModel,
			'assignSelectOptions'=>$assignSelectOptions,			
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
		
		if (!$not&&isset($_GET['not'])) {
			$not = $_GET['not'];
		}
		$result = parent::getSelectOption($q,$not);
		$result['data']['attributes'][] = 'user_nicename';
		if ($q) {
			$result['data']['criteria']->addSearchCondition('user_nicename',$q,true,'OR');
		}
		return $result;
	}	

}
