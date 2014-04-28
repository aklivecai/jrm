<?php
/**
 *
 * @authors aklivecai (aklivecai@gmail.com)
 * @date    2014-04-24 08:30:11
 * @version $Id$
 */
class OrderConfig {
    public static function getNoteText($fid = false) {
        $model = self::getNote($fid);
        $result = $model != null ? $model->item_value : '';
        return $result;
    }
    public static function getNote($fid = false) {
        !$fid && $fid = Tak::getFormid();
        $model = Setting::getDefault('order-note', $fid);
        return $model;
    }
    public static function getListAlipay($fid = false) {
        !is_numeric($fid) && $fid = Tak::getFormid();
        $tags = array();
        $sql = "SELECT  title,itemid FROM :tabl WHERE fromid=:fromid AND type=':item' ORDER BY listorder DESC ";
        $sql = strtr($sql, array(
            ':tabl' => Info::$table,
            ':fromid' => $fid,
            ':item' => 'order-alipay',
        ));
        // Tak::KD($sql);
        $data = Tak::getDb('db')->createCommand($sql)->queryAll(TRUE);
        return $data;
    }
    public static function getAlipay($id, $fid = false) {
        $tags = array();
        $id < 100 && $fid = 0;
        $sql = "SELECT  i.title,c.content FROM :info i LEFT JOIN :data c  ON(i.itemid=c.itemid)   WHERE i.itemid = :itemid AND i.fromid=:fromid AND i.type=:item";
        $sql = strtr($sql, array(
            ':info' => Info::$table,
            ':data' => ContentData::$table,
            ':itemid' => $id,
        ));
        $arr = array(
            ':fromid' => $fid,
            ':item' => 'order-alipay',
        );
        // Tak::KD($sql);
        $data = Tak::getDb('db')->createCommand($sql)->queryRow(true, $arr);
        return $data;
    }
    
    public static function getSelectAlipay($fid = false, $label = false) {
        $tags = self::getListAlipay($fid);
        // Tak::KD($tags);
        if (count($tags) == 0) {
            $tags = self::getListAlipay(0);
        }
        $data = array();
        foreach ($tags as $key => $value) {
            $data[$value['itemid']] = $value['title'];
        }
        return $data;
    }
}
