<?php
class OrderController extends Controller {
    public function init() {
        parent::init();
        $this->modelName = 'Order';
    }
    public function getLink($id, $status) {
        $text = '马上处理';
        $arr = array(
            'id' => $id
        );
        $style = array();
        if ($status == 999 || $status == 200) {
            $text = '浏览';
            $url = $this->createUrl('view', $arr);
            $style['class'] = 'btn-success';
        } else {
            $url = $this->createUrl('updates', $arr);
            $style['class'] = 'btn-info';
        }
        if ($status == 10) {
            $url.= '#changeOrder';
        }
        $style['class'] = $style['class'] . ' btn xbtn-mini';
        return JHtml::link($text, $url, $style);
    }
    public function actionView($id) {
        $model = $this->loadModel($id);
        $this->render('views', array(
            'model' => $model,
        ));
    }
    public function actionIndex() {
        $this->redirect(array(
            'admin',
        ));
    }
    public function actionUpChange($id) {
        $model = $this->loadModel($id);
        if ($model->status != 10) {
            $this->redirect(array(
                'updates',
                'id' => $model->primaryKey
            ));
        }
        $m = 'OrderFlow';
        if (isset($_POST[$m])) {
            $orderFlow = new $m();
            $orderFlow->attributes = $_POST[$m];
            if ($orderFlow->status > 0) {
                $orderFlow->status = 11;
            } else {
                $orderFlow->status = 12;
            }
            if ($orderFlow->status == 11) {
                $oldFlow = OrderFlow::getOneLast($model->primaryKey);
            }
            $flowItemid = $model->saveStatus($orderFlow->status, $orderFlow->note);
            /*通过才进行更改订单产品,把最后一次的流程文件,描述的转移到追加的订单产品中*/
            if ($flowItemid && $orderFlow->status == 11) {
                $orderProduct = new OrderProduct();
                $orderProduct->attributes = array(
                    'itemid' => $oldFlow['itemid'],
                    'order_id' => $model->primaryKey,
                    'fromid' => $model->fromid,
                    'name' => $oldFlow['name'],
                    'note' => $oldFlow['note'],
                    'price' => 0,
                    'amount' => 0,
                );
                $orderProduct->save();
                if (isset($_POST['files'])) {
                    $files = $_POST['files'];
                    $t2 = array();
                    foreach ($files as $key => $value) {
                        $file = new OrderFiles;
                        $t2['file_path'] = $value;
                        $t2['action_id'] = $flowItemid;
                        $file->attributes = $t2;
                        $file->save();
                    }
                }
            }
        }
        
        $this->redirect(array(
            'updates',
            'id' => $model->primaryKey
        ));
    }
    public function actionUpdates($id) {
        $model = $this->loadModel($id);
        $temp = 'updates';
        if ($model->status == 999 || $model->status == 200 || $model->status == 10) {
            $temp = 'views';
        }
        $this->render($temp, array(
            'model' => $model,
        ));
    }
    public function actionStatus($id, $status) {
        $model = $this->loadModel($id);
        if (!$model->getState($status)) {
            $this->error();
        } elseif ($model->status == $status) {
            $this->redirect(array(
                'updates',
                'id' => $model->primaryKey
            ));
        }
        $model->saveStatus($status);
        $this->redirect($this->returnUrl ? $this->returnUrl : array(
            'updates',
            'id' => $model->primaryKey
        ));
    }
    
    public function actionFlowset($id) {
        $model = $this->loadModel($id);
        $m = 'OrderFlow';
        $flow = new $m;
        if (isset($_POST[$m])) {
            $_POST[$m]['order_id'] = $id;
            $flow->attributes = $_POST[$m];
            if ($flow->save()) {
                // Tak::KD($_POST[$m],1);
                if (isset($_POST['files'])) {
                    $files = $_POST['files'];
                    $t2 = array();
                    foreach ($files as $key => $value) {
                        $file = new OrderFiles;
                        $t2['file_path'] = $value;
                        $t2['action_id'] = $flow->itemid;
                        $file->attributes = $t2;
                        $file->save();
                    }
                }
            } else {
                Tak::KD($flow->getErrors() , 1);
            }
        }
        $this->redirect(array(
            'updates',
            'id' => $model->primaryKey
        ));
    }
}
