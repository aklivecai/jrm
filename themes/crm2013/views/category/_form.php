<?php
$action = $model->isNewRecord ? 'Create' : 'Update';

$items = Tak::getEditMenu($model->primaryKey, $model->isNewRecord);
?>
<div class="row-fluid">
<div class="span12">

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'clientele-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
)); ?>
<?php echo $form->errorSummary($model); ?>
<div class="head clearfix">
	<i class="isw-documents"></i> 
	<h1><?php echo Tk::g(array(
    $action,
    'Creategory'
)); ?></h1>
<?php
$this->widget('application.components.MyMenu', array(
    'htmlOptions' => array(
        'class' => 'buttons'
    ) ,
    'items' => $items,
));
?>         
</div>
<div class="block-fluid">
	<div class="row-form clearfix" style="border-top-width: 0px;">
		<?php echo $form->textFieldRow($model, 'parentid', array(
    'size' => 60,
    'maxlength' => 100
)); ?>
	</div>

	<div class="row-form clearfix">
		<?php
if ($model->isNewRecord) {
    echo $form->textAreaRow($model, 'catename', array(
        'size' => 60,
        'maxlength' => 255
    ));
} else {
    echo $form->textFieldRow($model, 'catename', array(
        'size' => 60,
        'maxlength' => 100
    ));
}
?>
	</div>
	<div class="row-form clearfix">
		<?php echo $form->textFieldRow($model, 'listorder', array(
    'size' => 60,
    'maxlength' => 255
)); ?>
	</div>
  </div>	
</div>

<div class="footer tar">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
    'size' => 'large',
    'buttonType' => 'submit',
    'label' => Tk::g($action)
)); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
    'size' => 'large',
    'buttonType' => 'reset',
    'label' => Tk::g('Reset')
)); ?>    
</div>
<?php $this->endWidget(); ?>
</div>
</div>