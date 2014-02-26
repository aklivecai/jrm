<?php
class CategoryController extends Controller
{
	protected $m = null;

	protected $cateUrl = false;

	public function init()  
	{  

		$m = $this->getType();
		$this->modelName = 'Category';
		$this->primaryName = 'catid';

		$this->cateUrl = $this->createUrl('Admin',array('m'=>$m));

 		parent::init();
	}

	private function getType()
	{
		$m = Yii::app()->request->getQuery('m',false);
		if (!$m||!Category::getModel($m)) {
			$this->error();
		}

		return ucwords($m);
	}

	public function actionAdmin($id=false)
	{

		$m = $this->modelName;
		$model = new $m('search');
		$model->unsetAttributes();
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$model->setModel($this->m);
		$this->render($this->templates['admin'],array(
			'model'=>$model,
		));
	}	

	public function actionCreate()
	{
		
	}

	public function actionList($m,$id=false)
	{
		
		$this->render('list',array(
			'data'=>$data,
		));
	}
}