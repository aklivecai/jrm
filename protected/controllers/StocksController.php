<?php

class StocksController extends Controller
{
	public function init()  
	{     
    		parent::init();
    		$this->modelName = 'Stocks';
	}

	public function actionViewProduct($id){
		$model = Product::model()->findByPk($id);
			if($model===null)
				$this->error();

		$this->render('viewproduct',array(
			'model'=>$model,
		));			

	}

	public function actionIndex()
	{
		$m = 'Product';
		$model = new $m('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$this->render($this->templates['index'],array(
			'model'=>$model,
		));
	}

	public function actionAdmin()
	{
		$m = 'Product';
		$model = new $m('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$m])){
			$model->attributes = $_GET[$m] ;
		}
		$this->render($this->templates['admin'],array(
			'model'=>$model,
		));
	}	
}
