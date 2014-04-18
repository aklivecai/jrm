<?php
	$mproduct = new Product();
	$dsetting = Setting::getStocks($model->primaryKey);
	foreach (array('typeid','name','warehouse_id') as $key => $value) {
		!isset($dsetting['stocks_'.$key])&&$dsetting['stocks_'.$key]='';
	}
?>
<div class="row-fluid">
	<div class="head clearfix" id="stocks">
		<i class="isw-documents"></i> <h1><?php echo Tk::g(array(
		'Jurisdiction'
		)); ?></h1>
	</div>
	<div class="block-fluid" >
		<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'setting-form','action'=>Yii::app()->createUrl('Setting/Creates',array('id'=>$model->primaryKey))));?>
		<table class="detail-view table table-striped table-condensed">
			<caption>库存</caption>
			<tbody>
				<tr>
					<th width="15%">
						<?php echo JHtml::label(Tk::g('Warehouse'),'');?>
					</th>
					<td>
						<?php
						echo JHtml::dropDownList('Setting[stocks_warehouse_id]',$dsetting['stocks_warehouse_id'],Warehouse::toSelects(Tk::g('All')));
						?>
					</td>
					<th width="15%">
						<?php echo JHtml::label($mproduct->getAttributeLabel('typeid'),'');?>
					</th>
					<td>
						<?php
							$this->renderPartial('/category/select', array(
							'id' => "Setting[stocks_typeid]",
							'value' => $dsetting['stocks_typeid'],
							));
						?>
					</td>
				</tr>
				<tr>
					<th width="15%">
						<?php echo JHtml::label($mproduct->getAttributeLabel('name'),'setting_name');?>
					</th>
					<td colspan="2">
						<?php echo JHtml::textField('Setting[stocks_name]',$dsetting['stocks_name'],array('id'=>"setting_name"));?>
					</td>
					<td colspan="4" align="right">
						<?php
						$this->widget('bootstrap.widgets.TbButton',
							array(
								'type'=>'info',
								'label' => Tk::g('Preview'),
								'htmlOptions'=>array('id'=>"preview")
							)
						);
						echo '  ';
						$this->widget('bootstrap.widgets.TbButton',
							array(
								'buttonType'=>'submit',
								'label' => Tk::g('Save'),
							)
							);
						?>

					</td>
				</tr>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="4"></td>
			</tr>
			</tfoot>
		</table>
		<?php
			echo CHtml::hiddenField('returnUrl', Yii::app()->createUrl($this->route,array('id'=>$model->primaryKey)).'#stocks');
			$this->endWidget();
		?>
		<div class="dr"><span></span></div>
	</div>
</div>
<script>
	$('#preview').on('click',function(){
		wurl  = createUrl('Site/Supplier',['action=preview','Setting[stocks_typeid]='+$('#Setting_stocks_typeid').val(),'Setting[stocks_name]='+$('#setting_name').val(),'Setting[stocks_warehouse_id]='+$('#Setting_stocks_warehouse_id').val(),]);
		window.open(wurl, "windowPreview" ,"width=800,height=650,resizable=0,scrollbars=1");
	})
	
</script>