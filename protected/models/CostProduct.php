<?php
/**
 * This is the model class for table "{{Cost_Product}}".
 *
 * The followings are the available columns in table '{{Cost_Product}}':
 * @property string $itemid
 * @property string $fromid
 * @property string $cost_id
 * @property string $type
 * @property string $name
 * @property string $spec
 * @property string $color
 * @property string $file_path
 * @property string $expenses
 * @property string $price
 * @property string $numbers
 * @property string $totals
 */
class CostProduct extends DbRecod {
    public $linkName = 'name'; /*连接的显示的字段名字*/
    public static $table = '{{cost_product}}';
    
    public function rules() {
        return array(
            array(
                'cost_id, type, name, spec, numbers',
                'required'
            ) ,
            array(
                'itemid, cost_id',
                'length',
                'max' => 25
            ) ,
            array(
                'fromid, expenses, price, totals',
                'length',
                'max' => 10
            ) ,
            array(
                'type, name, spec, color',
                'length',
                'max' => 100
            ) ,
            array(
                'file_path',
                'length',
                'max' => 255
            ) ,
            array(
                'numbers',
                'length',
                'max' => 20
            ) ,
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'itemid, fromid, cost_id, type, name, spec, color, file_path, expenses, price, numbers, totals',
                'safe',
                'on' => 'search'
            ) ,
        );
    }
    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'itemid' => '编号',
            'fromid' => '平台会员ID',
            'cost_id' => '成本核算编号',
            'type' => '品名',
            'name' => '型号',
            'spec' => '规格',
            'color' => '颜色',
            'file_path' => '产品图片',
            'expenses' => '管理费',
            'price' => '单价',
            'numbers' => '数量',
            'totals' => '总成本',
        );
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.
        
        $criteria = new CDbCriteria;
        
        $criteria->compare('itemid', $this->itemid, true);
        $criteria->compare('fromid', $this->fromid, true);
        $criteria->compare('cost_id', $this->cost_id, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('spec', $this->spec, true);
        $criteria->compare('color', $this->color, true);
        $criteria->compare('file_path', $this->file_path, true);
        $criteria->compare('expenses', $this->expenses, true);
        $criteria->compare('price', $this->price, true);
        $criteria->compare('numbers', $this->numbers, true);
        $criteria->compare('totals', $this->totals, true);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CostProduct the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    //保存数据前
    protected function beforeSave() {
        if ($this->isNewRecord) {
            !$this->itemid && $this->itemid = Ak::fastUuid();
            !$this->fromid && $this->fromid = Ak::getFormid();
        }
        return true;
    }
    protected function afterSave() {
    }
}
