<?php
/**
 *
 * @authors aklivecai (aklivecai@gmail.org)
 * @date    2014-03-13 10:08:23
 * @version $Id$
 */
class JImportProduct extends JImportForm {
    public $headers = array(
        'A' => '产品型号',
        'B' => '货物分类',
        'C' => '材料',
        'D' => '规格',
        'E' => '颜色',
        'F' => '计量单位',
        'G' => '备注',
        'H' => '单价',
        'I' => '库存',
    );
    public $cols = array(
        'A' => 'name',
        'B' => 'catename',
        'C' => 'material',
        'D' => 'spec',
        'E' => 'color',
        'F' => 'unit',
        'G' => 'note',
        'H' => 'price',
        'I' => 'stocks',
    );
    public $model = 'product';
    
    public function checkS($col, $value, $index) {
        $result = false;
        if (($col == 'A' || $col == 'B') && $value == '') {
            $result = true;
        } elseif ($col == 'I' && $value != '') {
            $result = !(Tak::isNumeric($value) && $value > 0);
        } elseif ($col == 'H' && $value != '') {
            $result = !Tak::isPrice($value);
        }
        return $result;
    }
    public function import($warehouse_id = false) {
        if (!$warehouse_id) {
            $warehouse_id = Tak::getPost('warehouse_id', false);
        }
        $arr = Tak::getOM();
        unset($arr['itemid']);
        $arr['note'] = '导入';
        $cates = $this->getCates();
        $sqls = array();
        AdminLog::$isLog = false;
        
        $cate = new Category('create');
        $cate->attributes = $arr;
        $cate->setModel($this->model);
        
        $product = new Product('create');
        $product->attributes = $arr;
        
        $stock = new Stocks('create');
        $stock->attributes = $arr;
        $newCates = array();
        $newProducts = 0;
        // Tak::KD($this->data);
        foreach ($this->data as $key => $value) {
            if (isset($cates[$value['catename']])) {
                $catid = $cates[$value['catename']];
            } else {
                if ($cate->catid > 0) {
                    $cate->catid+= $key * 2;
                }
                $cate->catename = $value['catename'];
                $cate->setIsNewRecord(true);
                $cate->save();
                $newCates[] = $cate->catename;
                $catid = $cates[$value['catename']] = $cate->catid;
            }
            $product->attributes = $value;
            if ($product->itemid > 0) {
                $product->itemid+= $key * 2;
                $product->setIsNewRecord(true);
            }
            if ($product->price == '') {
                $product->price = 0;
            }
            $product->typeid = $catid;
            if ($product->save()) {
                if ($warehouse_id > 0 && $product->stocks > 0) {
                    if ($stock->itemid > 0) {
                        $stock->itemid+= $key * 2;
                        $stock->setIsNewRecord(true);
                    }
                    $stock->attributes = array(
                        'product_id' => $product->itemid,
                        'warehouse_id' => $warehouse_id,
                        'stocks' => $product->stocks,
                    );
                    $stock->warehouse_id = $warehouse_id;
                    if ($stock->save()) {
                        // Tak::KD($warehouse_id);
                        // Tak::KD($stock->warehouse_id);
                        // return false;                        
                    } else {
                        // Tak::KD($stock->getErrors());
                        // return false;                        
                    }
                }
                $newProducts++;
            } else {
                // Tak::KD($product->getErrors());
            }
        }
        AdminLog::$isLog = true;
        $str = '
            <ul>
                <li>
                    导入产品型号：%s个<a href="%s">点击浏览</a>
                </li>%s
            </ul>
        ';
        $strCate = '';
        if (count($newCates) > 0) {
            $_str = '';
            foreach ($newCates as $value) {
                $_str.= sprintf("<span class='label label-success'>%s</span> ", $value);
            }
            $strCate = sprintf('<li>导入货物分类：%s</li>', $_str);
        }
        
        $tarr = array(
            'add_time' => $arr['time'],
            'add_ip' => $arr['ip'],
        );
        $url = Yii::app()->createUrl('Product/Admin?');
        
        $url.= Tak::createMUrl($tarr, $this->getModel());
        $str = sprintf($str, $newProducts, $url, $strCate);
        Tak::deleteUCache($this->model);
        AdminLog::log($str, $arr);
        Tak::setFlash($str);
    }
    
    public function getCates() {
        $sql = "SELECT catid,catename FROM :table WHERE  fromid=:fromid AND module=':module' ORDER BY listorder DESC,catid ASC";
        $sql = strtr($sql, array(
            ':table' => Category::$table,
            ':module' => 'product',
            ':fromid' => Tak::getFormid() ,
        ));
        $tags = Tak::getDb('db')->createCommand($sql)->queryAll();
        $result = array();
        foreach ($tags as $key => $value) {
            $result[$value['catename']] = $value['catid'];
        }
        return $result;
    }
}
