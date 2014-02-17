<?php

class ProductController extends Controller
{
	public function init()  
	{     
    		parent::init();
    		$this->modelName = 'Product';
	}

	public function actionSelect(){
		 $pageSize = Yii::app()->request->getQuery('page_limit',10);
		 $page = Yii::app()->request->getQuery('page',1);
		 $q = Yii::app()->request->getQuery('q','*');

		 $itemid = Yii::app()->request->getQuery('itemid','0');

		 $notitemid = Yii::app()->request->getQuery('not',false);
		 if(!is_numeric($itemid)){
		 	$itemid = '0';
		 }
		 $criteria = new CDbCriteria;
		 if ($q!='*') {
		 	$criteria->addSearchCondition('name',$q);
		 }
		 if ($itemid!='0') {
		 	$criteria->addCondition('itemid=:itemid');
		 	$criteria->params[':itemid']=$itemid;
		 }
		 if ($notitemid) {
		 	$notitemid = explode(",", $notitemid);
		 	$notitemid = array_filter($notitemid);
		 	$criteria->addNotInCondition("itemid",$notitemid);
		 }
		 $dataProvider = new JSonActiveDataProvider($this->modelName,array(
		 		'attributes' => array('itemid', 'name', 'material', 'spec','color','price'),
		 		'criteria' => $criteria,
				'sort'=>array(
					'defaultOrder'=>'name ASC', 
				),
				'pagination' => array( 
					'pageSize' => $pageSize
				), 	            
		 )); 
		 $rs = $dataProvider->getArrayCountData();
		 $this->writeData($dataProvider->getJsonData());
	}		
}
