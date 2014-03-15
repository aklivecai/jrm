<?php
class WarehouseController extends Controller {
    public $defaultAction = 'admin';
    public $modelName = 'Warehouse';
    public $returnUrl = array(
        "admin"
    );
    public function init() {
        parent::init();
    }
    public function loadModel($id, $isload = false) {
        if ($isload || $this->_model === null) {
            $m = $this->modelName;
            $m = $m::model();
            $m = $m->findByAttributes(array(
                'itemid' => $id
            ));
            if ($m === null) {
                $this->error();
            }
            $this->_model = $m;
        }
        return $this->_model;
    }
    public function actionAdmin($id = false) {
        $m = $this->modelName;
        $model = new $m;
        $data = $m::getDataProvider();
        $this->render('admin', array(
            'data' => $data,
            'id' => $id,
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
        $this->redirect(array(
            "admin"
        ));
    }
    
    public function actionListorder($id, $action) {
        $model = $this->loadModel($id);
        $m = null;
        if ($action == 'up') {
            $m = $model->getNext(true);
        } elseif ($action == 'dw') {
            $m = $model->getPrevious(true);
        }
        // Tak::KD($m,1);
        if ($m) {
            $m = $this->loadModel($m, true);
            $o = $m->listorder;
            $o1 = $model->listorder;
            if ($o1 == $o) {
                $o = $action == 'up' ? ($o + 1) : ($o - 1);
            }
            if ($o < 0) {
                $o = 0;
            }
            $m->listorder = $model->listorder;
            $m->save();
            $model->listorder = $o;
            $model->save();
        }
        $this->redirect(array(
            "admin",
            'id' => $id
        ));
    }
}
