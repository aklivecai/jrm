<?php
/* @var $this ClienteleController */
/* @var $model Clientele */

$this->breadcrumbs=array(
	Tk::g($this->getType().' Category')=>array('Admin'),
	$model->getLinkName()=>array('View','id'=>$model->itemid),
	Tk::g('Update'),
);
?>	
<?php $this->renderPartial('_form', array('model'=>$model)); ?>