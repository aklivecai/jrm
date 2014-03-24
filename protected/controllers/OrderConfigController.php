<?php
// orderconfig
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
                    // Tak::KD($flow->getErrors());
                    
                }
            break;
            default:
                # code...
                
            break;
        }
        $flowTypes = TakType::geList($this->_taktype);
        $this->render('_config_flow', array(
            'flowTypes' => $flowTypes,
        ));
    }
    public function actionConfig() {
        $menu = array(
            'flow' => '订单流程',
            'setting' => '订单提示信息'
        );
        $flowTypes = TakType::geList($this->_taktype);
        $this->render('config', array(
            'flowTypes' => $flowTypes,
        ));
    }
}
