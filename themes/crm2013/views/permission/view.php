<?php
/* @var $this ClienteleController */
/* @var $model Clientele */

$this->breadcrumbs=array(
	Tk::g($this->modelName)=>array('admin'),
	$model->title,
);
// CSqlDataProvider
	 $childDataProvider = $model->getChild();
	// $childDataProvider = new RAuthItemChildDataProvider($model);
	// $childSelectOptions = Rights::getParentAuthItemSelectOptions($model, $type, $exclude);
	// $childFormModel = new AuthChildForm();
	 $data = $childDataProvider->getData();

    $this->renderPartial('_tabs', array('model'=>$model,'id'=>$id)); 

 ?>
<div class="tab-content">
   <div class="row-fluid">
                    <div class="span6">
                        <div class="head clearfix">
                            <div class="isw-users"></div>
                            <h1><?php echo Tk::g('权限');?></h1>
                        </div>

                        <div class="block-fluid users">              
<?php if(count($data)>0):?>
		<?php foreach ($data as $key => $value) :?>
                                <div class="item clearfix">
                                    <div class="info">
                                        <?php
                                            echo CHtml::link($value['description']

                                                // ,array('show','id'=>$id,'child'=>urlenc\ode($crypt->encrypt($value['name'])))
                                                ,"#"

                                                 ,array('class'=>'name data-ajax--','title'=>Tk::g(array($this->types[$value['type']],' - ',$value['description'])))
                                                );
                                        ?>                                                            
                                        <span>
                                        <?php echo $this->types[$value['type']];?>
                                        </span>
                                        <div class="controls">
                                        <?php
                                        	echo CHtml::link('',array('RemoveChild','id'=>$id,'child'=>urlencode($crypt->encrypt($value['name']))),array('class'=>'icon-remove'));
                                        ?>                    
                                        </div>                                      
                                    </div>                                
                                </div>                                                                      
		  <?php endforeach ?>
<?php else:?>
    <div class="item clearfix">
        <?php echo '还没有分配!';?>
    </div>
<?php
if (count($this->tabs)<=1) {
    Tak::regScript('bodyend-',
    "   intro =new  introJs();
           intro.setOptions({
                    steps: [
                      {
                        element: document.querySelector('#horizontalForm'),
                        intro: '给部门分配需要的权限.',
                        position: 'button'
                      }
                    ]
              });
            intro.start();          
    "
    );
}
?>    
<?php endif?>          
                        </div>
                    </div>                
                    <div class="span6">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1><?php echo Tk::g('Add'); ?></h1>
                        </div>
                        <div class="block  clearfix">                  
				<?php if( $childFormModel!==null ): ?>
					<?php $this->renderPartial('_childForm', array(
						'model'=>$childFormModel,
						'itemnameSelectOptions'=>$childSelectOptions,
					)); ?>
				<?php else: ?>
					<p class="info"><?php echo Rights::t('core', 'No children available to be added to this item.'); ?>
				<?php endif; ?>                        
                        </div>
                    </div>
                </div>            
                <div class="dr"><span></span></div>
</div>
<?php
Tak::regScript('bodyend',
"
(function(){
var modid = 'ajax-modal'
, strMod =  '<div id=\"'+modid+'\" class=\"modal hide fade\"  tabindex=\"-1\"> <div class=\"modal-header\"> <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button> <h4 class=\"mhead\"></h4> </div> <div class=\"modal-body\"> </div> </div> '
,mod,mhead,modC
; 
$(document).on('click','.data-ajax', function(event){
    event.preventDefault();
        var t = $(this)
        , url = t.attr('href')
    ;
    if (!mod) {
        mod = $(strMod).appendTo(document.body);
        modC = mod.find('.modal-body');
        mhead = mod.find('.mhead');
    }
    if (mod.attr('data-url')==url) {
        
    }else{
        var _thead = t.attr('title')!=''?t.attr('title'):t.text();
            ;
            mhead.text(_thead);
            modC.html('<div class=\"loading-spinner in\" style=\"width: 200px; margin-left: -100px;\"><div class=\"progress progress-striped active\"><div class=\"bar\" style=\"width: 20%;\"></div></div>').addClass('load-content');
            $.ajax(url).done(function(data) {                
                modC.html(data);
            }) .fail(function(error,i,s) {
                mhead.text('请求错误:'+s);
                 modC.html('<div class=\"alert alert-error\">'+error.responseText+'</div>');
              })
              .always(function() {
                    modC.removeClass('load-content');
                    mod.attr('data-url',url).trigger('k-load');
                    t.trigger('click');
              });
    }
    mod.modal('show');
});
}());
");
?>
</div>