<?php
class ToolsController extends JController {
    public function init() {
        parent::init();
        $this->menu = array(
            'manage' => array(
                'label' => Tk::g('Manage') ,
                'url' => array(
                    'Manage'
                )
            ) ,
            'database' => array(
                'label' => Tk::g('Database') ,
                'url' => array(
                    'Database'
                )
            ) ,
            'wipe' => array(
                'label' => Tk::g('Wipe') ,
                'url' => array(
                    'Wipe'
                )
            ) ,
        );
    }
    public function actionCreate() {
        throw new CHttpException(404, '所请求的页面不存在。');
    }
    public function actionUpdate($id) {
        throw new CHttpException(404, '所请求的页面不存在。');
    }
    public function actionDelete($id) {
        throw new CHttpException(404, '所请求的页面不存在。');
    }
    public function actionIndex() {
        $this->render('index', array());
    }
    public function actionWipe() {
        $model = new WipeForm;
        if (isset($_POST['WipeForm'])) {
            $sqls = $model->sqls;
            $command = Tak::getDb('db')->createCommand('');
            $arr = array(
                ':fromid' => 5112
            );            
            foreach ($sqls as $key => $value) {
                $command->text = $value;
                // $str = strtr($command->text,$arr);
                // $str = str_replace( 'DELETE' ,'SELECT 1' ,$str);
                // Tak::KD($str);
                // $rowCount = $command->execute($arr);
            }
        }
        $this->render('wipe', array(
            'model' => $model
        ));
    }
    public function actionAdmin() {
        $model = new TestLog('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['TestLog'])) $model->attributes = $_GET['TestLog'];
        
        $this->render('admin', array(
            'model' => $model,
        ));
    }
}
