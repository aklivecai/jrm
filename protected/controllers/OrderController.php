<?php
class OrderController extends Controller {
    public function init() {
        parent::init();
        $this->modelName = 'Order';
    }
    public function actionUpdates($id) {
        $model = $this->loadModel($id);
        $m = $this->modelName;
        if (isset($_POST[$m])) {
            $model->attributes = $_POST[$m];
            if ($model->save()) {
            }
        }
        $temp = 'updates';
        if ($model->status == 999 || $model->status == 200) {
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
    
    public function creatOrder() {
        $arr = array();
        $_temp = Tak::getOM();
        $order_id = $_temp['itemid']; //Tak::fastUuid();
        $fromid = $_temp['fromid'];
        $manageid = $_temp['manageid'];
        $time = $_temp['time'];
        
        $nowDate = date("Y") . '-' . date("m") . '-' . date("d") . ' ' . mt_rand(10, 23) . ':' . mt_rand(10, 59) . ':' . mt_rand(10, 59);
        
        $arr['order'] = array(
            'itemid' => $order_id,
            'fromid' => $fromid,
            'manageid' => array_rand(array(
                '0',
                $manageid
            ) , 1) ,
        );
        
        $info = array(
            'itemid' => $order_id,
            'date_time' => strtotime("$nowDate +15 day") ,
            'pay_type' => mt_rand(1, 3) ,
            'packing' => mt_rand(1, 3) ,
            'taxes' => mt_rand(1, 2) ,
            'convey' => mt_rand(1, 2) ,
            'detype' => mt_rand(1, 2) ,
            'note' => '订单备注要求',
            'earnest' => mt_rand(10, 20) ,
            'few_day' => mt_rand(5, 15) ,
            'remaining_day' => mt_rand(5, 15) ,
            'delivery_before' => mt_rand(30, 60) ,
        );
        // 托运
        if ($info['detype'] == 2) {
            $info['area'] = mt_rand(1, 33);
            $info['address'] = '测试的收货地址';
            $info['people'] = '张三';
            $info['tel'] = '18688888888';
            $info['phone'] = '0700-6666666';
        } else {
            $info['purchasconsign'] = array_rand(array(
                '0' => '',
                '1' => '某某家具有限公司'
            ) , 1);
            if ($info['purchasconsign'] != '') {
                $info['contactphone'] = '18688888888';
            }
        }
        $arr['info'] = $info;
        
        $arr['product'] = $product = array(
            array(
                'order_id' => $order_id,
                'fromid' => $fromid,
                'fromid' => $fromid,
                'note' => '产品备注要求',
                'unit' => '平方',
                'name' => '实木椅',
                'model' => 'BL1410',
                'standard' => '20*120',
                'color' => '红色',
                'amount' => mt_rand(25, 100) ,
                'price' => mt_rand(1500, 2000) ,
            ) ,
            array(
                'order_id' => $order_id,
                'fromid' => $fromid,
                'fromid' => $fromid,
                'note' => '产品备注要求',
                'unit' => '平方',
                'name' => '大班椅',
                'model' => 'BLXX1410',
                'standard' => '35*220',
                'color' => '黑色',
                'amount' => mt_rand(9, 18) ,
                'price' => mt_rand(1500, 2000) ,
            ) ,
            array(
                'order_id' => $order_id,
                'fromid' => $fromid,
                'fromid' => $fromid,
                'note' => '产品备注要求',
                'unit' => '平方',
                'name' => '大班台',
                'model' => 'ALXX110',
                'standard' => '100*450',
                'color' => '大红',
                'amount' => mt_rand(25, 100) ,
                'price' => mt_rand(1500, 2000) ,
            ) ,
            
            array(
                'order_id' => $order_id,
                'fromid' => $fromid,
                'fromid' => $fromid,
                'note' => '产品备注要求',
                'unit' => '平方',
                'name' => '老板椅',
                'model' => 'CDXX110',
                'standard' => '80*350',
                'color' => '紫色',
                'amount' => mt_rand(5, 15) ,
                'price' => mt_rand(150, 999) ,
            ) ,
        );
        
        $arr['files'] = $files = array(
            array(
                'file_type' => '1',
                'file_path' => '/upload/test/order-test2.jpg'
            ) ,
            array(
                'file_type' => '1',
                'file_path' => '/upload/test/order-test1.jpg'
            ) ,
            array(
                'file_type' => '2',
                'file_path' => '/upload/test/test.zip'
            ) ,
            array(
                'file_type' => '3',
                'file_path' => '/upload/test/test.doc'
            ) ,
        );
        
        return $arr;
    }
    
    public function actionTestOrder() {
        $data = $this->creatOrder();
        $order = new Order;
        $info = new OrderInfo;
        $order->attributes = $data['order'];
        $info->attributes = $data['info'];
        if ($order->save() && $info->save()) {
            $products = $data['product'];
            $files = $data['files'];
            foreach (array_rand($products, mt_rand(2, 4)) as $v1) {
                $product = new OrderProduct;
                $product->attributes = $products[$v1];
                if ($product->save()) {
                    $pid = $product->itemid;
                    foreach (array_rand($files, mt_rand(2, 4)) as $v2) {
                        $file = new OrderFiles;
                        $t2 = $files[$v2];
                        $t2['action_id'] = $pid;
                        $file->attributes = $t2;
                        $file->save();
                    }
                }
            }
            $order->upTotal();
            $this->redirect(array(
                'updates',
                'id' => $order->primaryKey
            ));
        }
        $this->redirect(array(
            'admin'
        ));
    }
    
    public function actionCreate() {
        $m = $this->modelName;
        $model = new $m;
        
        if (isset($_POST[$m])) {
            $post = $_POST[$m];
            $post['fromid'] = Tak::getFormid();
            $post['manageid'] = Tak::getManageid();
            
            $model->attributes = $post;
            if ($model->save()) {
                if ($this->returnUrl) {
                    $this->redirect($this->returnUrl);
                } else {
                    if ($this->isAjax) {
                        if ($_POST['getItemid']) {
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
}
