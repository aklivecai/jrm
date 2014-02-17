<?php
class DefaultController extends JController
{

	public function init(){
		parent::init();
		$this->layout = 'column1';
		if (!Tak::isGuest()) {
			$this->checkAccess();
		}
	}
	public function allowedActions()
	{
	 	return 'login, error';
	}	

	public function actionIndex()
	{
		$this->render('index');		
	}

	/*检测权限*/
	private function checkAccess(){
		$uid = Tak::getManageid();
		$sql  = "SELECT itemname FROM {{rbac_authassignment}}  WHERE fromid = 1 AND userid=$uid ";
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();
		$reader = $command->query();
		$auth = Yii::app()->authManager;
		if (Tak::checkSuperuser()) {
			
		}
	}

	/*发送邮件*/
	public function actionEmail($email='',$itemid='')
	{

        $model = new MailForm();  
        $msg = array();
        if ($itemid>0) {
        	$msg = TestMemeber::model()->findByPk($itemid);
        	$model->to = $msg->email;

        	$model->from = Manage::model()->findByPk(Tak::getManageid())->user_nicename;
        }
          
        if (isset($_POST["MailForm"])){  
            $model->attributes=$_POST['MailForm'];  
              
            if($model->validate()) {     
				
				Yii::app()->mailer->CharSet = "UTF-8";  
				Yii::app()->mailer->IsHTML(true);
				Yii::app()->mailer->IsSMTP();
				Yii::app()->mailer->SMTPAuth = true;				
				Yii::app()->mailer->Port = '25';
				Yii::app()->mailer->Host = 'smtp.vip.163.com';
				Yii::app()->mailer->Username = '9juren002';
				Yii::app()->mailer->Password = 'juren002';

				Yii::app()->mailer->From = '9juren002@vip.163.com';

				// Yii::app()->mailer->Host = 'smtp.126.com';
				// Yii::app()->mailer->Username = 'z01926';
				// Yii::app()->mailer->Password = 'cb19880627';
				// Yii::app()->mailer->From = 'z01926@126.com';

				Yii::app()->mailer->FromName = $model->from;
				Yii::app()->mailer->AddReplyTo($model->from);
				Yii::app()->mailer->AddAddress($model->to);
				Yii::app()->mailer->Subject = ($model->subject);
				Yii::app()->mailer->Body =  ($model->body);

				// Tak::KD($model->body);
				$sendmail = Yii::app()->mailer->Send();            	
                if ($sendmail) {  
                	Yii::app()->user->setState('esubject',$model->subject);
                    Yii::app()->user->setFlash("success", "邮件发送成功! \n" );  
                    $this->refresh();  
                } else {  
                    Yii::app()->user->setFlash("failed", "邮件发送失败！ \n");  
                }  
            }  
        }  

        $model->subject = Yii::app()->user->getState('esubject','');

        $this->render('email',   
                array(  
                    'model' => $model,
                    'msg' => $msg,   
		));
	}

	/*登录系统*/
	public function actionLogin($itemid=false)
	{
		/*已经登录，返回上一页，没有就首页*/
		if (!Tak::isGuest()) {
			$this->redirect(Yii::app()->user->returnUrl);
		}
		
		$this->layout = 'column1';
		$model = new LoginForm;
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			$_POST['LoginForm']['fromid'] = 1;
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$fromid = $_POST['LoginForm']['fromid'];
			if ($fromid) {
				$fromid = Tak::getCryptKey($fromid);
			}else{
				$fromid = 1;
			}
			$fromid = 1;
			$_POST['LoginForm']['fromid'] = $fromid;
			$model->attributes = $_POST['LoginForm'];
			if($model->validate() && $model->login()){

				 $this->redirect(array('index'));
			}
		}
		if ($itemid) {
			$model->fromid = Tak::getCryptKey($itemid); 

		}else{
			$itemid = Tak::setCryptKey(1);
		}		
		$model->attributes = array('fromid'=>$itemid);
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/*退出登录*/
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(array('default/login'));
	}

	//修改密码
	public function actionChangepwd()
	{
		$model = new PasswdModifyForm();
		$m = 'PasswdModifyForm';
		$modifySuccess = false;
		if(isset($_POST[$m]))
		{
			$model->attributes = $_POST[$m];
			if($model->save()){
                $model->oldPasswd="";
                $model->passwd = "";
                $model->passwdConfirm="";
			}
		}
		$this->render('changepwd',array(
			'model' => $model,
			'modifySuccess' => $modifySuccess,
		));
	}			
}