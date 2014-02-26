<?php
$this->breadcrumbs=array(
	Tk::g(array('Product Category'))
);
$action = $model->isNewRecord?'Create':'Update';
?>
<div class="tab-content">
   <div class="row-fluid">
                    <div class="span7">
                        <div class="head clearfix">
                            <div class="isw-list"></div>
                            <h1><?php echo Tk::g('List'); ?></h1>
                        </div>

                        <div class="block-fluid clearfix">
					<?php 
					$widget = $this->widget('bootstrap.widgets.TbGridView', array(
							    'type'=>'striped bordered condensed',
							    'id' => 'list-grid',
								'dataProvider'=>$model->search(),
								'template' => '{summary}<div class="dr"><span></span></div>{items}',
							    	'summaryCssClass' => 'dataTables_info',
							    	'pagerCssClass' => 'pagination dataTables_paginate',
								'columns'=>array(
									array(
										'name'=>'listorder',
										'type'=>'raw',
										'headerHtmlOptions'=>array('style'=>'width: 85px'),
									),
									array(
										'name'=>'catename',
										'type'=>'raw',
									),
									array(
										'name'=>'item',
										'type'=>'raw',
									),
									array(
										 'class'=>'bootstrap.widgets.TbButtonColumn'
										  ,'htmlOptions'=>array('style'=>'width: 85px')
										  ,'template' => '{update} {delete}'
										  , 'deleteButtonUrl' => '$data->getDelLink()'
										  , 'updateButtonUrl' => '$data->getEidtLink()'

									),			
								),
							)); 
							?>
				</div>
			</div>
	<div class="span5">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1><?php echo Tk::g($action); ?></h1>
                        </div>
                        <div class="block  clearfix">        
						<?php 
						$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
							'id'=>'address-groups-form',
							 'type'=>'verticalForm ',
							 'enableAjaxValidation'=>false,
							 'htmlOptions'=>array('class'=>'well'),
							 'focus'=>array($model,'catename'),
						)); ?>
						<?php echo $form->errorSummary($model); ?>
						<?php echo $form->hiddenField($model,'model'); ?>
						<?php echo $form->textFieldRow($model,'parentid',array('size'=>60,'maxlength'=>255)); ?>
						<?php echo $form->textFieldRow($model,'catename',array('size'=>60,'maxlength'=>255)); ?>
						<?php echo $form->textFieldRow($model,'listorder',array('size'=>60,'maxlength'=>255)); ?>
						<div class="">
							<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Tk::g($action),'htmlOptions'=>array())); ?>
						</div>
						<?php $this->endWidget(); ?></div>
				</div>
			</div>			
			<div class="dr"><span></span></div>
		</div>
	</div>