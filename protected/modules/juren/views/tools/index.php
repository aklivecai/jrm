<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'contact-form',
    'enableAjaxValidation' => false, //是否启用ajax验证
    'method' => 'get',
    'action' => array('index'),
    'htmlOptions' => array(
        'method' => 'get'
    ) ,
));

echo CHtml::dropDownList('action', $action, $this->actions);

echo CHtml::textField('fid', $fid);
?>
<button type="submit">提交</button>
<?php
$this->endWidget();
?>
