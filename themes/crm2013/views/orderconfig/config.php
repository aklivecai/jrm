<?php
/* @var $this OrderController */
/* @var $model Order */

$this->breadcrumbs=array(
	Tk::g('Order')=>array('admin'),
	Tk::g(array('Order','Config')),
);

 foreach ($flowTypes as $key => $value) {
 	# code...
 }

?>
<div class="block-fluid">
    <div class="row-fluid">
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'brand'=>'',
    'brandUrl'=>'#',
    'fixed'=>'false',
    'fixed'=>'true',
    'collapse'=>true, // requires bootstrap-responsive.css

    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array('label'=>Tk::g(array('Add',$modelName)), 'url'=>$this->getLink('Create'), 'active'=>true,'linkOptions'=>array('class'=>"data-ajax",'id'=>'create-category','title'=>Tk::g(array('Add',$modelName)))),
                array('label'=>Tk::g(array('Update',$modelName)), 'url'=>$this->getLink('Update'),'linkOptions'=>array('title'=>Tk::g(array('Update',$modelName)),'class'=>'data-ajax','id'=>"ajax-update")),

                array('label'=>Tk::g(array('Delete',$modelName)), 'url'=>$this->getLink('Delete'),'linkOptions'=>array('id'=>"data-deletd")),
            ),
        ),
    ),
)); 
?>
<form class="form-inline" id="taktype-form" action="<?php echo $this->createUrl('flow',array('act'=>'create'))?>" method="post"> 
<div class="input-prepend"><input class="input-medium" placeholder="流程名字" name="flow[typename]" id="TakType_name" type="text" maxlength="25" required="required"></div><button class="btn" type="submit">添加</button>
<ul id="sortable-flow">
	<?php $this->renderPartial('_config_flow', array('flowTypes'=>$flowTypes,));?>
 </ul>
 </form>
<div class="dr"><span></span></div>
	<strong>提示</strong>：拖动左侧图标，即可排序
</div>

</div>
	<script>
	$(function() {
		var addForm = $('#taktype-form')
		, sortable = addForm.find('#sortable-flow') 
		, addName = addForm.find('#TakType_name')
		, addUrl = addForm.attr('action')
		, orderUrl = "<?php echo $this->createUrl('flow',array('act'=>'order')); ?>"
		, upUrl = "<?php echo $this->createUrl('flow',array('act'=>'update')); ?>"
		, checkName = function(elem){
			var result = elem.val()!='';
			if (!result) {
				alert('流程名字不能为空!');
				elem.focus();				
			};
			return result;

		}
		, updateTable = function(url,adata){
		        $.ajax({
		            type: "POST",
		            url: url,
		            data: adata,
		            success: function(data){
						if (data!='') {
							sortable.html(data);
						};			
		            }
		      });   			
		}
		;
		sortable.sortable({
			placeholder: "ui-state-highlight",
        	opacity: 0.6,
		      update: function (event, ui) {
		        var data = $(this).sortable('toArray')
		        // log(data.join('&'));
		        updateTable(orderUrl,data.join('&'));
		}
	});

		addForm.on('submit',function(event){
			// return true;
			event.preventDefault();
			var adata = addName.val();
			if (!checkName(addName)) {
				return false;
			};
			adata = addName.attr('name')+'='+adata;
			updateTable(addUrl,adata);
			addName.val('');
		});
		sortable.on('change', '.ini', function () {
			var t = $(this);
			if (!checkName(t)) {
				return false;
			};
			// log($(this).val());
			var adate = t.attr('name')+'='+t.val();
			updateTable(upUrl,adate);
		});
		sortable.on('click', '.ajax-del', function (event) {
			 // return true;
			event.preventDefault();
			var t = $(this).parent();
			t.addClass('bor-error');
			if(confirm('你确定要删除信息吗?')){
				updateTable($(this).attr('href'),'');
			}
			t.removeClass('bor-error');
			
		});
		sortable.disableSelection();
	});
	</script>
<style>
#sortable-flow { list-style-type: none; margin: 0;  padding: .2em;width:260px;}
#taktype-form .input-prepend{padding-left: 25px;}
#sortable-flow li { margin:5px 0 ; padding:3px 0;}
</style>
