/** 
 dateFormat('yyyy-MM-dd hh:mm:ss');
dateFormat(new Date(), 'yyyy-MM-dd hh:mm:ss');
 */
if (typeof window['log']=='undefined'){
    var log = function(msg){        
        if (typeof window['console']=='undefined') return false;
        var len = arguments.length;
        if (len>1) {
            for(var i=0;i<len;i++){
                log(arguments[i]+'\n');
            }
        }else{
            console.log(msg);    
        } 
    }    
}
//用于动态生成网址
//$route,$params=array(),$ampersand='&'
var createUrl = function(route)
{
    if(!CrmPath){
        return false;
    }
    var ampersand = typeof arguments[2]!='undefined'?arguments[2]:'&'
    , params = typeof arguments[1]!='undefined'?arguments[1]:[]
    ;
    if(!route || route == "undefined"){
        return CrmPath;
    }

    // var url = CrmPath + (CrmPath.indexOf('?')>0?'':'?');
    var url = CrmPath;
    url += route;
    url = url + (url.indexOf('?')>0?'':'?');
    if (params.length>0) {
        url += ampersand+params.join(ampersand);
    };
    if (url.indexOf('?&')>0) {
        url = url.replace('?&','?');
    };
    return url;
}
,dateFormat = function(date, format) {
    if(format === undefined){
        format = date;
        date = new Date();
    }
    var map = {
        "M": date.getMonth() + 1, //月份 
        "d": date.getDate(), //日 
        "h": date.getHours(), //小时 
        "m": date.getMinutes(), //分 
        "s": date.getSeconds(), //秒 
        "q": Math.floor((date.getMonth() + 3) / 3), //季度 
        "S": date.getMilliseconds() //毫秒 
    };
    format = format.replace(/([yMdhmsqS])+/g, function(all, t){
        var v = map[t];
        if(v !== undefined){
            if(all.length > 1){
                v = '0' + v;
                v = v.substr(v.length-2);
            }
            return v;
        }
        else if(t === 'y'){
            return (date.getFullYear() + '').substr(4 - all.length);
        }
        return all;
    });
    return format;
}  
, affirm = function(){
    var btn = $('#btn-affirm')
        , txt = btn.text()
        , url = btn.attr('href')
    ;
    if (confirm("是否"+txt+"?\n"+txt+"后将不可以修改。")) {
        if (url) {
            window.location.href = url;
        }else{
            return true;    
        }
     }
     return false;
}

jQuery(function($){
var wapConct = $('#content');

if ($('.more-list').length>0) {
    $(document).on('click','.more-list>a',function(){
        var t = $(this)
        , wap = t.parent()
        , loadwap = t.next('.dropdown-menu')
        , url = wap.attr('data-geturl')
        ;
        if (!loadwap.hasClass('load-over')) {
            $.ajax(url).done(function(data) {
                if (data!='') {
                    setTimeout(function(){
                        loadwap.replaceWith(data).addClass('load-over');
                    },300);                    
                }else{
                    wap.replaceWith('没有了...');
                }
            });
        };   
    })
};

window.afterListView = function(id,data){
    var t = $('#'+id);
    if ($.fn.yiiListView.settings[id]['kload']) {
        t.find('.pagination li.active>a').trigger('click.yiiListView');  
        $.fn.yiiListView.settings[id]['kload'] = false;
    };    
}

var searchForm = $('#search-form');
$('.more-search').on('click',function(){
    $(this).toggleClass('active');
    searchForm.find('.more-search-info').toggleClass('hide');
});

searchForm.find('.btn-reset').on('click',function(){
    // $('#'+searchForm.attr('to-view')+' > div.keys').attr('title');
    searchForm.find('input[type=reset]').trigger('click');
    searchForm.trigger('submit');
})

window.kloadCGridview = function(action,data){
    var t = $('#' + action)
    ;
        if ($(document.body).attr('data-'+action)) {
            var url = t.yiiGridView('getUrl'),
            params = $.deparam.querystring($.param.querystring(url));

            _turl = decodeURIComponent($.param.querystring(url.substr(0, url.indexOf('?')), params));
            
            window.History.pushState(null, document.title, _turl); 
           $(document.body).attr('data-'+action,false);
        };
}
searchForm.on('submit',function(event){
    event.preventDefault();
    var action = searchForm.attr('to-view')?searchForm.attr('to-view'):'list-grid'
        , data = {'data': searchForm.serialize()};
    ;
    if(action.indexOf('grid')>=0){
        $.fn.yiiGridView.update(action,data);
        if (typeof History!='undefined') {
            $(document.body).attr('data-'+action,true);
        }
    }else{
        $.fn.yiiListView.settings[action]['kload'] = true;
        $.fn.yiiListView.update(action,data);
        // log($.fn.yiiListView.getUrl(action));
        // log($('#'+action+'#list-views .pagination li.active>a'));        
        // $(document).trigger('click.yiiListView',action);
    }
    return false;
});

// 
var  modid = 'myModal'
, strMod =  '<div id="'+modid+'" class="modal hide fade"> <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> <h4 class="mhead"></h4> </div> <div class="modal-body"> </div> </div> '
, mod,modc,mhead
;

if($('#'+modid).length==0){
    mod = $(strMod).appendTo(document.body);
    mod.modal('hide');  
    modC = mod.find('.modal-body');
    mhead = mod.find('.mhead');
    mod.on('k-load',function(){
             modC.find('input[class*=select-]').each(function(i,el){
                    loadSelects($(el));
             });  
    });
}

$(document).on('click','#list-views tbody tr',function(){
    var t = $(this);
    t.toggleClass('active');
}).on('click','.data-preview',function(event){
    event.preventDefault();
    var t = $(this)
        , url = t.attr('data-url')?t.attr('data-url'):t.attr('href')
    ;
    if (mod.length==0) {
        mod = $(strMod).appendTo(document.body);
        mod.modal({});
    }    
    if (mod.attr('data-url')==url) {
        mod.modal('show');
    }else{
        var _thead = t.attr('title')!=''?t.attr('title'):t.text();
            ;
        mhead.text(_thead);
        modC.html('...').addClass('load-content');
        $.ajax(url).done(function(data) {
            modC.removeClass('load-content');
            modC.html(data);
            mod.attr('data-url',url).trigger('k-load').modal('show');
            t.trigger('click');          
        })
    }
}).on('click','li a.delete',function(){
        var str = $(this).attr('data-title') ? ' ['+$(this).attr('data-title')+'] ' : '这个信息';
        if(!confirm('你确定要删除'+str+'吗?')) return false;
    }).on('click','a.icon-remove',function(){
        if(!confirm('你确定要删除信息吗?')) return false;
    }).on('click','.to-seas',function(){
        if(!confirm('你确定要把这个信息仍进公海吗?')) return false;
    }).on('click','.navigation .openable > a',function(event){
        event.preventDefault();
        var par = $(this).parent('.openable');
        var sub = par.find("ul");

        if(sub.is(':visible')){
            par.find('.popup').hide();
            par.removeClass('active');
        }else{
            par.addClass('active');            
        }        
        return false;
}).on('click','.more-list li a,.ajax-content',function(event){
    event.preventDefault();
    wapConct.addClass('load-content');
    var url = $(this).attr('href');
    $.ajax(url).done(function(data) {
        setTimeout(function(){
            window.History.pushState(null, document.title, url); 
            wapConct.html(data).removeClass('load-content');
        },300);
    });
});
// 
$('[data-preview]').on('click',function(){
    window.open ($(this).attr('data-preview'), 'preview', 'height=350, width=450, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no') //这句要写成一行
});

$(document).on('submit','#'+modid+' form',function(event){
    event.preventDefault();
    var t = $(this)
    ;
    if (!confirm('是否确认 '+ mhead.text() +'?')) return false;
    $.ajax({
            url: t.attr('action'),
            type: t.attr('method'),
            data: t.serialize() ,
            success: function (data) {
                if (data!='') {
                    modC.html(data).trigger('k-load');
                }else{
                    mod.attr('data-url','').modal('hide').trigger('k-over');
                }
                return false;
                if (data !== null && typeof data === 'object') {
                    
                } else {
                    mod.show();    
                }
            }
        });    
})

var btnAffirm = $('#btn-affirm');
if (btnAffirm.length>0) {      
    btnAffirm.on('click',function(event){
        event.preventDefault();
        affirm();
    }).trigger('click');
};
    

    if (!Modernizr.input.required) {
        $('form').on('submit',function(event){
            var t = $(this)
            , list = t.find('input[required]')
            , errorElem = false
            ;
            if (list.length>0) {
                list.each(function(i,elem){
                    if($(elem).val()==''){
                        $(elem).addClass('error');
                        if (!errorElem) {
                            errorElem = $(elem);
                        };
                    }else{
                        $(elem).removeClass('error');
                    }
                });
                if (errorElem) {
                    event.preventDefault();
                    errorElem.onfocus();
                };
            }
            return true;
        })
    }

    if (!Modernizr.inputtypes.date) {
        $('input[type=date]').each(function(i,elem){
            if ($(elem).val()==0) {
                $(elem).val('');
            };
          $(elem).on('focus',function(){
            WdatePicker({minDate:'%y-%M-{%d+0}'});
          });
        })
    };

  var listDate = $('.type-date');
  if (listDate.length>0) {
        var _DateFormat = {
            'time':{
                    startDate:'%y-%M-01 00:00:00',
                    dateFmt:'yyyy-MM-dd HH:mm:ss',
                },
            'date':{
                    startDate:'%y-%M-01',
                    dateFmt:'yyyy-MM-dd',
                },
        }
        listDate.each(function(){
            var t = $(this)
              , date = new Date(t.val()*1000)
              , maxDate = t.attr('name').indexOf('sstart')>0?'#F{$dp.$D(\'Events_end_time\')}':''
              , minDate = t.attr('name').indexOf('send')>0?'#F{$dp.$D(\'Events_start_time\')}':''
              , v = t.val()
              , dF = _DateFormat.date
             ;
             if (t.attr('data-type')
                &&typeof(_DateFormat[t.attr('data-type')]!='undefined')) {
                dF = _DateFormat[t.attr('data-type')];
             };
             // 只能选择今天以前的日期(包括今天)
             if (t.attr('data-date-max')=='now') {
                // maxDate = '%y-%M-%d';
             };
             if (t.attr('data-date-min')=='now') {
                // minDate = '%y-%M-%d';
             };
            dF.alwaysUseStartDate = true;
            dF.maxDate = maxDate;
            dF.minDate = minDate;
                // return false;
             if (v!=0&&v!='') {
                if (v>0) {
                    var _tr = dF.dateFmt.toLowerCase();
                    _tr = _tr.replace('mm','MM');
                    t.val(dateFormat(date, _tr));
                }
             }else if(t.attr('data-date')=='now') {
                var today = new Date()
                , day = today.getDate()
                , month = today.getMonth() + 1
                , year = today.getFullYear()
                , _d = year + "-" + (month<=9?'0':'') + month + "-" + (day<=9?'0':'')+day
                ;
                t.val(_d);
            }else{
                t.val('');
            }
            (function(__df){
                t.on('focus',function(){
                    __df.el = t.attr('id');
                    WdatePicker(__df);
                });
            })(dF);
        });
  };  


   var  afterDelete = function(){}
   , refreshGridView = function(){
     jQuery('#list-grid').yiiGridView('update');
   }
   ;

    $('.delete-select').on('click',function(event){
        event.preventDefault();
        var arr = $.fn.yiiGridView.getSelection('list-grid');
        if (arr.length==0) {
            alert('请选则需要删除的信息!');
           
        }else if(!confirm('你确定要删除选择的'+arr.length+'信息')){

        }else{        
            jQuery('#list-grid').yiiGridView('update', {
                type: 'POST',
                url: jQuery(this).attr('href'),
                success: function(data) {
                   refreshGridView();
                    afterDelete(th, true, data);
                },
                error: function(XHR) {
                    return afterDelete(th, false, XHR);
                }
            });
        }
        return false;
    });
    $('.refresh').on('click',function(event){
        event.preventDefault();
        refreshGridView();
        return false;
    });
    $('.logout').on('click',function(event){
        if (!confirm('是否确认退出？')) {
            event.preventDefault();
            return false;
        };
    })

    $("div[class^='span']").find(".row-form:first").css('border-top', '0px');
    $("div[class^='span']").find(".row-form:last").css('border-bottom', '0px');            
    
    // collapsing widgets    
        $(".toggle a").click(function(){            
            var box = $(this).parents('[class^=head]').parent('div[class^=span]').find('div[class^=block]');
            if(box.length == 1){                
                if(box.is(':visible')){                          
                    if(box.attr('data-cookie'))                    
                        $.cookies.set(box.attr('data-cookie'),'hidden');                                      
                    $(this).parent('li').addClass('active');
                    box.slideUp(100);                    
                }else{                    
                    if(box.attr('data-cookie'))                    
                        $.cookies.set(box.attr('data-cookie'),'visible');                                        
                    $(this).parent('li').removeClass('active');
                    box.slideDown(200);                    
                }
            }            
            return false;
        });
    
    
    $(".header_menu .list_icon").click(function(){        
        var menu = $("body .wrapper .menu");            
        if(menu.is(":visible")){
            menu.fadeOut(200);
            $("body > .modal-backdrop").remove();
        }else{
            menu.fadeIn(300);
            $("body").append("<div class='modal-backdrop fade in'></div>");
        }        
        return false;
    });
    
    if($(".adminControl").hasClass('active')){
        $('.admin').fadeIn(300);
    }
    
    
    $(".adminControl").click(function(){        
        if($(this).hasClass('active')){            
            $.cookies.set('b_Admin_visibility','hidden');            
            $('.admin').fadeOut(200);            
            $(this).removeClass('active');            
        }else{            
            $.cookies.set('b_Admin_visibility','visible');            
            $('.admin').fadeIn(300);            
            $(this).addClass('active');
        }
        
    });

    
    $(".alert").on('click',function(){
        $(this).fadeOut(300, function(){            
            $(this).remove();            
        });
    });
    
    $(".buttons li > a").click(function(){        
        var parent   = $(this).parent();        
        if(parent.find(".dd-list").length > 0){        
            var dropdown = parent.find(".dd-list");
            if(dropdown.is(":visible")){
                dropdown.hide();
                parent.removeClass('active');
            }else{
                dropdown.show();
                parent.addClass('active');
            }
            return false;            
        }        
    });

    // Wizard
    
    if($("#wizard_validate").length > 0){
        
        // $.fn.stepy.defaults.validate = true;
        // $.fn.stepy.defaults.titleClick = true;

        $('#wizard_validate').stepy({
            duration  : 400,
            // validate  : true,
            transition: 'fade',
            nextLabel:'下一步',
            backLabel:'上一步',
            back: function(index) {                                                                
                // if(!$("#wizard_validate").validationEngine('validate')) return false; //uncomment if u need to validate on back click                
            }, 
            next: function(index) {                
                // if(!$("#wizard_validate").validationEngine('validate')) return false;                
            }, 
            finish: function(index) {                
                // if(!$("#wizard_validate").validationEngine('validate')) return false;
            }            
        });
    }
    // eof wizard     

});

$(window).load(function(){
    headInfo();    
});
$(window).resize(function(){
    headInfo();    
    if($("body").width() > 980){        
        $("body .wrapper .menu").show();
        $("body > .modal-backdrop").remove();
    }else{
        $("body .wrapper .menu").hide();
        $("body > .modal-backdrop").remove();
    }        
});

$('.wrapper').resize(function(){    
    if($("body > .content").css('margin-left') == '220px'){
        if($("body > .menu").is(':hidden'))
            $("body > .menu").show();
    }    
    headInfo();
});

function headInfo(){
    var block = $(".headInfo .input-append");
    var input = block.find("input[type=text]");
    var button = block.find("button");    
    input.width(block.width()-button.width()-44);
}