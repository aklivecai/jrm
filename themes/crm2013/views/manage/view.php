<?php
/* @var $this ManageController */
/* @var $model Manage */

$this->breadcrumbs = array(
	Tk::g('Manages')=>array('admin'),
	$model->getLinkName(),
);

?>
<div class="block-fluid">
               <div class="row-fluid">
                    <div class="span10">
<?php 
$att = array(
		'user_name',
		'user_nicename',
		array('name'=>'branch','type'=>'raw' ,'value'=>$this->getBranch($model->branch)),
		array('name'=>'isbranch','type'=>'raw' ,'value'=>TakType::getStatus("isbranch",$model->isbranch)),

		'user_email',
		array('name'=>'add_time', 'value'=>Tak::timetodate($model->add_time,6),),
		array('name'=>'add_ip', 'value'=>Tak::Num2IP($model->add_ip),),
		array('name'=>'last_login_time', 'value'=>Tak::timetodate($model->last_login_time,6),),
		array('name'=>'last_login_ip', 'value'=>Tak::Num2IP($model->last_login_ip),),
		'login_count',
		array('name'=>'user_status','type'=>'raw' ,'value'=>TakType::getStatus("status",$model->user_status)),

		'note',
		array('name'=>'active_time', 'value'=>Tak::timetodate($model->active_time,6),),
	);
if (Tak::getAdmin()) {
	$att[] = 'manageid';
}

$this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>$att,
)); ?>
</div>
<div class="span2">
<?php 

$items = Tak::getViewMenu($model->primaryKey);

// revoke-link
$items['Delete']['label'] = Tk::g('Lock');
$items['Delete']['linkOptions']['class'] = 'revoke-link';

$_itemis = array(
	'---',
	'log' => array('label'=>Tk::g('AdminLog'), 'icon'=>'indent-left','url'=>array('AdminLog/admin','AdminLog[user_name]'=>$model->user_name)),

		array('label'=>Tk::g(array('More','Manages')), 'url'=>'#', 'icon'=>'list','itemOptions'=>array('data-geturl'=>$model->getLink(false,'gettop'),'class'=>'more-list'),'submenuOptions'=>array('class'=>'more-load-info'),'items'=>array(
	    	array('label'=>'...', 'url'=>'#'),
		))
);

$nps = $model->getNP(true);
if (count($nps)>0) {
   array_splice($_itemis,count($_itemis),0,Tak::getNP($nps));
}

array_splice($items,count($items)-2,0,$_itemis);  

$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'list',
    'items'=> $items,
    )
); 
?>
</div>
</div>
</div>

<div class="row-fluid" id="userAssignments">
<div class="head clearfix">
	<i class="isw-documents"></i> <h1><?php echo Tk::g(array('Jurisdiction'));?></h1>
<ul class="buttons">
        <li class="toggle"><a href="#userAssignments"></a></li>
</ul>
</div>

<div class="block-fluid clearfix">
	<div class="assignments span6">
    <div class="block-fluid nm without-head">
		<?php if( $formModel!==null ): ?>
			 <div class="">
		<?php 
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		    'id'=>'horizontalForm',
		    'type'=>'horizontal',
		)); 
		echo JHtml::tag('label',Rights::t('core', 'Assign item'));
		echo $form->dropDownList($formModel, 'itemname', $assignSelectOptions); 
		echo $form->error($formModel, 'itemname'); 
		$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Rights::t('core', 'Assign'))); 
		$this->endWidget(); 
		?>

			</div>
		<?php else: ?>
			<p class="info">
			<?php echo Rights::t('core', 'No assignments available to be assigned to this user.'); ?>
		</p>
		<?php endif; ?>    
<table cellpadding="0" cellspacing="0" width="100%" class="table">
  <thead>
      <tr>
      	 <th width="15%">类型</th>
          <th width="65%">名字</th>
          <th width="20%">操作</th>
      </tr>
  </thead>
  <tbody>
  <?php foreach ($dataJurisdiction as $key => $value) :?>
      <tr <?php if($value['active']){echo 'class="unedit"';}?>>
          <td><?php echo $value['typeName']  ?></td>
          <td><?php echo $value['title']  ?></td>
          <td>
		<?php 
			if (!$value['active']) {
				echo JHtml::link('<i class="icon-remove"></i>'.Tk::g('Revoke'),array('revoke','id'=>$model->primaryKey,'name'=>$value['id']),array('class'=>'revoke-link'));
				echo '&nbsp;|&nbsp;';
			}

			if ($value['type']==2&&is_numeric($value['name'])) {
				
				echo JHtml::link('<i class="icon-eye-open"></i>'.Tk::g('View'),array('permission/preview','id'=>$value['name']),array('class'=>'data-ajax','title'=>Tk::g(array('View',' 「'.$value['title'].'」','Jurisdiction'))));				
			}
		?>          
          </td>
      </tr>
    <?php endforeach ?>
      </tbody>
</table>
</div>

	</div>
    <div class="span5 add-assignment">
    
	</div>
</div>