<?php
class ClientelesController extends Controller
{
	public $defaultAction = 'index';
	public function init()  
	{     
    		parent::init();
    		$this->modelName = 'Clientele';
	}
	public function loadModel($id,$not=false)
	{
		if($this->_model===null)
		{
			$m = $this->modelName;
			$m = $m::model();
			$this->_model = $m->setGetCU()->findByPk($id);
			if($this->_model===null)
				$this->error();
		}
		return $this->_model;
	}

	public function actionIndex()
	{
		$m = $this->modelName;
		$model = new $m('search');
		$model->setGetCU();
		$model->unsetAttributes();
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}

		$this->render('index',array(
			'model'=>$model,
		));
	}	
	
	public function actionMove($id){
		$model = $this->loadModel($id);
		$uname = $model->iManage->user_nicename;
		$uid = $model->manageid;
		$m = $this->modelName;

		$mF = 'MovesForm';
		$modelF = new $mF;

		if(isset($_POST[$mF])){
			$modelF->attributes = $_POST[$mF];
			if($modelF->validate()){
				$model->manageid = $modelF->tMid;
				if($model->save()&&$model->move()){
					if ($this->isAjax) {
						exit;
					}else{
						$this->redirect(array('move','id'=>$model->primaryKey));
					}							
				}
			}
		}
		$this->render('move',array(
			'model' => $model,
			'uname' => $uname,
			'uid' => $uid,
			'modelF' => $modelF,
		));		
	}
	public function actionView($id)
	{
		$model = $this->loadModel($id);

		$strView = $this->templates['view'];

		$m = 'Contact';
		$mContact = new $m('search');
		$mContact->setGetCU();
		$arr = isset($_GET[$m])?$_GET[$m]:false;
		if($arr) {
			$arr['clienteleid'] = $model->itemid;
			$mContact->attributes = $arr;
		}else{
			$mContact->attributes = array('clienteleid'=>$model->itemid);
		}

		if($this->isAjax
			&&($arr||$_GET[$m.'_page'])){
			$strView = 'contact';
		}
		if($mContact->contact_time == 0){
			$mContact->contact_time = '';
		}

		$this->render($strView,array(
			'model' => $model,
			'mContact' => $mContact,
		));
	}	
}
