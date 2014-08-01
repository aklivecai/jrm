<?php
class ToolsController extends JController {
    protected $actions = array(
        '0' => '操作',
        'product' => '清空产品',
        'order' => '清空订单',
        'production' => '生产管理',
    );
    public function init() {
        parent::init();
        $this->menu = array(
            'company' => array(
                'label' => Tk::g('Company') ,
                'url' => array(
                    'company/admin'
                )
            ) ,
            'manage' => array(
                'label' => Tk::g('Manage') ,
                'url' => array(
                    'Manage'
                )
            ) ,
            'database' => array(
                'label' => Tk::g('Database') ,
                'url' => array(
                    'Database'
                )
            ) ,
            'wipe' => array(
                'label' => Tk::g('Wipe') ,
                'url' => array(
                    'Wipe'
                )
            ) ,
        );
    }
    public function actionCreate() {
        throw new CHttpException(404, '所请求的页面不存在。');
    }
    public function actionUpdate($id) {
        throw new CHttpException(404, '所请求的页面不存在。');
    }
    public function actionDelete($id) {
        throw new CHttpException(404, '所请求的页面不存在。');
    }
    public function actionIndex() {
        $fid = Tak::getPost('fid', false);
        $action = Tak::getPost('action', false);
        if ($action && $fid) {
            switch ($action) {
                case 'product':
                    $this->clearProduct($fid);
                break;
                case 'order':
                    $this->clearOrder($fid);
                break;
                case 'production':
                    $this->clearProduction($fid);
                break;
                default:
                break;
            }
        }
        $this->render('index', array(
            'fid' => $fid,
            'aciton' => $action,
        ));
    }
    public function clearOrder($fid) {
        if ($fid) {
            $command = Tak::getDb('db')->createCommand('');
            $arr = array(
                ':fromid' => $fid
            );
            /*清空数据,订单*/
            $sqls = array(                
                "DELETE FROM  {{order_files}} WHERE action_id  IN( SELECT oflow.itemid FROM {{order_flow}} AS oflow
                                            INNER JOIN {{order}}  AS o 
                                                ON oflow.order_id=o.itemid 
                                            WHERE o.fromid=:fromid  UNION ALL SELECT itemid FROM {{order_product}}  WHERE fromid=:fromid)",
                
                "DELETE FROM {{order_product}} WHERE order_id IN(SELECT itemid FROM {{order}} WHERE fromid=:fromid ) ",

                "DELETE FROM {{order_flow}} WHERE order_id IN(SELECT itemid FROM {{order_info}}  WHERE fromid=:fromid )",


                "DELETE FROM {{order_review}} WHERE fromid=:fromid ",
                "DELETE FROM {{order}} WHERE fromid=:fromid ",
                "DELETE FROM {{order_info}} WHERE fromid=:fromid ",
            );
            
            $sqlsx = array(
                '删除订单文件' => 'DELETE ofile FROM {{order_files}} AS ofile 
                                            INNER JOIN {{order_flow}}  AS oflow 
                                                ON ofile.action_id=oflow.itemid 
                                            WHERE oflow.fromid=:fromid',
                '删除订单产品文件' => 'DELETE ofile FROM {{order_files}} AS ofile 
                                            INNER JOIN {{order_product}}  AS oproduct 
                                                ON ofile.action_id=oproduct.itemid 
                                            WHERE oproduct.fromid=:fromid',
                
                '删除订单产品' => 'DELETE oproduct FROM {{order_product}} AS oproduct 
                                            INNER JOIN {{order}}  AS o 
                                                ON oproduct.order_id = o.itemid 
                                            WHERE oproduct.fromid=:fromid',
                
                '删除变更产品' => 'DELETE aproduct FROM {{alteration_product}} AS aproduct 
                                            INNER JOIN {{order}}  AS o 
                                                ON aproduct.alteration_id = o.itemid 
                                            WHERE o.fromid=:fromid',
                
                '删除订单流程' => 'DELETE oflow FROM {{order_flow}} AS oflow
                                            INNER JOIN {{order}}  AS o 
                                                ON oflow.order_id=o.itemid 
                                            WHERE o.fromid=:fromid',
                
                '删除订单和信息' => ' DELETE o,oinfo FROM {{order}} AS o　
                                            LEFT JOIN {{order_info}} AS oinfo
                                                    ON o.itemid=oinfo.itemid
                                            WHERE o.fromid=:fromid',
            );
            foreach ($sqls as $key => $value) {
                while (1) {
                    //每次只做1000条
                    $command->text = sprintf('%s LIMIT 1000', $value);
                    // $command->text = $value;
                    $rowCount = $command->execute($arr);
                    if ($rowCount == 0) {
                        // 没得可删了，退出！
                        // Tak::KD($command->text);
                        break;
                    } else {
                        Tak::KD($rowCount);
                    }
                    // 每次都要休息一会儿
                    usleep(50000);
                }
            }
        }
    }
    private function getDb($id) {
        $db = Tak::db(true, $id)->createCommand('');
        return $db;
    }
    private function clearProduction($fid) {
        $command = $this->getDb($fid);
        $arr = array(
            ':fromid' => $fid
        );
        if (!$fid) {
            return false;
        }
        /*成本核算，生产管理*/
        $sqls = array(
            "DELETE FROM {{Cost}} WHERE fromid=:fromid ",
            "DELETE FROM {{Cost_Product}} WHERE fromid=:fromid ",
            "DELETE FROM {{Cost_Materia}} WHERE fromid=:fromid ",
            "DELETE FROM {{Cost_Process}} WHERE fromid=:fromid ",
            "DELETE FROM {{Production}} WHERE fromid=:fromid ",
            "DELETE FROM {{Production_Product_Days}} WHERE fromid=:fromid ",
            "DELETE FROM {{Production_Days}} WHERE fromid=:fromid ",
            "DELETE FROM {{production_progresss}} WHERE fromid=:fromid ",
        );
        foreach ($sqls as $key => $value) {
            $command->text = $value;
            $rowCount = $command->execute($arr);
            Tak::KD($rowCount);
        }
    }
    private function clearProduct($fid) {
        $command = $this->getDb($fid);
        $arr = array(
            ':fromid' => $fid
        );
        if (!$fid) {
            return false;
        }
        /*清空数据,订单*/
        $sqls = array(
            "DELETE FROM {{product_moving}} WHERE product_id IN (SELECT itemid FROM {{product}} WHERE fromid=:fromid);",
            "DELETE FROM {{product}} WHERE fromid=:fromid ",
            "DELETE FROM {{stocks}} WHERE fromid=:fromid ",
            "DELETE FROM {{movings}} WHERE fromid=:fromid ",
            "DELETE FROM {{category}} WHERE fromid=:fromid ",
        );
        foreach ($sqls as $key => $value) {
            $command->text = $value;
            $rowCount = $command->execute($arr);
            Tak::KD($rowCount);
        }
    }
    public function actionDatabase() {
        $m = 'live';
        if (isset($_POST[$m]) && isset($_POST[$m]['fid'])) {
            $this->clearProduct($_POST[$m]['fid']);
        }
        $this->render('index', array());
    }
    public function actionWipe() {
        $model = new WipeForm;
        if (isset($_POST['WipeForm'])) {
            $sqls = $model->sqls;
            $command = Tak::getDb('db')->createCommand('');
            $arr = array(
                ':fromid' => 5112
            );
            foreach ($sqls as $key => $value) {
                $command->text = $value;
                // $str = strtr($command->text,$arr);
                // $str = str_replace( 'DELETE' ,'SELECT 1' ,$str);
                // Tak::KD($str);
                // $rowCount = $command->execute($arr);
                /**/
            }
        }
        $this->render('wipe', array(
            'model' => $model
        ));
    }
    public function actionAdmin() {
        $model = new TestLog('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['TestLog'])) $model->attributes = $_GET['TestLog'];
        
        $this->render('admin', array(
            'model' => $model,
        ));
    }
}
