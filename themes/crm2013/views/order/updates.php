<?php
/* @var $this OrderController */
/* @var $model Order */
/* @var $form bootstrap.widgets.TbActiveForm */
?>
<?php
$itemid = $model->itemid;
$this->breadcrumbs=array(
	Tk::g($model->sName) => array('admin'),
	$itemid,
);
  $orderInfo = $model->getOrderInfo();

  $listStatus = $model->getListStatus();

// Tak::KD($this->creatOrder(),1);
// $ts = $model->getProducts();
// Tak::KD($ts[0]->getAttributes());
// Tak::KD($model->getAttributes());
// Tak::KD($orderInfo->getAttributes());

  $orderStatus = OrderType::items('order-status');
  $actionStatus = TakType::items('order-flow',true);

  $optionsStatus = array();
  foreach ($orderStatus as $key => $value) {
	if($key>1){
  		$temp  = array('label'=>$value);
	  	if ($model->status<999&&$key>$model->status) {
	  		$temp['url'] = $this->createUrl('status',array('id'=>$itemid,'status'=>$key));
	  	}
	  	$optionsStatus[$key] = $temp;
  	}
  }

  $optionsAction = array();
  // $actionStatus[] = '---';
  // $actionStatus['0'] = '自定义';
  foreach ($actionStatus as $key => $value) {
  		$temp  = array('label'=>$value);
	  	if ($model->status<999
	  		&&$key!=$model->status
	  		&&$model->status>100
	  		&&!$listStatus[$key]
	  	) {
	  		$temp['url'] = 'javascript:setStatus('.$key.',"'.$value.'");';
	  	}
	  	$optionsAction[$key] = $temp;
  }
  $optionsAction[] = '---';
  $optionsAction['-1'] = array('label'=>'自定义','htmlOptions'=>array('style'=>'color:red'));
  
  if ($model->status<999&&$model->status>100) { 
  	$optionsAction['-1']['url'] ='javascript:setStatus(0,"自定义");';
  	$optionsAction[] = array('label'=>'关闭','url'=>'javascript:setStatus("","");');
  }else{
  	$optionsAction[] = '---';
  	$optionsAction[] = array('label'=>'订单未审核状态,不可操作,请先审核订单','htmlOptions'=>array('style'=>'color:#000;'),'url'=>'javascript:;');
  }
  
?>

<div class="tak-order-status">
	<?php echo CHtml::image($this->getAssetsUrl().'img/tak/'.$model->status.'.png') ?>
</div>

<div class="well">

<strong><?php echo $model->getAttributeLabel('itemid');?></strong>：
	<?php echo $model->itemid; ?>
	，
<strong><?php echo $model->getAttributeLabel('status');?></strong>：
	<?php echo OrderType::item('order-status',$model->status); ?>
	，
	<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
        'type'=>'info', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'buttons'=>array(
            array('label'=>'订单状态处理', 'items'=>$optionsStatus),
        ),
    )); ?>
    <strong class="red">第1步</strong>
<p>
	<?php echo $model->getAttributeLabel('add_time');?>：
	<?php echo Tak::timetodate($model->add_time,6); ?>
	，
	<?php echo $model->getAttributeLabel('total');?>：
	<strong class="price-strong">￥
		<?php echo $model->total; ?>
	</strong>
	，
	<?php echo $model->getAttributeLabel('manageid');?>：
	<?php 
	echo CHtml::link($model->iManage->company,Yii::app()->createUrl('/Site/PreviewTestMember',array('id'=>$model->manageid)),array('class'=>'data-preview'));
	 ?>
	，
	<?php echo $model->getAttributeLabel('add_ip');?>：
	<?php echo Tak::Num2IP($model->add_ip); ?>
</p>
<p>
	<?php
	 	if ($model->pay_time>0) {
	 		echo $model->getAttributeLabel('pay_time').'：'.Tak::timetodate($model->pay_time,6).'，';
	 	}
	 	if ($model->delivery_time>0) {
	 		echo $model->getAttributeLabel('delivery_time').'：'.Tak::timetodate($model->delivery_time,6);
	 	}
	 ?>
</p>
<div class="wide">

<?php 
$flow = new OrderFlow;
$form=$this->beginWidget('CActiveForm',array(
	'id'=>'flow-form',
	'action'=>$this->createUrl('flowset',array('id'=>$itemid)),
	// 'htmlOptions' => array('class'=>'flow-form'),
)); ?>
<?php echo $form->hiddenField($flow,'status');?>
<?php echo $form->hiddenField($flow,'itemid');?>
<input type="hidden" disabled="">
<table class="tak-table">
<caption>
<strong class="red">第2步</strong>
<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
        'type'=>'info', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'buttons'=>array(
            array('label'=>' 订单流程操作 ', 'items'=>$optionsAction),
        ),
    )); ?>

    &nbsp;&nbsp;
	<span class="label label-info" id="show-status"></span>
</caption>
<tbody class="wap-flow-content">
	<tr>
	<th><?php echo $form->label($flow,'action_user'); ?></th>
	<td>
		<?php echo $form->textField($flow,'action_user',array('size'=>100,'maxlength'=>10,'required'=>'required')); ?>
	</td>
	<th><?php echo $form->label($flow,'name'); ?></th>
	<td>
		<?php echo $form->textField($flow,'name',array('size'=>60,'maxlength'=>60,'disabled'=>'disabled')); ?>
	</td>
	</tr>
	<tr>
		<th>文件列表</th>
		<td>
			<?php

     $iupload =  $this->widget('ext.Plupload.PluploadWidget', array(
         'config' => array(
          'pluploadPath' => true,
          'url' => $this->createUrl('/it/upload'),
          'browse_button' => 'pickfiles',
              'container' => 'container',
         ),
         'id' => 'uploader'
      ),true);	

    $postFileUrl = $this->createUrl('/it/upload');

$tempscript = "
  var objUpload = {
  runtimes : 'flash,html5,html4',
  'url': '$postFileUrl',
  flash_swf_url : '$iupload/Moxie.swf',
  silverlight_xap_url : '$iupload/Moxie.xap', 
  browse_button:'pickfiles' ,
  container:'container' ,
  filters : {
    max_file_size : '10mb',
    mime_types: [
      {'title' : 'Image files', 'extensions' : 'jpg,gif,png,jpeg'},
      {'title' : 'Zip files', 'extensions' : 'zip,rar'},
      {'title' : 'Doc files', 'extensions' : 'doc,docx,xls,xlsx,rtf,txt'}
    ]
  }}
  , uploader = new plupload.Uploader(objUpload)

  ;

	$('#uploadfiles').click(function(e) {
		uploader.start();
		e.preventDefault();
	});
	uploader.init();	
	uploader.bind('PostInit', function(up) {
		$('#filelist').html('');
	});
	uploader.bind('FilesAdded', function(up, files) {
		plupload.each(files, function(file) {
			document.getElementById('filelist').innerHTML += '<div id=\"' + file.id + '\"><a data-to=\"'+file.id+'\" href=\"javascript:;\" class=\"remove\"><i class=\"icon-trash\"></i></a>  ' + file.name + ' <b></b></div></div>';
		});
		up.refresh(); 
		uploader.start();
	});

	uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + ' b').html(file.percent + '%');
	});

	uploader.bind('Error', function(up, err) {
		$('#filelist').append('<div class=\"red\">提示: ' + err.message +(err.file ? ', 文件: ' + err.file.name :'') +'</div>'
		);
		up.refresh();
	});
    uploader.bind('FileUploaded', function(up, file,msg) {
    	var elem = $('#' + file.id);
         elem.find('b').html('100%');
        if (msg&&typeof msg['response']!='undefined') {
        	var obj = $.parseJSON(msg['response']);
        	if (obj&&typeof obj['result']!='undefined') {        		
        	elem.append('<input type=\"hidden\" name=\"files[]\" value=\"'+obj.result+'\"/>');
        	}
        }
    });
    $('#filelist').on('click',' a.remove',function(event) {
    	event.preventDefault();
    	var id = $(this).attr('data-to');
      	uploader.removeFile(uploader.getFile(id));
      	$('#' + id).remove();
    });
";
 Yii::app()->clientScript->registerScript('', $tempscript, CClientScript::POS_READY);			
?>		
<div id="container" class="itak-dr">
	<a id="pickfiles" href="#">[选择文件]</a>
	<div class="dr"><span></span></div>
	<div id="filelist"></div>
</div>
		</td>
	<th><?php echo $form->label($flow,'note'); ?></th>
	<td>
		<?php echo $form->textArea($flow,'note',array('size'=>60,'maxlength'=>255)); ?>
	</td>
	</tr>
	<tr>
	<th></th>
	<td colspan="3">
		  <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Tk::g('Save'))); ?>
	</td>
	</tr>
</tbody>
</table>

<?php $this->endWidget(); ?>

 
<table class="tak-table action-fold">
	<caption>订单跟踪</caption>
	<colgroup align="center">
	<col width="140px"/>
	<col width="85px"/>
	<col width="150px"/>
	</colgroup>	
	<thead>
	<tr>
		<th>处理时间</th>
		<th>操作人</th>
		<th>状态</th>
		<th>文件</th>
		<th>处理信息</th>
	</tr>
	</thead>
	<tbody class="wap-products">
	<?php 
	$list = $model->getFlows();
	$result = '';
	$strHtml = '<tr>
	<td>:add_time</td>
	<td>:action_user</td>
	<td>:name</td>
	<td>:pics</td>
	<td>:note</td>
	</tr>';
	$arr = false;
	foreach ($list as $key => $value) {
		$arr = array(
			':pics'=>$value->getFilesImg(),
			':add_time'=>Tak::timetodate($value->add_time,6),
			':action_user'=>$value->action_user,
			':name'=>$value->getName(),
			':note'=>$value->note,
		);
		$result .= strtr($strHtml,$arr);
	}
	echo $result;
	?>
	</tbody>	
</table>

</div>

<table class="tak-table action-fold">
	<caption>详细信息</caption>
	<tbody>
	<tr>
		<th><?php echo $orderInfo->getAttributeLabel('date_time');?>:</th>
		<td><?php echo Tak::timetodate($orderInfo->date_time,3); ?></td>
		<th><?php echo $orderInfo->getAttributeLabel('packing');?>:</th>
		<td><?php echo OrderType::item('packing',$orderInfo->packing); ?></td>
	</tr>
	<tr>
		<th><?php echo $orderInfo->getAttributeLabel('taxes');?>:</th>
		<td><?php echo OrderType::item('taxes',$orderInfo->taxes); ?></td>
		<th><?php echo $orderInfo->getAttributeLabel('convey');?>:</th>
		<td><?php echo OrderType::item('convey',$orderInfo->convey); ?></td>
	</tr>
	<tr>
		<th><?php echo $orderInfo->getAttributeLabel('pay_type');?>:</th>
		<td colspan="3">
			<?php 
				echo OrderType::getStatus('pay_type',$orderInfo->pay_type); 
				echo $orderInfo->getPayInfo();
			?>
		</td>		
	</tr>
	<tr>
		<th><?php echo $orderInfo->getAttributeLabel('detype');?>:</th>
		<td colspan="3">
		<?php 
			echo OrderType::getStatus('detype',$orderInfo->detype); 			
			echo $orderInfo->getContactp();
		?>
		</td>
	</tr>
	<tr>
		<th><?php echo $orderInfo->getAttributeLabel('note');?>:</th>
		<td colspan="3">
		<?php 
			echo $orderInfo->note;
		?>
		</td>
	</tr>
	</tbody>
</table>

<table class="tak-table action-fold">
	<caption>商品清单</caption>
	<thead>
		<tr>
			<th width="150">产品名称</th>
			<th>详情</th>
			<th width="120">单价</th>
			<th width="80">数量</th>
			<th width="150">小计</th>
		</tr>
	</thead>
	<tbody class="wap-products">
	<?php 
	 $list = $model->getProducts();
	$result = '';
	$strHtml = '<tr>
	<td>:name</td>
	<td>
		<dl>
			<dt>$model:</dt><dd>:model &nbsp;</dd>
			<dt>$standard:</dt><dd>:standard &nbsp;</dd>
			<dt>$color:</dt><dd>:color &nbsp;</dd>
			<dt>$unit:</dt><dd>:unit &nbsp;</dd>
		</dl>
		<div class="kclear"></div>
		<div>
		<strong>$note:</strong>
		:note
		</div>
		:pics
	</td>
	<td class="price-strong">￥:price</td>
	<td>:amount</td>
	<td class="price-strong">￥:sum</td>
	</tr>';
	$arr = false;
	foreach ($list as $key => $value) {
		$arr = array(':pics'=>$value->getFilesImg());
		$icount = 0;
		foreach (array('name','amount','price','sum','unit','model','standard','color','note') as  $v1) {
			$arr[':'.$v1] = $value->{$v1};
			if ($icount>3) {
				$arr['$'.$v1] = $value->getAttributeLabel($v1);
			}
			$icount++;
		}
		$result .= strtr($strHtml,$arr);
	}
	echo $result;
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">合计:
			<strong  class="price-strong">￥
			<?php echo $model->total;  ?>
			</strong>
			</td>
		</tr>
	</tfoot>
</table>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'sssModal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Modal header</h4>
</div>
 
<div class="modal-body">
    <p>用户信息........</p>
</div> 
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
 
<?php $this->endWidget(); ?>



<script>
var flowForm = $('#flow-form')
, flowStatus = flowForm.find('#OrderFlow_status')
, flowName = flowForm.find('#OrderFlow_name')

, setStatus = function(status,txt){	
	$('#show-status').html(txt);
	if (status!=='') {
		flowForm.addClass('active');
		flowStatus.val(status);
		if (status==0) {
			flowName.removeAttr('disabled');
		}else{
			flowName.attr('disabled',true);
		}
	}else{
		flowForm.removeClass('active');
	}
};

 flowForm.on('submit',function(event){
	 if (flowStatus.val()==0&&flowName.val()=='') {
	 	event.preventDefault();
	 	alert('流程名字不能为空!');
	 	flowName.focus();
	 };
});

	$(function() {
		$(document).on('click','table.action-fold caption',function(){
			var t = $(this).parent();
			t.toggleClass('active');
		});
	});
</script>