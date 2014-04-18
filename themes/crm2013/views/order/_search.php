<?php
/** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'search-form',
    'type' => 'search',
    'htmlOptions' => array(
        'class' => 'well'
    ) ,
    'action' => Yii::app()->createUrl($this->route) ,
    'method' => 'get',
));
echo $form->dropDownList($model, 'status', Order::getSearchStatus());
echo $form->dropDownList($model, 'manageid', Order::getUsersSelect());

echo $form->textFieldRow($model, 'add_time', array(
    'size' => 10,
    'maxlength' => 10,
    'class' => 'type-date'
));
echo $form->textFieldRow($model, 'itemid', array(
    'class' => 'input-medium',
    'prepend' => '<i class="icon-search"></i>'
));
echo " ";
$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'label' => Tk::g('Search') ,
)); ?>

            <hr />

<?php
if (YII_DEBUG) {
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'link',
        'label' => '生成测试订单',
        'url' => array(
            'TestOrder'
        ) ,
        'size' => 'large'
    ));
}
$this->endWidget();
?>