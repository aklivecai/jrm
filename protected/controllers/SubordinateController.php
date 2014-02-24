<?php
class SubordinateController extends Controller
{
	public $defaultAction = 'index';
	protected $users = array();
	protected $modes = array();


	public function init()  
	{     
    		parent::init();
    		$this->modelName = 'SubClientele';
		$this->users = Subordinate::getUsers();
    		// $this->users = Manage::model()->findAllByAttributes(array('branch' => Tak::getState('isbranch',-1)));
		// Tak::KD($this->users);
	}

	/**
	 * [loadModel description]
	 * @param  [type]  $id     [description]
	 * @param  boolean $m      [模块]
	 * @param  boolean $isload [是否保存加载]
	 * @return [type]          [返回查找的信息]
	 */
	public function loadModel($id,$m=false,$isload=false)
	{
		if (!$m) {
			$m = $this->modelName;
		}
		if(!isset($this->modes[$m])||$isload)
		{
			$model = $m::model();
			$model = $model->setGetCU()->findByPk($id);
			$model->setGetCU();
			if($model===null)
				$this->error();
			$this->modes[$m] = $model;
		}
		return $this->modes[$m];	

	}

	public function actionIndex()
	{
		$tags = null;
	}

	public function actionClienteles()
	{
		$m = 'SubClientele';
		$model = new $m('search');
		$model->setGetCU();
		$model->unsetAttributes();
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}

		$this->render('clienteles',array(
			'model'=>$model,
		));
	}	

	public function actionSelectById($id=false){
		$m = $this->getM();
		$_tempname  = $this->modelName;
		$this->modelName = 'Sub'.$m;
		$result = parent::actionSelectById($id);
		$this->modelName = $_tempname;
	}

	private function getM(){
		$m = Yii::app()->request->getQuery('get',false);
		$m = strtolower($m);
		$m = ucwords($m);
		$_models = array('Manage'=>1,'Clientele'=>2);
		if (!isset($_models[$m])) {
			exit;
		}		
		return $m;			
	}

	protected function getSelectOption($q,$not=false){
		$m = $this->getM();

		$_tempname  = $this->modelName;
		$this->modelName = 'Sub'.$m;

		$result = parent::getSelectOption($q,$not);

		$this->modelName = $_tempname;

		$criteria = $result['data']['criteria'];
		
		if ($m==='Manage') {
			$result['data']['attributes'][] = 'user_nicename';
			if ($q) {
				$criteria->addSearchCondition('user_nicename',$q,true,'OR');
			}				
		}
			
		$result['data']['criteria'] = $criteria;
		return $result;
	}

	public function actionClienteleMove(){
		$m = 'MovesForm';
		$model = new $m;
		if(isset($_POST[$m])){
			$model->attributes = $_POST[$m];
			if($model->validate()){
				foreach (array($model->fMid,$model->tMid) as  $value) {
					$this->loadModel($value,'SubManage',true);
				}
				$arr = $model->moveClienteles();
				if ($arr&&count($arr)>0) {
					$str = '成功转移 <br />客户 <span class="red">:c</span> ,<br /> 联系人<span class="red">:cp</span>, <br />联系记录<span class="red">:cc</span>';
					$str = strtr($str,$arr);
					Tak::msg('',$str);
				}
			}
		}
		$this->render('clientelesmove',array(
			'model' => $model,
		));		
	}
	
	public function actionClientelesView($id)
	{
		$model = $this->loadModel($id,'SubClientele');
		$strView = 'clientelesview';
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
