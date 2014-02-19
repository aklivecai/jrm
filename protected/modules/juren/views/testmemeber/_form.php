<?php
/* @var $this TestMemeberController */
/* @var $model TestMemeber */
/* @var $form CActiveForm */
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'test-memeber-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'company'); ?>
		<?php echo $form->textField($model,'company',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'company'); ?>
	</div>
	
	<?php if($model->active_time>0&&Tak::getAdmin()):?>
	<div class="row">
		<?php echo $form->label($model,'active_time'); ?>
		<?php echo $form->textField($model,'active_time',array('size'=>10,'maxlength'=>10,'class'=>'date')); ?>
	</div>	
	<?php endif ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textField($model,'note',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'note'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Tk::g('添加') : Tk::g('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->