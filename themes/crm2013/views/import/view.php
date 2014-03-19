<?php
 $form = $this->beginWidget('CActiveForm', array(
	'id'=>'import',
	'action'=>array('Import/Import?action='.$action)
)); ?>
	<div class="controls">
		<?php 
			$this->renderPartial('_'.$action); 
		?>
		<button class="btn" id="btn-submit" type="sublime">提交</button>
		<div class="dr"><span></span></div>
		<h6>总数:
		<span class="label label-warning">
			<?php echo  count($data)?>
		</span>,		请正确填写<span class="red">红色</span>文本框的内容!</h6>
	</div>
	<div class="dr"><span></span></div>
	<table class="items table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<?php foreach($header as $value):?>
				<th><?php echo $value?></th>
				<?php endforeach ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach($data as $key=>$value):?>
			<tr>
				<?php foreach($value as $k=>$v):?>
				<td><?php 
					$id = JImportForm::colName($action,$k,$key);
					$arr = array('id'=>$id);
					echo JHtml::textField($id,$v,$arr);
				?></td>
				<?php endforeach ?>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php $this->endWidget(); ?>