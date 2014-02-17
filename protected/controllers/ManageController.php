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

	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
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
