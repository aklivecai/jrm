<?php
class DbRecod extends ModuleRecord {
    public static $_db = null;
    /**
     * 检测企业是否存在数据库配置
     * @return [type] $db
     */
    public function getDbConnection() {
        if (self::$_db !== null) return self::$_db;
        else {
            if ($db = Ak::db(true)) {
                self::$db = self::$_db = $db;
            } else {
                self::$_db = self::$db;
            }
        }
        return self::$_db;
    }
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
}