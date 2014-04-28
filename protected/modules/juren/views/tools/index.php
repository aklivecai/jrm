<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'contact-form',
    'enableAjaxValidation' => false, //是否启用ajax验证
    'htmlOptions' => array() ,
));
?>
<input type="text" name="live[name]" />
<button type="submit">提交</button>
<?php
$this->endWidget();
?>