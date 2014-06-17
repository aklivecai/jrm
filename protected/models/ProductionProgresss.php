<?php
/**
 * 这个模块来自表 "{{production_progresss}}".
 *
 * 数据表的字段 '{{production_progresss}}':
 * @property string $itemid
 * @property string $fromid
 * @property string $manageid
 * @property string $process_id
 * @property string $add_time
 * @property integer $progress
 */
class ProductionProgresss extends DbRecod {
    public static $table = '{{production_progresss}}';
    public function rules() {
        return array(
            array(
                'itemid, process_id',
                'required'
            ) ,
            array(
                'status',
                'numerical',
                'integerOnly' => true
            ) ,
            array(
                'itemid, manageid, process_id',
                'length',
                'max' => 25
            ) ,
            array(
                'fromid, add_time',
                'length',
                'max' => 10
            ) ,
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'itemid,  process_id, add_time, progress,status',
                'safe',
                'on' => 'search'
            ) ,
        );
    }
    public function attributeLabels() {
        return array(
            'itemid' => '编号',
            'fromid' => '平台会员ID',
            'manageid' => '会员ID',
            'process_id' => '工时编号',
            'add_time' => '添加时间',
            'progress' => '进度',
            'status' => '状态',
        );
    }
    
    public function search() {
        $cActive = parent::search();
        $criteria = $cActive->criteria;
        $criteria->compare('itemid', $this->itemid);
        $criteria->compare('fromid', $this->fromid);
        $criteria->compare('manageid', $this->manageid, true);
        $criteria->compare('process_id', $this->process_id, true);
        $criteria->compare('add_time', $this->add_time, true);
        $criteria->compare('progress', $this->progress);
        return $cActive;
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    //默认继承的搜索条件
    public function defaultScope() {
        $arr = parent::defaultScope();
        $condition = array(
            $arr['condition']
        );
        // $condition[] = 'display>0';
        $arr['condition'] = join(" AND ", $condition);
        return $arr;
    }
    //保存数据前
    protected function beforeSave() {
        $result = parent::beforeSave();
        if ($result) {
            if ($this->isNewRecord) {
            } else {
            }
        }
        return $result;
    }
    //保存数据后
    protected function afterSave() {
        parent::afterSave();
        $data = array(
            ':materia' => CostMateria::$table,
            ':table' => self::$table,
            ':itemid' => $this->itemid,
            ':fromid' => Ak::getFormid() ,
        );
    }
    //删除信息后
    protected function afterDelete() {
        parent::afterDelete();
    }
    /**
     * 根据工序的编号查询进度，按照最新的排在前面
     * @param  int $process_id 工序的编号
     * @return array             [description]
     */
    public static function getListByProcessid($process_id) {
        $sql = 'SELECT *  FROM :table WHERE fromid=:fromid AND process_id=:process_id ORDER BY add_time DESC';
        $data = array(
            ':table' => self::$table,
            ':fromid' => Ak::getFormid() ,
            ':process_id' => $process_id,
        );
        $sql = strtr($sql, $data);
        // Tak::KD($sql, 1);
        $result = Ak::getDb('db')->createCommand($sql)->queryAll(true);
        return $result;
    }
}
