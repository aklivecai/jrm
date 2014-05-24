
  $("#addproduct").on("click",function(){
    var wurl = createUrl("Product/window",["warehouse_id="+$("#Movings_warehouse_id").val()]);
      window.open(wurl, "windowName" ,"width=800,height=650,resizable=0,scrollbars=1");
  });
var data = []
, dataObject = []
, tempFn = doT.template(document.getElementById("data-row").innerHTML)
, setObj = function(id){
  if (getObj(id)) {
    return false;
  }else{
    dataObject["x"+id] = true;  
  }
  return true;  
}
, getObj = function(id){
  return  typeof dataObject["x"+id] != "undefined";
}
, delObj = function(id){
  if (getObj(id)) {
    delete dataObject["x"+id];
  }
}
, countObj = function(){
  return dataObject.length;
}
, loadData = function(_data){
  var temp = tempFn({"tags":_data});
  $("#product-movings").append(temp).find("#data-loading").remove();
  $("#wizard .stepContainer").attr("style","");  
}
;
for (i=100; i <102 ; i++) { 
  // setObj(i);
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
// loadData(data);
  window.addData = function(odata){
    var tdata = []
    , len = odata.length
    ;
    for (i=0; i < len; i++) { 
      if (setObj(odata[i]["itemid"])) {
        tdata.push(odata[i]);
      }
    }
    if (tdata.length>0) {
      loadData(tdata);
    }
    return true;
  }

$(document).on("click","#product-movings .data-remove",function(){
  if (sCF("是否确认移除?")) {
    return false;
  }
  var id = $(this).attr("id");
  delObj(id);
  $(this).parents("tr").remove();
});

var getIfm = function(){
  var ifmname = "ifm" + Math.random()
  , ifm = $('<iframe src="about:blank" style="position: absolute;top:-9999;" width="2" height="1" frameborder="0" name="'+ ifmname +'">');
  ifm.appendTo($(document.body));
  return ifm;
}

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
          wizard.smartWizard("setError",{stepnum:step,iserror:true}); 
        }else{
          wizard.smartWizard("setError",{stepnum:step,iserror:false});
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
  var isStepValid = true
  , message = "";
  ;
  elem.find("[required]").each(function(i,el){
    var t = $(el);
    if (t.val()=="") {
      t.addClass("error");
      isStepValid = false;
      message+="<li>"+$("label[for="+t.attr("id")+"]").text()+"</li>";
    }else{
      t.removeClass("error");
    }
  });
  if (message!="") {
    message = "<ul><li>下面内内容填写不正确！</li>"+message+"</ul>";
    wizard.smartWizard("showMessage",message);
  }else{
    wizard.find(".close").trigger("click");
  }  
  return isStepValid;
}
;

  function submitAction(){
    if (countObj==0) {
      alert("尚未添加产品型号！");
      return false;
    }
    var message = ""
    , priceMessage = ""
     , reg =/(^[-+]?[1-9]\d*(\.\d{1,2})?$)|(^[-+]?[0]{1}(\.\d{1,2})?$)/;  
    $("#product-movings input[name*=number]").each(function(){
         var val = $(this).val();
        if(val.search(/^[\+\-]?\d+\.?\d*$/)==0&&val>0){
          $(this).removeClass("error");
        }else{
          $(this).addClass("error");
          if (message=="") {
            message = "<li>请输入正确的数量!</li>";
          }
        }
    });
    $("#product-movings input[name*=price]").each(function(){
         var val = $(this).val();
        if(reg.test(val)&&val>0){  
          $(this).removeClass("error");
        }else{
          $(this).addClass("error");
          if (priceMessage=="") {
            priceMessage = "<li>价格必须为合法数字(正数，最多两位小数)！</li>";
          }
        }
    });
    message+=priceMessage;
    if (message!="") {
      message = "<ul>"+message+"</ul>";
      wizard.smartWizard("showMessage",message);
      wizard.smartWizard("setError","1");
      return false;    
    }
  var ifm = getIfm()
  , ifmname = ifm.attr("name")
  ;
  
  wizard.parents("form").attr("target",ifmname);
  wizard.parents("form").submit();
  ifm.on("load",function(){
    // ifm.remove();
  });    
     wizard.smartWizard("setError","0");
  }

  wizard.smartWizard({
              // selected: 1,  
              // errorSteps:[0],
              labelNext:"下一步", 
              labelPrevious:"上一步",
              labelFinish:"提交", 
              onFinish:submitAction,
              // transitionEffect:"slideleft",
              onLeaveStep:leaveAStepCallback,
              // onFinish:onFinishCallback,
              enableFinishButton:true
        });
