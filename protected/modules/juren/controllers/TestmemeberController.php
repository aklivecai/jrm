<?php
class TestMemeberController extends JController {
    public $defaultAction = 'admin';
    public function init() {
        $this->modelName = 'Test9Memeber';
        parent::init();
        $this->menu = array(
            'admin' => array(
                'label' => Tk::g('Admin') ,
                'url' => array(
                    'admin'
                )
            ) ,
            'create' => array(
                'label' => Tk::g('Create') ,
                'url' => array(
                    'create'
                )
            ) ,
            'volume' => array(
                'label' => Tk::g('Volume') ,
                'url' => array(
                    'volume'
                )
            ) ,
            'import' => array(
                'label' => Tk::g('Import') ,
                'url' => array(
                    'import'
                )
            )
        );
    }
    
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        $m = $this->modelName;
        if (isset($_POST[$m])) {
            $model->attributes = $_POST[$m];
            if ($model->save()) {
                $manageid = $_POST[$m]['manageid'];
                if ($model->manageid != $manageid) {
                    $model->moveManage($manageid);
                }
                $this->redirect($this->returnUrl ? $this->returnUrl : array(
                    'view',
                    'id' => $model->primaryKey
                ));
            }
        }
        
        $label = $model->getAttributeLabel('manageid');
        $manages = array(
            '0' => $label
        );
        foreach (Manage::model()->findAllByAttributes(array(
            "fromid" => Tak::getFormid()
        )) as $record) {
            $manages[$record->primaryKey] = $record->user_name . ' - ' . $record->user_nicename;
        }
        $this->render('update', array(
            'model' => $model,
            'manages' => $manages,
        ));
    }

    public function actionVolume() {
        $model = new VolumeForm();
        if (isset($_POST['VolumeForm'])) {
            $model->attributes = $_POST['VolumeForm'];
            $time = $model->save();
            if ($time > 0) {
                $this->toSTime($time);
            }
        }
        $this->render('volume', array(
            'model' => $model,
        ));
    }
    public function actionImport() {
        $m = 'ImportForm';
        $model = new $m();
        
        if (isset($_POST[$m])) {
            $model->attributes = $_POST[$m];
            $model->file = CUploadedFile::getInstance($model, "file");
            $time = $model->save();
            if ($time > 0) {
                $this->toSTime($time);
            }
        }
        $this->render('import', array(
            'model' => $model,
        ));
    }
    public function toSTime($time) {
        $this->redirect(array(
            'admin',
            'TestMemeber[add_time]' => $time
        ));
    }
}
