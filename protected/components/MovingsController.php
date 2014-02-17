<?php

class MovingsController extends Controller
{
	public $type = 1;
	protected $typename = null;
	protected $cates = null;
	
	public function init()  
	{     
		$this->dir = '//movings/';
	    	parent::init();
	    	$this->modelName = 'Movings';
	    	$this->typename = $typename = Tak::getMovingsType($this->type);
	    	$_type = $typename.'-type';
	    	$_type = strtolower($_type);
	    	$this->cates = TakType::items($_type);
	}
	public function loadModel($id=false,$recycle=false)
	{
		if($this->_model===null)
		{
			if ($id) {
				$m = $this->modelName;			
				$model = $m::model();
				if ($recycle) {
					$model->setRecycle();
				}
				$this->_model = $model->findByPk($id);
			}
			if($this->_model===null){
					throw new CHttpException(404,'所请求的页面不存在。');
			}else{				
				$this->_model->initak($this->type);
			}
		}
		return $this->_model;
	}
	
	protected function afterAction($action){
		// if($action->id=='update'){
		// 	if ($this->_model!==null
		// 		&&$this->_model->time_stocked>0) {
		// 		$this->redirect(array('view','id'=>$this->_model->itemid));
		// 	}
		// }
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);
		if ($this->_model->time_stocked>0) {
			$this->redirect(array('view','id'=>$this->_model->itemid));
		}else{
			parent::actionUpdate($id);
		}
	}

	protected function beforeAction($action)
	{
		$m = $this->modelName;
		$strTypeName = 'typeid';
		$typeid = false;
		if ($action->id=='create') {
			$_arr = Yii::app()->request->getParam($m,false);
			if ($_arr) {
				$typeid = $_arr[$strTypeName]?$_arr[$strTypeName]:false; 
			}
			// Tak::KD($typeid,1);
			if (!$typeid||!$this->cates[$typeid]) {
				$typeid = key($this->cates);
			}
			if(isset($_POST[$m])){
				$_POST[$m][$strTypeName] = $typeid;
				$_POST[$m]['type'] = $this->type;
			}else{
				$_GET[$m][$strTypeName] = $typeid;
			}
		}
	    return true;
	} 	
	public function afterRender($view, &$output){
		parent::afterRender($view, $output);
	}	

	public function actionView($id,$affirm=false){
		$this->render($this->templates['view'],array(
			'model' => $this->loadModel($id),
			'affirm' =>$affirm,
		));
	}

	public function actionAdmin()
	{
		$m = $this->modelName;
		$model = new $m('search');
		$model->initak($this->type);
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$this->render($this->templates['admin'],array(
			'model'=>$model,
		));
	}	

	public function actionAffirm($id){
		$this->loadModel($id)->affirm();
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('view','id'=>$id));
	}
}
