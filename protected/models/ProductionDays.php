<?php
/**
 * 这个模块来自表 "{{production_days}}".
 *
 * 数据表的字段 '{{production_days}}':
 * @property string $itemid
 * @property string $production_id
 * @property string $fromid
 * @property string $workshop_id
 * @property string $process
 * @property string $days
 * @property integer $progress
 */
class ProductionDays extends DbRecod {
    public static $table = '{{production_days}}';
    public function rules() {
        return array(
            array(
                'itemid, production_id, workshop_id, process',
                'required'
            ) ,
            array(
                'progress',
                'numerical',
                'integerOnly' => true
            ) ,
            array(
                'itemid, production_id',
                'length',
                'max' => 25
            ) ,
            array(
                'fromid, workshop_id, days',
                'length',
                'max' => 10
            ) ,
            array(
                'process',
                'length',
                'max' => 255
            ) ,
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'itemid, production_id, workshop_id, process, days, progress',
                'safe',
                'on' => 'search'
            ) ,
        );
    }
    public function attributeLabels() {
        return array(
            'itemid' => '编号',
            'production_id' => '生产单编号',
            'fromid' => '平台会员ID',
            'workshop_id' => '车间编号',
            'process' => '工序名字',
            'days' => '总用时', /*(0.5天)*/
            'progress' => '进度', /*(0:进行中,1:已完成)*/
        );
    }
    
    public function search() {
        $cActive = parent::search();
        $criteria = $cActive->criteria;
        
        $criteria->compare('itemid', $this->itemid, true);
        $criteria->compare('production_id', $this->production_id, true);
        $criteria->compare('fromid', $this->fromid, true);
        $criteria->compare('workshop_id', $this->workshop_id, true);
        $criteria->compare('process', $this->process, true);
        $criteria->compare('days', $this->days, true);
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
    }
    //删除信息后
    protected function afterDelete() {
        parent::afterDelete();
    }
}
