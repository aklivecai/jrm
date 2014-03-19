<?php
class PermissionController extends Controller
{
	protected $tabs = null;
	private  $_authorizer = null;
	private $_mods = null;

	public $defaultAction='admin';

	public function init()  
	{     
		parent::init();
		$this->modelName = 'Permission';
		if (!$this->isAjax) {
			$this->tabs = Permission::getList();
		}
	}
	public function loadModel($id){
		if($this->_model===null)
		{
			$m = $this->modelName;
			$m = $m::model();
			$this->_model = $m->findByPk($id);
			if($this->_model===null)
				$this->error();
		}
		return $this->_model;		
	}
	public function getAuthorizer()
	{
		if( isset($this->_authorizer)===false ){
			$this->_authorizer = Jurisdiction::getAuthorizer();
		}
		return $this->_authorizer;
	}

	/**
	* @return string the item name or null if not set.
	*/
	public function getItemName()
	{
		return isset($_GET['name'])===true ? urldecode($_GET['name']) : null;
	}

	/**
	* Returns the data model based on the primary key given in the GET variable.
	* If the data model is not found, an HTTP exception will be raised.
	*/
	public function loadModelI($name)
	{
		if( $this->_mods===null )
		{
			$itemName = $name;
			if( $itemName!==null ){
				$authorizer  = $this->getAuthorizer();
				$this->_mods = $authorizer->authManager->getAuthItem($itemName);		
				$this->_mods = $authorizer->attachAuthItemBehavior($this->_mods);
			}
			if( $this->_mods===null )
				$this->error();
		}

		return $this->_mods;
	}
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		if(!isset($_GET['ajax']))
			$this->redirect(isset($this->returnUrl) ? $this->returnUrl : array('admin'));
	}		

	public function actionAdmin()
	{
		$len = count($this->tabs);

		if ($len==0) {
			$this->redirect(array('create'));
		}else{
			$t = array_keys($this->tabs);
			$id = $t[0];
			$this->redirect(array('view','id'=>$id));
		}
		$m = $this->modelName;
		$model = new $m('search');
		$this->render($this->templates['admin'],array(
			'model'=>$model,
		));		
	}

	public function actionPreview($id)
	{
		$model = $this->loadModel($id);
	 	$childDataProvider = $model->getChild();
	 	$data = $childDataProvider->getData();	
		$this->render($this->templates['preview'],array(
			'model' => $model,
			'data' => $data,
		));	 	
	}


	public function actionShow($child)
	{
		$child = urldecode($child);
		$crypt = new SysCrypt();
		$id = $crypt->decrypt($child);

		$itemName = $id;	
		$_model = $this->loadModel($id);
		$model = $this->loadModelI($id);
		$itemName = $id;	
		$type = Rights::getValidChildTypes($model->type);
		$exclude = array(Rights::module()->superuserName);
		$childSelectOptions = Rights::getParentAuthItemSelectOptions($model, $type, $exclude);		

		$parentDataProvider = new RAuthItemParentDataProvider($model);
		$childDataProvider = new RAuthItemChildDataProvider($model);

		$crypt = new SysCrypt();

		$this->render('show',array(
			'model' => $_model,
			'models'=>$model,
			'id' => $id,

			'crypt' => $crypt,
			'childSelectOptions'=>$childSelectOptions,
			'parentDataProvider'=>$parentDataProvider,
			'childDataProvider'=>$childDataProvider,
		));

	}

	public function actionView($id)
	{
		$_model = $this->loadModel($id);
		$model = $this->loadModelI($id);
		$itemName = $id;

		$crypt = new SysCrypt();

		$type = Rights::getValidChildTypes($model->type);
		$exclude = array(Rights::module()->superuserName);
		$childSelectOptions = Rights::getParentAuthItemSelectOptions($model, $type, $exclude);

		if( $childSelectOptions!==array() )
		{
			$childFormModel = new AuthChildForm();		
			// Child form is submitted and data is valid
			if( isset($_POST['AuthChildForm'])===true )
			{
				$childFormModel->attributes = $_POST['AuthChildForm'];
				if( $childFormModel->validate()===true )
				{
					$authorizer  = $this->getAuthorizer();

					$childFormModel->itemname = $crypt->decrypt($childFormModel->itemname);
					$authorizer->authManager->addItemChild($itemName, $childFormModel->itemname);
					$child = $authorizer->authManager->getAuthItem($childFormModel->itemname);
					$child = $authorizer->attachAuthItemBehavior($child);

					// Set a flash message for adding the child
					Tak::setFlash(
						Rights::t('core', 'Child :name added.', array(':name'=>$child->getNameText())),
						'success'
					);
					$this->redirect(array('view', 'id'=>urlencode($itemName)));
					
				}
			}else{}
		}
		else
		{
			$childFormModel = null;
		}

		// 取消部门选择
		if(isset($childSelectOptions['部门'])===true){
			unset($childSelectOptions['部门']);				
		}

		$_notArr = array(
			'Site.*',
			'Setting.*',
			'PostUpdateOwn',
			'Site.Logout',
			'Manage.Select',
			'AddressBook.View',
			'AddressBook.Index',
			'Clientele.Index',
			'ContactpPrson.*',
			'Contact.*',
			'Subordinate.*',
			'Events.*',
			'Message.*',
		);
		$notstr = '~'.implode('~',$_notArr).'~';
		// Tak::KD($notstr,1);

		foreach ($childSelectOptions as $key => $value) {
			if ($key=='部门') {
				unset($childSelectOptions[$key]);
			}else{
				$t = array();
				foreach ($value as $k1 => $v1) {
					if (strpos($notstr, '~'.$k1.'~')!==false) {
						unset($childSelectOptions[$key][$k1]);
					}else{
						$t[$crypt->encrypt($k1)]	 = $v1;
					}					
				}
				$childSelectOptions[$key] = $t;
			}
		}		
		$parentDataProvider = new RAuthItemParentDataProvider($_model);
		$childDataProvider = new RAuthItemChildDataProvider($_model);
		 $childDataProvider = $_model->getChild();
		 $data = $childDataProvider->getData();

		$this->render($this->templates['view'],array(
			'model' => $_model,
			'models'=>$model,
			'id' => $id,
			'crypt' => $crypt,

			'childFormModel'=>$childFormModel,
			'childSelectOptions'=>$childSelectOptions,
			
			'data'=> $data,
			
		));
	}

	/**
	* Removes a child from an authorization item.
	*/
	public function actionRemoveChild($id,$child)
	{
		$_model = $this->loadModel($id);
		$itemName = $id;
		$child = urldecode($child);
		$crypt = new SysCrypt();
		$childName = $crypt->decrypt($child);
		
		$authorizer  = $this->getAuthorizer();

			// Remove the child and load it
			$authorizer->authManager->removeItemChild($itemName, $childName);
			$child = $authorizer->authManager->getAuthItem($childName);
			$child = $authorizer->attachAuthItemBehavior($child);


			// Set a flash message for removing the child
					Tak::setFlash(
						Rights::t('core', 'Child :name removed.', array(':name'=>$child->getNameText())),
						'success'
					);
			// If AJAX request, we should not redirect the browser
			if( !$this->isAjax){
				$this->redirect(array('view', 'id'=>urlencode($id)));
			}else{

			}
	}	

}
