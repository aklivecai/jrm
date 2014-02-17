<?php

	$this->pageTitle = Tk::g(array($model->getTypeName(),'bill')).'-'.$this->cates[$model->typeid];
?>
<div class="content">
<h1 class="title"><?php echo Tk::g(array($model->getTypeName(),'bill')); ?>
	<br />
<?php echo $this->cates[$model->typeid];?></h1>
<div>
	<div class="col3">
		<?php echo CHtml::encode($model->getAttributeLabel('numbers')); ?>:</b>
		<?php echo CHtml::encode($model->numbers); ?>
	</div>
	<div class="col3">
		<?php echo CHtml::encode($model->getAttributeLabel('enterprise')); ?>:</b>
		<?php echo CHtml::encode($model->enterprise); ?>
	</div>
	<div class="col3">
		<?php echo CHtml::encode($model->getAttributeLabel('time')); ?>:</b>
		<?php echo Tak::timetodate($model->modified_time); ?>
	</div>
	<i class="clearfix"></i>
 <?php $this->widget('bootstrap.widgets.TbListView', array(
			'dataProvider' => $model->getProductMovings(),
			'itemView'=>'//movings/_product_print',
			'template'=>'<table class="itable"> <thead> <tr> <th>物料名称</th> <th>规格</th> <th>颜色</th> <th>单位</th> <th>数量</th> <th>备注</th> </tr> </thead> <tbody>{items}</tbody> </table>',
            'emptyText'=>'<tr><td colspan="6">没有数据!</td></tr>'
		)); ?> 
	<div class="txt-right">
		<?php echo CHtml::encode($model->getAttributeLabel('us_launch')); ?>:</b>
		<?php echo CHtml::encode($model->us_launch); ?>
	</div>

<div class="noprint txt-center">
	<button type="button" onclick="window.print();"><?php echo Tk::g('Print');?></button>
	<button type="button" onclick="window.close();"><?php echo Tk::g('Close');?></button>
</div>
</div>
</div>
