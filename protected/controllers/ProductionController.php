<?php
/**
 *
 * @authors aklivecai (aklivecai@gmail.com)
 * @date    2014-06-14 17:17:05
 * @version $Id$
 */

class ProductionController extends Controller {
    public function init() {
        parent::init();
        $this->modelName = 'Production';
    }
    public function loadModel($id, $iserror = true) {
        if ($this->_model === null) {
            $id = $this->getSId($id);
            $m = $this->modelName;
            $m = $m::model();
            $m = $m->findByPk($id);
            if ($m === null && $iserror) {
                $this->error();
            }
            $this->_model = $m;
        }
        return $this->_model;
    }
    public function actionView($id) {
        $model = $this->loadModel($id);
        $this->_setLayout('//layouts/columnCost');
        /**查询核算中的产品**/
        $products = $model->getProducts();
        // Tak::KD($products);
        /**查询生产中的工序用时**/
        $process = $model->getProcess();
        $data = array();
        foreach ($process as $key => $value) {
            if (!isset($data[$value['workshop_id']])) {
                $data[$value['workshop_id']] = array(
                    'process' => array() ,
                    'product' => array() ,
                );
            }
            // $data[$value['workshop_id']]['product'][] = $value;
            $data[$value['workshop_id']]['process'][$value['process']] = $value;
        }
        foreach ($products as $key => $value) {
            if (isset($data[$value['workshop_id']])) {
                $data[$value['workshop_id']]['product'][] = $value;
            }
        }
        
        $_workshops = Workshop::getAll();
        $workshop_ids = array_keys($data);
        $workshops = array();
        foreach ($workshop_ids as $wid) {
            if (isset($_workshops[$wid])) {
                $workshops[$wid] = array();
                $process = array();
                $_process = $data[$wid]['process'];
                // Tak::KD($_workshops[$wid]['proc1ess']);
                foreach ($_workshops[$wid]['process'] as $v) {
                    if (isset($_process[$v['typename']])) {
                        $pro = array(
                            'name' => $v['typename'],
                            'value' => $_process[$v['typename']]['days'],
                            'itemid' => $_process[$v['typename']]['itemid'],
                        );
                        //工序的进度
                        $pro['list'] = ProductionProgresss::getListByProcessid($pro['itemid']);
                        $process[] = $pro;
                    }
                }
                // 排序好的工序
                $data[$wid]['process'] = $process;
                //车间的名字
                $data[$wid]['name'] = $_workshops[$wid]['typename'];
            }
        }
        $this->render('view', array(
            'model' => $model,
            'data' => $data,
        ));
    }
    public function actionIndex() {
        $m = $this->modelName;
        $model = new $m('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        $this->render($this->templates['index'], array(
            'model' => $model,
        ));
    }
    public function writeProduct($data) {
        $htmls = array(
            '<ol class="mov-products">'
        );
        foreach ($data as $key => $value) {
            $numbers = Tak::getNums($value['numbers']);
            $names = implode(' , ', array(
                $value['type'],
                $value['name'],
                $value['spec'],
                $value['color'],
            ));;
            // $numbers =Tak::tagNum($numbers,'label-info');
            $htmls[] = sprintf("<li>(%s) - 数量:%s</li>", $names, $numbers);
        }
        $htmls[] = '</ol>';
        $html = implode("", $htmls);
        if (count($data) > 3) {
            $html = sprintf('<div class="mov-product">%s<a class="not-printf more-product">&gt;&gt;更多</a><a class="not-printf more-product-hide">&gt;&gt;收起</a></div>', $html);
        }
        return $html;
    }
    
    public function getLink($id, $status) {
        // c
        $name = $status == 1 ? '进度跟进' : '查看详细';
        return JHtml::link($name, array(
            "View",
            "id" => $id
        ) , array(
            "class" => "target-win"
        ));
    }
}
