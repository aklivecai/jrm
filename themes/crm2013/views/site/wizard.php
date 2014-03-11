<link href="/_/_/smart-wizard/styles/smart_wizard.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/_/_/smart-wizard/js/jquery.smartWizard-2.0.min.js"></script>
<?php
  $this->pageTitle = Yii::app()->name . ' - 帮助中心';
  $this->breadcrumbs=array(
    '帮助中心 ',
  );

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
            <div id="step-1">

            <div class="">
                            <div class="toolbar nopadding-toolbar clearfix">
                                <h4>单据信息</h4>
                            </div>               
                            <div class="row-form clearfix">
                              <div class="span2">
                                <?php echo $form->labelEx($model,'typeid');?>
                              </div>
                              <div class="span5">
                                <?php echo $form->dropDownList($model,'typeid',$types);?>
                              </div>
                            </div>
                            <div class="row-form clearfix">
                               <div class="span2">
                                 <?php echo $form->labelEx($model,'warehouse_id');?>
                               </div>
                               <div class="span5">
                                 <?php echo $form->dropDownList($model,'warehouse_id',$warehouses);?>
                               </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span2">
                                  <?php echo $form->labelEx($model,'time'); ?>
                                </div>
                                <div class="span5">
                                <?php echo $form->dateField($model,'time',array('required'=>'required','size'=>10,'maxlength'=>10,'value'=>($model->time>0?Tak::timetodate($model->time):''))); ?>
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
                                <?php echo $form->textField($model,'enterprise',array('size'=>60,'maxlength'=>100)); ?>
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
                            <div class="row-form clearfix">
                            </div>
                      </div>
          
          </div>
            <div id="step-2">

                        <div class="row-form clearfix">
      <table cellpadding="0" cellspacing="0" width="100%" class="table" >
          <thead>
            <tr>
              <th>产品</th>
              <th width="80">规格</th>
              <th width="80">材料</th>
              <th width="80">颜色</th>
              <th width="80">单价</th>
              <th width="80">数量</th>              
              <th width="80">备注</th>
              <th width="30">移除</th>
            </tr>
          </thead>
          <tbody class="not-mpr" id="product-movings">
          <?php echo $strProducts;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3">
                    <div>
                            <input type="text" class="sele1ct-product" placeholder="搜索产品" />
                    </div>
              </td>
              <td colspan="5"></td>
            </tr>
          </tfoot>
        </table>
                            <span class="bottom">显示对应仓库的产品(库存不大于0的标记<span class="red">红色.</span>不能选择)</span>
                        </div>
          </div>
      </div>


<?php

  $this->endWidget(); 
  // $this->regScriptFile('k-load-movings.js?t=1', CClientScript::POS_END);
Tak::regScript('end','
        $("#wizard").smartWizard({
              selected: 0,  
              errorSteps:[2],
              labelNext:"下一步", 
              labelPrevious:"上一步",
              labelFinish:"提交", 
              onFinish:submitAction,
              transitionEffect:"slideleft",
              onLeaveStep:leaveAStepCallback,
              // onFinish:onFinishCallback,
              enableFinishButton:true
        });
      function leaveAStepCallback(obj){
        var step_num= obj.attr("rel");
        return true;
        // return validateSteps(step_num);
      }

// smartWizard({transitionEffect:"slideleft",onLeaveStep:leaveAStepCallback,onFinish:onFinishCallback,enableFinishButton:true});

        function submitAction(){
            $("#wizard").smartWizard("showMessage","Finish Clicked");
            $("#wizard").smartWizard("setError","0");
        }
  ');
?>