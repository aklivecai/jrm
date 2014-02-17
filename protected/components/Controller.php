<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends RController
{
	/**
	 * @var string the default layout for the controller view. Defaults to 'column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = 'column2';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */

	public $menu = array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

	public $_model = null;
	public $primaryName = 'itemid';
	public $modelName = '';

	public $isAjax = false;

	public $returnUrl = null;

	protected $dir = false;
	protected $templates = array('create'=>'create','update'=>'update','admin'=>'admin','view'=>'view','index'=>'index','preview'=>'_view','print'=>'print');

	protected $_manifest = null;

	public function init()  
	{  
 		parent::init();
    $this->isAjax  = Yii::app()->request->isAjaxRequest;
		if($this->isAjax){
			$this->_setLayout('//layouts/columnAjax');
			Yii::app()->clientScript->enableJavaScript = false;
		}else{
			// Yii::app()->bootstrap->register();
		}
		if ($this->dir) {
			$templates = $this->templates;
			foreach ($templates as $key => $value) {
				$templates[$key] = $this->dir.$value;
			}				
			$this->templates = $templates;
		}

		$this->returnUrl = Yii::app()->request->getParam('returnUrl',null);
	}	
	protected function _setLayout($layout='column2')
	{
		$this->layout = $layout;
	}	
	public function afterRender($view, &$output){
		if ($this->isAjax) {
			Yii::app()->clientScript->reset();
		}		
		parent::afterRender($view, $output);
	}	

	/**
	 * @return array action filters
	 */
	public function filters(){
		return array(
			'updateOwn + update', // Apply this filter only for the update action.
			'deleteOwn + delete', // Apply this filter only for the update action.
			'rights',
		);
	}

	public function allowedActions()
	{
	 	return 'views,error';
	}
	/**
	 * Filter method for checking whether the currently logged in user
	 * is the author of the post being accessed.
	 */
	public function filterUpdateOwn($filterChain){
		$obj = $this->loadModel($_GET['id']);
		// Remove the 'rights' filter if the user is updating an own post
		// and has the permission to do so.

		if(Yii::app()->user->checkAccess('UpdateOwn', array('userid'=>$obj->primaryKey)))
			$filterChain->removeAt(1);
		$filterChain->run();
	}	

	/**
	 * Filter method for checking whether the currently logged in user
	 * is the author of the post being accessed.
	 */
	public function filterDeleteOwn($filterChain){
		// $params=array('item'=>$model); // set params array for Rights' BizRule

		$obj = $this->loadModel($_GET['id']);
		if(Yii::app()->user->checkAccess('DeleteOwn', array('manageid'=>$obj->primaryKey)))
			$filterChain->removeAt(1);
		$filterChain->run();
	}	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel($id,$recycle=false)
	{
		if($this->_model===null)
		{
			$m = $this->modelName;
			$m = $m::model();
			if ($recycle) {
				$m->setRecycle();
			}
			$this->_model = $m->findByPk($id);
			if($this->_model===null)
				$this->error();
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Manage $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{

		$_tname = strtolower($this->modelName.'-form');

		if(isset($_POST['ajax']) && ($_POST['ajax']===$_tname)||$_POST['ajax']=='mod-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}	

	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}	

	public function actionView($id)
	{
		if (condition) {
			# code...
		}
		$this->render($this->templates['view'],array(
			'model' => $this->loadModel($id),
		));
	}

	public function actionViews($id)
	{
		$this->render('views',array(
			'model' => $this->loadModel($id),
		));
	}	
	public function actionPreview($id)
	{
		if(!$this->isAjax){
			$this->_setLayout('//layouts/columnPreview');
		}		
		$this->render($this->templates['preview'],array(
			'model' => $this->loadModel($id),
		));
	}	

	public function actionIndex()
	{
		$m = $this->modelName;
		$model = new $m('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}

		$this->render($this->templates['index'],array(
			'model'=>$model,
		));
	}	

	public function actionDelete($id)
	{
		$this->loadModel($id)->del();
		if(!isset($_GET['ajax']))
			$this->redirect(isset($this->returnUrl) ? $this->returnUrl : array('admin'));
	}	

	public function actionCreate()
	{
		$m = $this->modelName;
		$model = new $m;
		if(isset($_POST[$m]))
		{
			$this->performAjaxValidation($model);
			$model->attributes = $_POST[$m];
			if($model->save()){
				if ($this->returnUrl) {
					$this->redirect($this->returnUrl);
				}else{
					if ($this->isAjax) {
						if (isset($_POST['getItemid'])) {
							echo $model->primaryKey;
							exit;
						}
					}else{
						$this->redirect(array('view','id'=>$model->primaryKey));
					}					
				}				
			}
		}elseif(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$this->render($this->templates['create'],array(
			'model' => $model,
		));

	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$m = $this->modelName;
		
		if(isset($_POST[$m]))
		{
			$model->attributes=$_POST[$m];
			if($model->save()){
				$this->redirect($this->returnUrl?$this->returnUrl:array('view','id'=>$model->primaryKey));
			}
		}
		$this->render($this->templates['update'],array(
			'model'=>$model,
		));
	}

	public function actionRecycle(){
		$m = $this->modelName;
		$model = new $m('search');
		$model->sName .= Tk::g('Recycle');
		$model->setRecycle();
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$this->render($this->templates['admin'],array(
			'model'=>$model,
		));
	}

	
	public function actionRestore($id)
	{
		$model = $this->loadModel($id,true);
		$model->setRestore();
		$this->redirect(array('recycle'));
	}	

	// 彻底删除
	public function actionDel($id)
	{
		$this->loadModel($id,1)->delete();
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('recycle'));
	}	
	public function actionAdmin()
	{
		$m = $this->modelName;
		$model = new $m('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$this->render($this->templates['admin'],array(
			'model'=>$model,
		));
	}

	public function writeData($data){
		header('Content-Type: application/json');
		$callback = $_GET['callback'];
		$str = $callback.'('.$data.');';
		echo($str);
		exit;
	}
	
	public function actionPrint($id){
		$this->layout = '//layouts/colummPrint';
		$this->render($this->templates['print'],array(
			'model'=> $this->loadModel($id),
		));		
	}

	public function actionSelectById($id=false){
		if (!is_numeric($id)) {
			$message = Tk::g('Illegal operation');
			throw new CHttpException(403, $message);
			exit;
		}
		$m = $this->modelName;

		$msg = $this->loadModel($id);
		if ($msg!=null) {
			$str = json_encode($msg->attributes);
			$this->writeData('{data:['.$str.']}');
		}
		exit;
	}

	protected function getSelectOption($q,$not=false){
		$m = $this->modelName;
		$model = new $m;
		$result = array('name'=>$m,
			'data'=>array(
				'attributes' => array($model->primaryKey(), $model->linkName),
				'sort'=>array(
					'defaultOrder'=>'add_time DESC,'.$model->linkName.' ASC', 
				),
			)
		);
		$criteria = new CDbCriteria;
		if ($q) {			
			$criteria->addSearchCondition($model->linkName,$q,true);
		}
		if ($not) {
			$_not = str_split($not);
			if (is_array($_not)&&count($_not)>0) {
				$criteria->addNotInCondition($model->primaryKey(),$_not);	
			}
		}
		$result['data']['criteria'] = $criteria;
		return $result;
	}
	public function actionSelect($id=0,$page_limit=10,$q='*'){
		 (int)$id>0&&$this->actionSelectById($id);
		 $pageSize = (int)$page_limit>0?$page_limit:10;		 
		 $q = Yii::app()->request->getQuery('q',false);
		 $data = $this->getSelectOption($q);
		 $data['data']['pagination']['pageSize'] = $pageSize;
		 $dataProvider = new JSonActiveDataProvider($data['name'],$data['data']); 
		 $rs = $dataProvider->getArrayCountData();
		 $str = '{"total":'.$rs['totalItemCount'].',"link_template":"movies.json?q={search-term}&page_limit={results-per-page}&page={page-number}"';
		 $this->writeData($dataProvider->getJsonData());
	}
	public function actionGetTop($id,$top=5,$view='view'){
		
		$top = (int)$top>0?(int)$top:10;
		$msg = $this->loadModel($id);
		$tags = $msg->getNP(false,$top);
		
			$this->_setLayout('//layouts/columnAjax');
			Yii::app()->clientScript->enableJavaScript = false;

		$this->render('/chip/list-top',array(
			'model'=>$msg,
			'tags' => $tags,
			'view' => $view
		));		
	}

	protected function errorE($msg='非法操作'){
		$this->error(202,$msg);
	}
	protected function error($code=404,$msg='所请求的页面不存在。'){
		throw new CHttpException($code,$msg);
	}

}

