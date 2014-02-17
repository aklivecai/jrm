<?php

class AddressGroupsController extends Controller
{
	public function init()  
	{     
    	$this->modelName = 'AddressGroups';
    	$this->primaryName = 'address_groups_id';
    	parent::init();
	}
	
	public function actionSortable()
	{
		// We only allow sorting via POST request
		if( Yii::app()->request->isPostRequest===true )
		{
			$this->_authorizer->authManager->updateItemWeight($_POST['result']);
		}
		else
		{
			throw new CHttpException(404,'所请求的页面不存在。');
		}
	}	
}
