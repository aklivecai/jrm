<?php
class CategoryController extends Controller {
    protected $m = null;
    protected $cateUrl = false;
    public function init() {
        $this->m = $this->getType();
        $this->modelName = 'Category';
        $this->primaryName = 'catid';
        $this->cateUrl = $this->getLink();
        parent::init();
    }
    public function loadModel($id,$isload=false) {
        if ($isload||$this->_model === null) {
            $m = $this->modelName;
            $m = $m::model();
            $m = $m->findByAttributes(array('catid'=>$id,'module'=>$this->m));
            if ($m === null) {
                $this->error();
            }
            $this->_model = $m;
        }
        return $this->_model;
    }

    public function filters() {
        return array(
            'rights',
        );
    }    

    public function allowedActions() {
        $result = array(parent::allowedActions(),'select');
        return join(',',$result);
    }        
    protected function getLink($action = 'Admin') {
        return $this->createUrl($action, array(
            'm' => $this->m
        ));
    }    
    protected function getType() {
        $m = Tak::getQuery('m', false);
        if (!$m || !Category::getModel($m)) {
            $this->error();
        }
        return ucwords($m);
    }
    
    public function actionAdmin($action=false,$id=false) {
        $m = $this->modelName;
        $model = new $m('search');
        $model->unsetAttributes();
        if (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $model->setModel($this->m);
        if ($action=='select') {
            $this->_setLayout('//layouts/columnWindows');
            $view = '_show';
        }else{
            $view = $this->templates['admin'];
        }
        $this->render($view,array(
            'model' => $model,
            'id' => $id,
            'action' => $action,
        ));
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $m = $this->modelName;
        if (isset($_POST[$m])) {
            $model->attributes = $_POST[$m];
             $model->setModel($this->m);
            if ($model->save()) {
                   $this->redirect($this->getLink());
            }
        }
        $this->render($this->templates['update'], array(
            'model' => $model,
        ));
    }    
    
    public function actionCreate() {
        $m = $this->modelName;
        $model = new $m('create');
        if (isset($_POST[$m])) {
            $this->performAjaxValidation($model);
            $model->attributes = $_POST[$m];
            $model->setModel($this->m);
            if ($model->save()) {
                if ($this->returnUrl) {
                    $this->redirect($this->returnUrl);
                } else {
                    if ($this->isAjax) {
                        if (isset($_POST['getItemid'])) {
                            echo $model->primaryKey;
                            exit;
                        }
                    } else {
                        $this->redirect($this->getLink());
                    }
                }
            }
        } elseif (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $this->render('create', array(
            'model' => $model,
        ));
    }
    
    public function actionDelete($id) {
        $model = $this->loadModel($id);
        $result = $model->del();
        if ($result) {
            // Tak::KD($model->getError('name'));
            echo $result;
        } else {

        }
        exit;
        if ($this->isAjax) {
            exit;
        }
        $this->redirect(array("admin"));
    }    

    public function actionList($m, $id = false) {
        $this->render('list', array(
            'data' => $data,
        ));
    }


}
