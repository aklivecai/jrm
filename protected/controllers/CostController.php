<?php
class CostController extends Controller {
    public $layout = 'columnCost';
    public function init() {
        parent::init();
        $this->modelName = 'Cost';
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
        $result = array();
        $result[] = JHtml::link('核算详细', array(
            "View",
            "id" => $id
        ) , array(
            "class" => "target-win"
        ));
        
        if ($status == 2) {
            $result[] = JHtml::link('确认生产', array(
                "Production",
                "id" => $id
            ) , array(
                "class" => "target-win"
            ));
        } elseif ($status == 3) {
            $result[] = JHtml::link('查看生产进度', array(
                "/Production/View",
                "id" => $id
            ) , array(
                "class" => "target-win"
            ));
        }
        
        return implode(' | ', $result);
    }
    public function actionIndex($orderid = 0) {
        $this->_setLayout('//layouts/column2');
        $m = $this->modelName;
        $model = new $m('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET[$m])) {
            $model->attributes = $_GET[$m];
        }
        /**订单转入**/
        if ($orderid > 0 && $model->isOrder($orderid)) {
            $order_id = $orderid;
        } else {
            $order_id = false;
        }
        $this->render($this->templates['index'], array(
            'model' => $model,
            'order_id' => $order_id,
        ));
    }
    public function actionView($id = 0) {
        $model = $this->loadModel($id);
        $this->render('view', array(
            'model' => $model,
        ));
    }
    public function actionProduction($id) {
        $model = $this->loadModel($id);
        $id = $model->itemid;
        // 非订单过来的，
        if ($model->status <= 1) {
            $this->redirect(array(
                'view',
                'id' => $this->setSId($id) ,
            ));
        } elseif ($model->status == 3) {
            //已经生成生产单了
            $this->redirect(array(
                '/Production/view',
                'id' => $this->setSId($id) ,
            ));
        }
        // 查询所有车间
        $workshops = Workshop::getAll();
        //查询核算编号的所有产品
        $produts = $model->getProducts();
        $m = 'M';
        if (isset($_POST[$m]) && is_array($_POST[$m]) && count($produts) == count($_POST[$m])) {
            $data = $_POST[$m];
            $ppday = new ProductionProductDays('create');
            $ppday->production_id = $id;
            $errors = array();
            $_temps = array();
            foreach ($produts as $key => $value) {
                $_temps[$value['itemid']] = $value;
            }
            $produts = $_temps;
            foreach ($data as $product_id => $product) {
                if (
                //存在的产品
                isset($produts[$product_id])
                //存在的车间
                 && isset($workshops[$product['workshop']])
                //有传递工序数据过来
                 && is_array($product['process']) && count($product['process']) > 0) {
                    $dataprocess = $workshops[$product['workshop']]['process'];
                    foreach ($product['process'] as $proid => $days) {
                        //存在的车间工序
                        if (isset($dataprocess[$proid])
                        //工序用时大于０
                         && is_numeric($days) && $days > 0) {
                            $ppday->setIsNewRecord(true);
                            $ppday->product_id = $product_id;
                            $ppday->workshop_id = $product['workshop'];
                            $ppday->days = $days;
                            $ppday->process = $dataprocess[$proid]['typename'];
                            if ($ppday->validate() && $ppday->save()) {
                                $ppdays[] = $ppday;
                            } else {
                                Tak::KD($ppday->getErrors());
                                $errors = true;
                            }
                        } else {
                            $errors = true;
                        }
                    }
                } else {
                    $errors = true;
                }
            }
            /**有错误，删除刚刚插入的信息**/
            if ($errors) {
                ProductionProductDays::model()->deleteAllByAttributes(array(
                    'fromid' => Ak::getFormid() ,
                    'production_id' => $id,
                ));
            } else {
                /*新建生产单*/
                $production = new Production('create');
                $production->itemid = $id;
                $production->name = sprintf("%s-订单", $id);
                $production->company = Order::model()->findByPk($id)->company;
                $production->save();
                //更新汇总车间的工序的用时
                $production->upPdays();
                //更新成本合算，状态为已经生成生产单
                $model->upProduction();
                
                $this->redirect(array(
                    '/Production/view',
                    'id' => $this->setSId($id) ,
                ));
            }
        }
        foreach ($produts as $key => $value) {
            $produts[$key]['numbers'] = Tak::getNums($value['numbers']);
        }
        foreach ($workshops as $key => $value) {
            if (isset($value['process'])) {
                $workshops[$key]['process'] = array_values($value['process']);
            } else {
                $workshops[$key]['process'] = array();
            }
        }
        $this->render('production', array(
            'model' => $model,
            'produts' => $produts,
            'workshops' => array_values($workshops) ,
        ));
    }
    private function getFname($data) {
        $result = "M";
        !is_array($data) && $data = array(
            $data
        );
        foreach ($data as $key => $value) {
            $result.= sprintf("[%s]", $value);
        }
        return $result;
    }
    
    private function saveInfo($cost_id, $products) {
        $product_id = $cost_id;
        $errors = array();
        /**添加产品**/
        if ($products) {
            foreach ($products as $pkey => $product) {
                $product_id = Tak::numAdd($product_id, 2);
                $cprodut = new CostProduct('create');
                $cprodut->attributes = $product;
                $cprodut->cost_id = $cost_id;
                $cprodut->itemid = $product_id;
                /**验证无法通过**/
                /**保存产品**/
                if (!$cprodut->validate() || !$cprodut->save()) {
                    $errors[$this->getFname($pkey) ] = $cprodut->getErrors();
                } else {
                    /**添加材料**/
                    foreach (array(
                        1,
                        2
                    ) as $mate_type) {
                        $mate = isset($product['materia'][$mate_type]) && is_array($product['materia'][$mate_type]) ? $product['materia'][$mate_type] : false;
                        // Tak::KD($mate);
                        if ($mate) {
                            foreach ($mate as $mkey => $materia) {
                                // Tak::KD($materia);
                                $cmateria = new CostMateria('create');
                                $cmateria->attributes = $materia;
                                $cmateria->cost_product_id = $product_id;
                                $cmateria->cost_id = $cost_id;
                                $cmateria->typeid = $mate_type;
                                /**保存材料**/
                                if (!$cmateria->validate() || !$cmateria->save()) {
                                    $errors[$this->getFname(array(
                                        $pkey,
                                        $mate_type,
                                        $mkey
                                    )) ] = $cmateria->getErrors();
                                }
                            }
                        }
                    }
                    /**添加工序**/
                    $processs = isset($product['process']) && is_array($product['process']) ? $product['process'] : false;
                    if ($processs) {
                        foreach ($processs as $pskey => $process) {
                            $cprocess = new CostProcess('create');
                            $cprocess->attributes = $process;
                            $cprocess->cost_product_id = $product_id;
                            /**保存工序**/
                            if (!$cprocess->validate() || !$cprocess->save()) {
                                $errors[$this->getFname(array(
                                    $pkey,
                                    $pskey
                                )) ] = $cprocess->getErrors();
                            }
                        }
                    }
                }
            }
        }
        return $errors;
    }
    public function actionCreate($id = 0) {
        if ($id > 0) {
            $model = $this->loadModel($id, false);
            if ($model != null) {
                $this->redirect(array(
                    'view',
                    'id' => $this->setSId($model->itemid) ,
                ));
            }
        }
        $m = $this->modelName;
        $model = new $m('create');
        $errors = array();
        $script = null;
        $template = $this->templates['create'];
        $products = array();
        $orderid = '';
        //判断是不是订单编号
        if ($model->isOrder($id)) {
            $orderid = $cost_id = $id;
            $products = $model->getOrderProduct($id);
        } else {
            $cost_id = Tak::fastUuid();
        }
        if (isset($_POST['M'])) {
            $data = $_POST['M'];
            $model->name = isset($data['name']) ? $data['name'] : Tak::timetodate(time() , 6);
            $model->itemid = $cost_id;
            $model->totals = $data['totals'];
            $products = isset($data['product']) && is_array($data['product']) ? $data['product'] : false;
            $errors = $this->saveInfo($cost_id, $products);
            if ($model->save()) {
            } else {
                $errors['msg'] = $model->getErrors();
            }
            $template = 'script';
            if (count($errors) > 0) {
                $model->del();
            } else {
                // window.top.document.location
                // parent.window.self.location.href = "%s"
                // var s = parent.document.getElementById("tak-load");s.setAttribute("href","%s");s.click();
                /*
                var redirectLink = parent.document.createElement("a");
                redirectLink.href = "%s";
                parent.document.body.appendChild(redirectLink);
                console.log(redirectLink);
                redirectLink.click();                
                */
                $script = sprintf('
                    var s = parent.document.getElementById("tak-load");s.setAttribute("href","%s");s.click();
                ', $this->createUrl('view', array(
                    'id' => $model->itemid
                )));
            }
        }
        $this->render($template, array(
            'model' => $model,
            'errors' => $errors,
            'script' => $script,
            'products' => $products,
            'orderid' => $orderid,
        ));
    }
    public function actionWorkshop() {
        $data = Workshop::getAll();
        foreach ($data as $key => $value) {
            if (isset($value['process'])) {
                $data[$key]['process'] = array_values($value['process']);
            } else {
                $data[$key]['process'] = array();
            }
        }
        $dname = $_GET['dname'];
        $this->render('workshop', array(
            'data' => $data,
            'dname' => $dname,
        ));
    }
    
    public function actionOrderWorkshop($id) {
        header('Content-Type: application/json');
        $data = null;
        if (!$id) {
            $data = '不存在车间';
        } else {
            $id = (int)$id;
            $ws = Workshop::getAll();
            if (!isset($ws[$id])) {
                $data = '不存在车间';
            } else {
                $process = isset($ws[$id]['process']) ? $ws[$id]['process'] : false;
                $arr = $_POST['m'];
                if ($process) {
                    if (count($process) != count($arr)) {
                        $data = '非法操作';
                    } else {
                        foreach ($process as $key => $value) {
                            if (!isset($arr[$key])) {
                                $data = '非法操作';
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ($data === null) {
            if (Workshop::orderWorkshop($arr, $id)) {
                $data = Workshop::getAllByProcess($id);
            }
        }
        echo json_encode($data);
    }
    public function actionDelWorkshop($id) {
        header('Content-Type: application/json');
        $data = null;
        if (!$id) {
            $data = '请选择车间';
        } else {
            $id = (int)$id;
            $ws = Workshop::getAll();
            if (!isset($ws[$id])) {
                $data = '不存在车间';
            } else {
                $msg = Workshop::inProduction($id);
                if (!$msg) {
                    Workshop::delWorkshop($id);
                } else {
                    $data = '生产中已经有记录了,不允许删除';
                }
            }
        }
        echo json_encode($data);
    }
    /**
     * 删除工序
     * @param  int $id     工序编号
     * @param  int $typeid 车间编号
     */
    public function actionDelProcess($id, $typeid) {
        header('Content-Type: application/json');
        $data = null;
        $ws = Workshop::getAll();
        if (!$id) {
            $data = '请选择工序';
        } elseif (!$typeid) {
            $data = '请选择车间';
        } elseif (!isset($ws[$typeid])) {
            $data = '不存在车间';
        } else {
            if (isset($ws[$typeid]['process'])) {
                foreach ($ws[$typeid]['process'] as $key => $value) {
                    if ($key == $id) {
                        $data = true;
                        break;
                    }
                }
                if (!$data) {
                    $data = '不存在工序';
                } else {
                    if (Workshop::delProcess($id, $typeid)) {
                        $data = null;
                    } else {
                        $data = '删除失败';
                    }
                }
            }
        }
        echo json_encode($data);
    }
    
    public function actionUpWorkshop($name, $id = 0) {
        header('Content-Type: application/json');
        $typeid = is_numeric($id) ? $id : false;
        $data = null;
        if (!$name) {
            $data = '请输入车间的名称';
        } else {
            $ws = Workshop::getAll();
            if (count($ws) > 0) {
                if ($id > 0 && !isset($ws[$id])) {
                    $data = '不存在车间';
                }
                foreach ($ws as $key => $value) {
                    if ($value['typename'] == $name) {
                        $data = sprintf('车间 [%s] 有重复', $name);
                        break;
                    }
                }
                if ($id > 0 && $data != null) {
                    $data = '';
                }
            } else {
                if ($itemid > 0) {
                    $data = '不存在车间';
                }
            }
        }
        if ($data === null) {
            if ($itemid > 0) {
                Workshop::upWorkshop($itemid, $name, $typeid);
            } else {
                $data = Workshop::addWorkshop($name);
            }
        }
        echo json_encode($data);
    }
    /**
     * 修改或者新增工序信息
     * @param  int  $id     车间编号
     * @param  string  $name   工序名
     * @param  int $itemid 修改的工序编号
     * @return [type]          [description]
     */
    public function actionUpProcess($id, $name, $itemid = 0) {
        header('Content-Type: application/json');
        $typeid = is_numeric($id) ? $id : false;
        $data = null;
        if (!$name) {
            $data = '请输入工序的名称';
        } elseif (!$typeid) {
            $data = '请选择车间';
        } else {
            $ws = Workshop::getAll();
            if (!isset($ws[$typeid])) {
                $data = '不存在车间';
            } else {
                if (isset($ws[$typeid]['process'])) {
                    if ($itemid > 0 && !isset($ws[$typeid]['process'][$itemid])) {
                        $data = '不存在工序';
                    }
                    foreach ($ws[$typeid]['process'] as $key => $value) {
                        if ($value['typename'] == $name) {
                            $data = sprintf('工序 [%s] 有重复', $name);
                            break;
                        }
                    }
                    if ($itemid > 0 && $data != null) {
                        $data = '';
                    }
                } else {
                    if ($itemid > 0) {
                        $data = '不存在工序';
                    }
                }
            }
        }
        if ($data === null) {
            if ($itemid > 0) {
                Workshop::upProcess($itemid, $name, $typeid);
            } else {
                $data = Workshop::addProcess($typeid, $name);
            }
        }
        echo json_encode($data);
    }
}
