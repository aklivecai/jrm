<?php
class MovingsController extends Controller {
    public $type = 1;
    public $modelName = 'Movings';
    protected $typename = null;
    protected $cates = null;
    
    protected $dir = '//movings/';
    
    public function init() {
        parent::init();
        $this->typename = $typename = Tak::getMovingsType($this->type);
        $_type = $typename . '-type';
        $_type = strtolower($_type);
        $this->cates = TakType::items($_type);
    }
    public function loadModel($id = false, $recycle = false) {
        $id = $this->getSId($id);
        if ($this->_model === null) {
            if ($id) {
                $m = $this->modelName;
                $model = $m::model();
                if ($recycle) {
                    $model->setRecycle();
                }
                $this->_model = $model->findByPk($id);
            }
            if ($this->_model === null) {
                $this->error();
            } else {
                $this->_model->initak($this->type);
            }
        }
        return $this->_model;
    }
    
    protected function afterAction($action) {
        if ($action->id == 'update') {
        }
    }
    
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        if ($this->_model->time_stocked > 0) {
            $this->redirect(array(
                'view',
                'id' => $this->_model->itemid
            ));
        } else {
            parent::actionUpdate($id);
        }
    }
    
    public function actionCreate() {
        $m = $this->modelName;
        $model = new $m('create');
        $model->initak($this->type);
        if (isset($_POST[$m])) {
            $model->attributes = $_POST[$m];
            $script = false;
            if ($model->save()) {
                $model->affirm();
                $script = sprintf('parent.window.location.href="%s";', $this->createUrl('view', array(
                    'id' => $model->primaryKey
                )));
            } else {
                Tak::KD($model->getErrors());
            }
            $this->_setLayout('//layouts/columnWindows');
            $this->render('/chip/iframe', array(
                'model' => $model,
                'script' => $script,
            ));
            exit;
        } elseif (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $this->render($this->templates['create'], array(
            'model' => $model,
        ));
    }
    
    public function actionView($id, $affirm = false) {
        $m = $this->loadModel($id);
        $this->render($this->templates['view'], array(
            'model' => $this->loadModel($id) ,
            'affirm' => $affirm,
        ));
    }
    
    public function actionAdmin() {
        $m = $this->modelName;
        $model = new $m('search');
        $model->initak($this->type);
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $this->render($this->templates['admin'], array(
            'model' => $model,
        ));
    }
    
    public function actionAffirm($id) {
        $this->loadModel($id)->affirm();
        if (!isset($_GET['ajax'])) $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
            'view',
            'id' => $id
        ));
    }
    
    public function writeProduct($data) {
        $htmls = array('<ol>');
        foreach ($data as $key => $value) {
            $numbers = $value['numbers'];
            if ($value['unit']) {
                $numbers.= sprintf('%s', $value['unit']);
            }
            // $numbers =Tak::tagNum($numbers,'label-info');
            $htmls[] = sprintf("<li>%s - %s</li>", $value['name'], $numbers);
        }
        $htmls[] = '</ol>';
        $html = implode("", $htmls);
        if (count($data) >3) {
            $html = sprintf('<div class="mov-product">%s<a class="not-printf more-product">&gt;&gt;更多</a><a class="not-printf more-product-hide">&gt;&gt;收起</a></div>', $html);
        }
        return $html;
    }
    public function actionPrint($id) {
        $this->layout = '//layouts/colummPrint';
        $str = $this->templates['print'];
        $fid = Tak::getFormid();
        if ($fid == 5139) {
            $dir = $fid;
            $str = sprintf("/print/%s/movings/print", $dir);
        }
        $this->render($str, array(
            'fid'=>$fid,
            'model' => $this->loadModel($id) ,
        ));
    }    
}

