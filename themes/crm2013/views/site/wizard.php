<link href="/_/_/smart-wizard/styles/smart_wizard.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/_/_/smart-wizard/js/jquery.smartWizard-2.0.min.js"></script>
<script type="text/javascript" src="/_/static/doT/doT.min.js"></script>
<script type="text/javascript" src="/_/javascript/linq.js/linq.min.js"></script>
<?php
  $this->pageTitle = Yii::app()->name . ' - 帮助中心';
  $this->breadcrumbs=array(
    '帮助中心 ',
  );

  Category::toHtmlSelect();

  $model = new Movings;
  $types = TakType::items('Sell-type');
  $warehouses =  Warehouse::getSelect();
?>
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'takllist',
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
    )); ?>

        <!-- Smart Wizard -->
        <div id="wizard" class="swMain">
            <ul>
                <li><a href="#step-1">
                    <span class="stepNumber">第</span>
                    <span class="stepDesc">
                    一步<br />
                    <small>单据信息</small>
                    </span>
                </a></li>
                <li><a href="#step-2">
                    <span class="stepNumber">第</span>
                    <span class="stepDesc">
                    二步<br />
                    <small>产品明细</small>
                    </span>
                </a></li>
                <li><div class="steps-finish">
                  完成
                </li>
            </ul>
            <div id="step-1" class="block-fluid">
                            <div class="toolbar nopadding-toolbar clearfix">
                                <h4>单据信息</h4>
                            </div>               
                            <div class="row-form clearfix">
                              <div class="span2">
                                <?php echo $form->labelEx($model,'typeid');?>
                              </div>
                              <div class="span5">
                                <?php echo $form->dropDownList($model,'typeid',$types,array("required"=>"required"));?>
                              </div>
                            </div>
                            <div class="row-form clearfix">
                               <div class="span2">
                                 <?php echo $form->labelEx($model,'warehouse_id');?>
                               </div>
                               <div class="span5">
                                 <?php echo $form->dropDownList($model,'warehouse_id',$warehouses,array("required"=>"required"));?>
                               </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span2">
                                  <?php echo $form->labelEx($model,'time'); ?>
                                </div>
                                <div class="span5">
                                <?php echo $form->dateField($model,'time',array('required'=>'required','size'=>10,'maxlength'=>10,'value'=>($model->time>0?Tak::timetodate($model->time):'')),array("required"=>"required")); ?>
                                </div>
                            </div>              
                            <div class="row-form clearfix">
                                <div class="span2">
                                  <?php echo $form->labelEx($model,'numbers'); ?>
                                </div>
                                <div class="span5">
                                <?php echo $form->textField($model,'numbers',array('size'=>60,'maxlength'=>100)); ?>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span2">
                                  <?php echo $form->labelEx($model,'enterprise'); ?>
                                </div>
                                <div class="span5">
                                <?php echo $form->textField($model,'enterprise',array('size'=>60,'maxlength'=>100,"required"=>"required")); ?>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span2">
                                  <?php echo $form->labelEx($model,'us_launch'); ?>
                                </div>
                                <div class="span5">
                                <?php echo $form->textField($model,'us_launch',array('size'=>60,'maxlength'=>100)); ?>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span2">
                                  <?php echo $form->labelEx($model,'note'); ?>
                                </div>
                                <div class="span5">
                                <?php echo $form->textArea($model,'note',array('size'=>60,'maxlength'=>255)); ?>
                                </div>
                            </div>
          </div>
            <div id="step-2">
          <div class="toolbar nopadding-toolbar clearfix">
              <h4>产品明细</h4>
          </div>               
      <table cellpadding="0" cellspacing="0" width="100%" class="table" >
          <thead>
            <tr>
            <th width="45">移除</th>
              <th>产品</th>
              <th width="80">规格</th>
              <th width="80">材料</th>
              <th width="80">颜色</th>
              <th width="80">单价</th>
              <th width="80">数量</th>
              <th>备注</th>
            </tr>
          </thead>
          <tbody class="not-mpr" id="product-movings">
            <tr id="data-loading">
              <td colspan="8" class="grid-view-loading">...</td>
            </tr>
          <?php echo $strProducts;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3">
            <span class="">
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'link',
    'label' => Tk::g(array('Add','Product')),
    'htmlOptions'=>array('id'=>"addproduct")
)); ?>
      </span>              
              </td>
              <td colspan="5"></td>
            </tr>
          </tfoot>
        </table>
          </div>
      </div>
  <script id="data-row" type="text/x-dot-template">
  {{~it.tags :v:index}}
<tr id="{{=v.itemid}}">
<td><a href="#"><span class="icon-remove"></a></td>
<td class="info">{{=v.name}}</td>
<td class="info">{{=v.spec}}</td>
<td class="info">{{=v.material}}</td>
<td class="info">{{=v.color}}</td>
<td><input type="number" class="stor-txt" name="Product[price][{{=v.itemid}}]" min="0.1" value="{{=v.price}}"/></td>
<td class="info">
  <input type="number" class="stor-txt" name="Product[number][{{=v.itemid}}]" required="required" value="{{=v.number}}" min="1"/></td>
  <td><input name="Product[note][{{=v.itemid}}]" type="text" class="stor-txt" value="{{=v.note}}"/></td>  
  </tr>
  {{~}}
  </script>      
<?php

  $this->endWidget(); 
  // $this->regScriptFile('k-load-movings.js?t=1', CClientScript::POS_END);
Tak::regScript('end','
var  wizard = $("#wizard")
, leaveAStepCallback = function(obj){
        var step_num= obj.attr("rel");
        return validateSteps(step_num);
      }
, validateSteps = function(step){
    var isStepValid = true;
    if(step == 1){
      if(validateStep1() == false ){
          isStepValid = false; 
          wizard.smartWizard("showMessage","Please correct the errors in step"+step+ " and click next.");
      }
    }
    return isStepValid;
}
, validateStep1 = function(){
  var isStepValid = false;
    if (valdata($("#step-1"))) {
      isStepValid = true;
    }
    return isStepValid;
}
, valdata = function(elem){
  var isStepValid = true;
  elem.find("[required]").each(function(i,el){
    var t = $(el);
    if (t.val()=="") {
      t.addClass("error");
      isStepValid = false;
    }
  });
  return isStepValid;
}
;
  wizard.smartWizard({
              selected: 0,  
              errorSteps:[0],
              labelNext:"下一步", 
              labelPrevious:"上一步",
              labelFinish:"提交", 
              onFinish:submitAction,
              // transitionEffect:"slideleft",
              onLeaveStep:leaveAStepCallback,
              // onFinish:onFinishCallback,
              enableFinishButton:true
        });

// smartWizard({transitionEffect:"slideleft",onLeaveStep:leaveAStepCallback,onFinish:onFinishCallback,enableFinishButton:true});
        function submitAction(){
            $("#wizard").smartWizard("showMessage","Finish Clicked");
            $("#wizard").smartWizard("setError","0");
        }
  $("#addproduct").on("click",function(){
    var wurl = createUrl("Product/window",["warehouse_id="+$("#Movings_warehouse_id").val()]);
      window.open(wurl, "windowName" ,"width=800,height=650,resizable=0,scrollbars=1");
  });
var data = []
, tempFn = doT.template(document.getElementById("data-row").innerHTML)
;
for (i=0; i <3 ; i++) { 
  data.push({
      "itemid":i,
      "color":"绿色",
      "note":"备注信息",
      "number":500,
      "price":"12.55",
      "spec":"12x6"+i,
      "name":"xxx...",
      "material":"12*"
  });
}
var temp = tempFn({"tags":data});
  $("#product-movings").append(temp).find("#data-loading").remove();
');
?>