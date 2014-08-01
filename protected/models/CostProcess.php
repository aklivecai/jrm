<?php
/**
 * This is the model class for table "{{cost_process}}".
 *
 * The followings are the available columns in table '{{cost_process}}':
 * @property string $itemid
 * @property string $fromid
 * @property string $cost_product_id
 * @property string $name
 * @property string $price
 * @property string $note
 */
class CostProcess extends DbRecod {
    public $linkName = 'name'; /*连接的显示的字段名字*/
    public static $table = '{{cost_process}}';
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                ' cost_product_id, name',
                'required'
            ) ,
            array(
                'itemid, cost_product_id',
                'length',
                'max' => 25
            ) ,
            array(
                'fromid',
                'length',
                'max' => 10
            ) ,
            array(
                'price',
                'numerical',
            ) ,
            array(
                'name',
                'length',
                'max' => 60
            ) ,
            array(
                'note',
                'length',
                'max' => 255
            ) ,
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'itemid, fromid, cost_product_id, name, price, note',
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
            'fromid' => '企业编号',
            'cost_product_id' => '成本核算产品编号',
            'name' => '工序名字',
            'price' => '价格',
            'note' => '备注',
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
        $criteria->compare('cost_product_id', $this->cost_product_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('price', $this->price, true);
        $criteria->compare('note', $this->note, true);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CostProcess the static model class
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
