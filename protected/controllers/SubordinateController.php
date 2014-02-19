<?php
class SubordinateController extends Controller
{
	public $defaultAction = 'index';

	protected $users = array();
	public function init()  
	{     
    		parent::init();
    		$this->modelName = 'SubClientele';

		$sql = strtr('SELECT manageid,user_nicename FROM :tabl WHERE fromid=:fromid AND branch=:branch AND isbranch=0',
			array(':tabl'=>Manage::$table
				, ':fromid' =>Tak::getFormid()
				, ':branch' =>Tak::getState('branch',53763899612601129)
			)
		);

		
		$tags = Tak::getDb('db')->createCommand($sql)->queryAll();
		
		$arr = array();
		foreach ($tags as $key => $value) {
			$arr[$value['manageid']] = $value['user_nicename'];
		}
		
		$this->users = $arr;
    		// $this->users = Manage::model()->findAllByAttributes(array('branch' => Tak::getState('isbranch',-1)));
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
