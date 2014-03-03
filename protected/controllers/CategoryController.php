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
    
    protected function getLink($action = 'Admin') {
        return $this->createUrl($action, array(
            'm' => $this->m
        ));
    }
    
    protected function getType() {
        $m = Yii::app()->request->getQuery('m', false);
        if (!$m || !Category::getModel($m)) {
            $this->error();
        }
        return ucwords($m);
    }
    
    public function actionAdmin($id = false) {
        $m = $this->modelName;
        $model = new $m('search');
        $model->unsetAttributes();
        if (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $model->setModel($this->m);
        $this->render($this->templates['admin'], array(
            'model' => $model,
        ));
    }
    
    public function actionCreate() {
        $m = $this->modelName;
        $model = new $m;
        if (isset($_POST[$m])) {
            $this->performAjaxValidation($model);
            $model->attributes = $_POST[$m];
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
                        $this->redirect(array(
                            'view',
                            'id' => $model->primaryKey
                        ));
                    }
                }
            }
        } elseif (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $this->render($this->templates['create'], array(
            'model' => $model,
        ));
    }
    
    public function actionList($m, $id = false) {
        
        $this->render('list', array(
            'data' => $data,
        ));
    }
}
