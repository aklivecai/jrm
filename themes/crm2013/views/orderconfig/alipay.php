<?php
$this->breadcrumbs = array(
Tk::g('Order') => array(
'order/admin'
) ,
Tk::g('Alipay') ,
);
?>
<div class="page-header">
    <h1><?php echo Tk::g('Alipay') ?> <small><?php echo Tk::g($action) ?></small></h1>
</div>
<div class="block-fluid">
	<div class="row-fluid">
		<?php $this->tab(); ?>
		<div class="">
			&nbsp;
			<?php
				echo  JHtml::link(Tk::g('Create'),array('CreateAlipay'),array('class'=>'btn btn-success','id'=>'create'));
			?>
		</div>
		<div class="dr"><span></span></div>
		<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'manage-form',
		'enableAjaxValidation' => false,
		)); ?>
		<div  class="list-tree">
			<ul>
				<?php
				$html='';
				$str = '<li><a  title="更新" href="%s">%s <i class="icon-pencil"></i></a>|<a class="delete" title="删除" href="%s"><i class="icon-trash"></i></a></li>';
				foreach ($tags as $key => $value){
					$html.=sprintf($str,$this->createUrl('CreateAlipay',array('id'=>$value['itemid'])),$value['title'],$this->createUrl('DeletedAlipay',array('id'=>$value['itemid'])));
				}
				echo $html;
				?>
			</ul>
		</div>

	</form>
</div>
</div>
<?php $this->endWidget(); ?>