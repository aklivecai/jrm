<?php
/* @var $this TestLogController */
/* @var $model TestLog */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row">
		<?php echo $form->label($model,'userid'); ?>
		<?php echo $form->textField($model,'userid',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'company'); ?>
		<?php echo $form->textField($model,'company',array('size'=>60,'maxlength'=>60)); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton(Tk::g('Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->