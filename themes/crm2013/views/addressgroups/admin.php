<?php
/* @var $this AddressGroupsController */
/* @var $model AddressGroups */

$this->breadcrumbs=array(
	Tk::g('Address Groups')=>array('admin'),
	Tk::g('Admin'),
);
?>
<div class="page-header">
      <h1><?php echo Tk::g('Address Groups'); ?> <small>显示状态，表示是否在前台显示的组</small></h1>
</div>
<div class="row-fluid">
		<div class="span4">
			<div class="block-fluid without-head">
				<div class="toolbar nopadding-toolbar clear clearfix">
					<h4><?php echo Tk::g('Create'); ?></h4>  
				</div>
				<div class="stream">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'address-groups-form',
	 'type'=>'verticalForm ',
	 'enableAjaxValidation'=>false,
	 'htmlOptions'=>array('class'=>'well'),
	 'focus'=>array($model,'name'),
	 'action'=>$this->createUrl('create'),  
)); 

 echo CHtml::hiddenField('returnUrl',isset($this->returnUrl)?$this->returnUrl:$this->createUrl('admin'));

 echo $form->textFieldRow($model,'name',array('size'=>60,'maxlength'=>255)); 
 echo $form->textFieldRow($model,'listorder',array('size'=>60,'maxlength'=>255)); 

 echo $form->textAreaRow($model,'note',array('size'=>60,'maxlength'=>255));
 echo $form->radioButtonListRow($model,'display',TakType::items('display'),array('class'=>'','template'=>'<label class="checkbox inline">{input}{label}</label>'));
  ?>

<div class="tar">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Tk::g('Create'),'htmlOptions'=>array())); ?>
</div>
<?php $this->endWidget(); ?>
				</div>
			</div>
		</div>
<div class="span8">
<div class="block-fluid without-head">
<div class="toolbar nopadding-toolbar clear clearfix">
	<h4><?php echo Tk::g('Action'); ?></h4>  
</div>
<?php $widget = $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'id' => 'list-grid',
	'dataProvider'=>$model->search(),
	'template'=>"{items}",
	'enableHistory'=>true,
    'loadingCssClass' => 'grid-view-loading',
    'summaryCssClass' => 'dataTables_info',
    'pagerCssClass' => 'pagination dataTables_paginate',
    'template' => '{items}{pager}',
    'ajaxUpdate'=>true,    //禁用AJAX
    'enableSorting'=>true,
    'summaryText' => '<span>共{pages}页</span> <span>当前:{page}页</span> <span>总数:{count}</span> ',
	'filter'=>$model,
	'pager'=>array(
		'header'=>'',
		'maxButtonCount' => '5',
		'hiddenPageCssClass' => 'disabled'
		,'selectedPageCssClass' => 'active disabled'
		,'htmlOptions'=>array('class'=>'')
	),
	'columns'=>array(
		Tak::getAdminPageCol(array(
			  'template'=>'{view} {update} {delete} <br /><span>{AddressBook}</span>'
			  ,'buttons'=>array(
				'AddressBook' => array
					(
						'label'=>Tk::g('AddressBook'),
						 'url'=>'Yii::app()->createUrl("addressBook/admin",array("AddressBook[groups_id]"=>$data->address_groups_id))',
						 'linkOptions'=>array('style'=>'width: 50px'),
					),
			  ),
			)
		)
		,array(
			'name'=>'name',
			'type'=>'raw',
			'value'=>'$data->getLink()',
		)
,		'note',			
		array(
			'name' => 'display',
			'htmlOptions'=>array('style'=>'width: 50px'),
			'value'=>'TakType::getStatus("display",$data->display)',
			'type'=>'raw',
			'filter'=>TakType::items('display'), 
		)
	),
)); 
?>
</div>
		</div>

	</div>
<div class="dr"><span></span></div>