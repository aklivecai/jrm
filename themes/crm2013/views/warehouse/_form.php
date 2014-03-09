<?php
$action = $model->isNewRecord ? 'Add' : 'Update';
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
			    'id'=>'horizontalForm',
			    'type'=>'horizontal',
				 'enableAjaxValidation'=>false,
				 'htmlOptions'=>array('class'=>'well'),
			)
		);
	// echo $form->errorSummary($model); 	
?>


<div class="row-fluid">
    <div class="row-form clearfix" style="border-top-width: 0px;">
    		<?php echo $form->textFieldRow($model, 'name', array('size'=>50,'maxlength'=>50)); ?>
    </div>
    <div class="row-form clearfix">
		<?php echo $form->textFieldRow($model,'user_name',array('size'=>50,'maxlength'=>50)); ?>
	</div>
	<div class="row-form clearfix">
	<?php echo $form->textFieldRow($model,'telephone',array('size'=>50,'maxlength'=>50)); ?>
	</div>
		<div class="">
	<div class="footer tar">
	    <?php $this->widget('bootstrap.widgets.TbButton', array('size'=>'large','buttonType'=>'submit', 'label'=>$model->isNewRecord ? Tk::g('Add') : Tk::g('Save'))); ?>
	    <?php $this->widget('bootstrap.widgets.TbButton', array('size'=>'large','buttonType'=>'reset', 'label'=>Tk::g('Reset'))); ?>
	</div>
	</div>
</div>

<?php $this->endWidget(); ?>