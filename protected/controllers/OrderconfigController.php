<?php
class OrderConfigController extends Controller {
    public $defaultAction = 'config';
    public function init() {
        parent::init();
    }
    private $_taktype = 'order-flow';
    public function actionFlow($act) {
        $flow = new TakType;
        switch ($act) {
            case 'delete':
                $itemid = Tak::getParam('itemid');
                if ($itemid) {
                    $flow->deleteAllByAttributes(array(
                        'typeid' => $itemid
                    ));
                }
            break;
            case 'update':
                if (isset($_POST['flow']) && is_array($_POST['flow'])) {
                    foreach ($_POST['flow'] as $key => $value) {
                        $msg = $flow->getObj($key, $this->_taktype);
                        if ($msg != null) {
                            $msg->typename = $value;
                            $msg->save();
                        }
                    }
                }
            break;
            case 'order':
                if (isset($_POST['flow']) && is_array($_POST['flow'])) {
                    $icount = count($_POST['flow']);
                    foreach ($_POST['flow'] as $key => $value) {
                        $icount--;
                        $msg = $flow->getObj($key, $this->_taktype);
                        if ($msg != null) {
                            $msg->listorder = $icount;
                            $msg->save();
                        }
                    }
                }
            break;
            case 'create':
                if (isset($_POST['flow'])) {
                    $arr = $_POST['flow'];
                    // $flow->typename = $arr['typename'];
                    $flow->item = $this->_taktype;
                    $flow->attributes = $arr;
                    // Tak::KD($flow->getAttributes(),1);
                    $flow->save();
                    /*Tak::KD($flow->getErrors());*/
                }
            break;
            default:
            break;
        }
        $flowTypes = TakType::geList($this->_taktype);
        $this->render('_config_flow', array(
            'flowTypes' => $flowTypes,
        ));
    }
    public function tab() {
        $menu = array(
            'config' => array(
                'label' => Tk::g('Order Flow') ,
                'url' => array(
                    'config'
                ) ,
            ) ,
            'note' => array(
                'label' => Tk::g('Order Note') ,
                'url' => array(
                    'note'
                ) ,
            ) ,
            'alipay' => array(
                'label' => Tk::g('Alipay') ,
                'url' => array(
                    'alipay'
                ) ,
            ) ,
        );
        $id = $this->getAction()->id;
        if (strpos($id, 'alipay') !== false) {
            $id = 'alipay';
        }
        if (isset($menu[$id])) {
            $menu[$id]['active'] = true;
        }
        
        $this->renderPartial('_tab', array(
            'tabs' => $menu,
        ));
    }
    public function actionConfig() {
        $flowTypes = TakType::geList($this->_taktype);
        $this->render('config', array(
            'flowTypes' => $flowTypes,
        ));
    }
    public function actionNote() {
        $m = 'Setting';
        $model = OrderConfig::getNote();
        if (isset($_POST[$m]) && isset($_POST[$m]['item_value'])) {
            $data = $_POST[$m]['item_value'];
            $data = Tak::uhtml($data);
            $model->saveDefault(array(
                'item_value' => $data
            ));
        }
        $this->render('note', array(
            'model' => $model
        ));
    }
    public function actionAlipay() {
        $tags = OrderConfig::getListAlipay();
        $this->render('alipay', array(
            'tags' => $tags
        ));
    }
    private function getAlipay($id) {
        $m = 'Info';
        $model = $m::getOne($id, true);
        if ($model == null) {
            $this->error();
        }
        return $model;
    }
    public function actionDeletedAlipay($id) {
        $model = $this->getAlipay($id)->delete();
        $this->redirect($this->createUrl('alipay'));
    }
    public function actionCreateAlipay($id = false) {
        $m = 'Info';
        if ($id) {
            $model = $this->getAlipay($id);
        } else {
            $model = new $m;
        }
        if (isset($_POST[$m]) && $model) {
            $model->attributes = $_POST[$m];
            if (isset($_POST['tak_content'])) {
                $model->content = Tak::uhtml($_POST['tak_content']);
                $model->setIsContent(true);
            }
            $model->type = 'order-alipay';
            $model->save();
            $this->redirect($this->createUrl('alipay'));
        }
        $this->render('alipay_form', array(
            'model' => $model
        ));
    }
}
