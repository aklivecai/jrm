<?php
class OrderController extends Controller {
    public function init() {
        parent::init();
        $this->modelName = 'Order';
    }
    public function getLink($id, $status) {
        $id = $this->setSId($id);
        $text = Tk::g(array(
            'Order',
            'Admin'
        ));
        $arr = array(
            'id' => $id
        );
        $style = array();
        if ($status == 999 || $status == 200) {
            $text = Tk::g(array(
                'View',
                'Order'
            ));
            $url = $this->createUrl('view', $arr);
            $style['class'] = 'ibtn-success';
        } else {
            if ($status == 1) {
                $text = '审核订单';
            }
            $url = $this->createUrl('updates', $arr);
            $style['class'] = 'ibtn-info';
        }
        if ($status == 10) {
            $url.= '#changeOrder';
        }
        $style['class'] = $style['class'] . 'ibtn ixbtn-mini';
        $result = array(
            JHtml::link($text, $url, $style)
        );
        //存在生产模块
        if (Tak::isCost()) {
            $result[] = JHtml::link('生产管理', array(
                '/Production/View',
                'id' => $id
            ) , array(
                'class' => 'target-win ibtn ixbtn-mini',
                'target' => '_blank',
            ));
        }
        return implode(' | ', $result);
    }
    public function actionView($id) {
        
        $model = $this->loadModel($id);
        
        $orderReview = null;
        if ($model->isStatusOver()) {
            $orderReview = $model->getOrderReview();
        }
        
        $this->render('views', array(
            'model' => $model,
            'id' => $id,
            'orderReview' => $orderReview,
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
                'id' => $id
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
            'id' => $id,
        ));
    }
    public function actionUpdates($id) {
        $model = $this->loadModel($id);
        $temp = 'updates';
        if ($model->status == 999 || $model->status == 200 || $model->status == 10) {
            $temp = 'views';
        } else {
            if (!$model->serialid) {
                $model->serialid = Order::getSerialidMax();
            }
        }
        $this->render($temp, array(
            'model' => $model,
            'id' => $id,
        ));
    }
    public function actionUpData($id) {
        $model = $this->loadModel($id);
        $model->serialid = Tak::getPost('serialid', '');
        $model->cnote = Tak::getPost('cnote', '');
        $model->save();
        $this->redirect($this->returnUrl ? $this->returnUrl : array(
            'updates',
            'id' => $id
        ));
    }
    public function actionStatus($id, $status) {
        $model = $this->loadModel($id);
        if (!$model->getState($status)) {
            $this->error();
        } elseif ($model->status == $status) {
            $this->redirect(array(
                'updates',
                'id' => $id,
            ));
        }
        if ($status == 101) {
            $model->serialid = Tak::getPost('serialid', '');
            $model->cnote = Tak::getPost('cnote', '');
            $model->save();
            $note = '';
        } else {
            $note = Tak::getPost('note', '');
        }
        $model->saveStatus($status, $note);
        $this->redirect($this->returnUrl ? $this->returnUrl : array(
            'updates',
            'id' => $id
        ));
    }
    
    public function actionFlowset($id) {
        $model = $this->loadModel($id);
        $itemid = $this->getSId($id);
        $m = 'OrderFlow';
        $flow = new $m;
        if (isset($_POST[$m])) {
            $_POST[$m]['order_id'] = $itemid;
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
            'id' => $id,
        ));
    }
    
    public function actionUpdatePrice($id, $itemid, $value) {
        $model = $this->loadModel($id);
        if ($model->isStatusOver() || $model->status == 1) {
            exit;
        }
        if (!is_numeric($value) && $value <= 0) {
            $this->error();
        }
        $value = floatval($value);
        $orderProduct = OrderProduct::model()->findByPk($itemid);
        if ($orderProduct === null || $orderProduct->order_id != $model->itemid) {
            $this->error();
        }
        $orderProduct->price = $value;
        $orderProduct->sum = $orderProduct->price * $amount;
        if ($orderProduct->save()) {
            $model->upTotal();
        } else {
            print_r($orderProduct->getErrors());
        }
    }
    
    public function actionWindow() {
        $m = $this->modelName;
        $model = new $m('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $data = array();
        $this->_setLayout('columnWindows');
        $this->render('window', array(
            'data' => $data,
            'model' => $model,
        ));
    }
}
