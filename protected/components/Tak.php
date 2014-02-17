<?php 
class Tak extends Ak{  
    /*获取操作数*/
    public static function getOM(){
        $ip = Yii::app()->user->getState('ip')!=''?Yii::app()->user->getState('ip'):false;
        if (!$ip) {
            $ip = self::getIps();
            Yii::app()->user->setState('ip', $ip);   
        }
        // self::KD($ip);
        // self::KD($ip,1);
        $arr = array(
            'time' => self::now()
            ,'ip'  => $ip
            ,'itemid' => self::fastUuid()
            ,'manageid' => self::getManageid()
            ,'fromid' => self::getFormid()
        );
        return $arr;
    }


    public static function giiColAdmin($col){
        $result = self::giiCol($col);
        if (!$result) {
            $result = strpos('::,status,display,modified_time,',",$col,")>0;
        }
        return $result;
    }
    public static function giiColNot($col){
        $result = self::giiCol($col);
        if (!$result) {
            $result = strpos('::,add_time,modified_time,last_time,',",$col,")>0;
        }
        return $result;  
    }

    public static function giiCol($col){
        $result = strpos('::,itemid,fromid,manageid,add_us,add_ip,modified_us,modified_ip,',",$col,")>0;
        return $result;
    }

    public static function getEditMenu($itemid,$isNewRecord=true)
    {
           $items = array(  
                'Save' => array(
                  'icon' =>'isw-edit',
                  'url' => 'javascript:;',
                  'label'=>Tk::g('Save'),
                  'linkOptions'=>array('class'=>'save','submit'=>array()),
                )
            );
            if ($isNewRecord) {
                $action = 'Create';
            }else{
                $action = 'Update';
                $items['View'] = array(
                      'icon' =>'isw-zoom',
                      'url' => array('view','id'=>$itemid),
                      'label'=>Tk::g('View'),
                    );
                $items['Create'] = array(
                      'icon' =>'isw-plus',
                      'url' => array('create'),
                      'label'=>Tk::g('Create New'),
                    );
                $items['Delete'] = array(
                      'icon' =>'isw-delete',
                      'url' => array('delete','id'=>$itemid),
                      'label'=>Tk::g('Delete'),
                      'linkOptions'=>array('class'=>'delete'),
                    );
            }
            array_push($items
                ,array(
                  'icon' =>'isw-refresh',
                  'url' => Yii::app()->request->url,
                  'label'=>Tk::g('Refresh'),
                )
                ,array(
                  'icon' =>'isw-left',
                  'url' => ''.Yii::app()->request->urlReferrer,
                  'label'=>Tk::g('Return'),
                )
            ); 
        return $items;       
    }

    public static function getViewMenu($itemid){
        $items = array(
            'Action' => array('label'=>Tk::g('Action'), 'icon'=>'fire', 'url'=>'', 'active'=>true),
            'View' => array('label'=>Tk::g('View'), 'icon'=>'eye-open'),
            'Admin' => array('label'=>Tk::g('Admin'), 'icon'=>'th','url'=>array('admin')),
            'Create' => array('label'=>Tk::g('Create'), 'icon'=>'pencil','url'=>array('create')),
            'Update' => array('label'=>Tk::g('Update'), 'icon'=>'edit','url'=>array('update', 'id'=>$itemid)),
            '---',
            'Delete' => array('label'=>Tk::g('Delete'), 'icon'=>'trash','url'=>array('delete', 'id'=>$itemid),'linkOptions'=>array('class'=>'delete')),
        );
        return $items;    
    }

    public static function getListMenu(){
       $listMenu = array(  
            'Create'=>array(
              'icon' =>'isw-plus',
              'url' => array('create'),
              'label'=>Tk::g('Create'),
            )
            );
       if(self::checkSuperuser()){
            $listMenu['Recycle'] = array(
              'icon' =>'isw-delete',
              'url' => array('recycle'),
              'label'=>Tk::g('Recycle'),
            );
        }
         /* ,array(
              'icon' =>'isw-edit',
              'url' => '#',
              'label'=>Tk::g('Update'),
              'linkOptions'=>array('class'=>'edit'),
            )    
            ,array(
              'icon' =>'isw-delete',
              'url' => '#',
              'label'=>Tk::g('Delete'),
              'linkOptions'=>array('class'=>'delete-select','submit'=>array('click'=>"$.fn.yiiGridView.update('menu-grid');")),
            )*/

         $listMenu['Refresh'] =array(
              'icon' =>'isw-refresh',
              'url' => Yii::app()->request->url,
              'label'=>Tk::g('Refresh'),
              'linkOptions'=>array('class'=>'refresh'),
            );
           
       return $listMenu;    
    }   
    public static function getFileTypeId($file){
        $result = 0 ;
        $type = $file;
        if (strpos($file, '.')>0) {
            $type =explode("." , $file); 
            $count = count($type)-1; 
            $type = $type[$count];
        }
        $type = strtolower($type);
        switch ($type) {   
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
            case 'bmp':
            case 'cdr':
            case 'psd':
            case 'ai':
                $result = 1;

                break;
            case 'rar':
            case 'zip':
            case '7z':
            case 'iso':
            case 'jar':
                $result = 2;
                break;
            case 'doc':
            case 'docx':
                $result = 3;
                break;
            case 'xls':
            case 'xlsx':
                $result = 4;
                break;         
            case 'txt':
                $result = 5;
                break;         
            default:
                $result = 0 ;
                break;
        }

        return $result;
    }

    
    public static function getFileSrc($str,$isWrite='',$dir=''){
        $path_info = pathinfo($str );  
        $extension = $path_info['extension'];
        $file1 = md5($str);
        $file2 = self::getPathBySplitStr($file1);

        if ($dir='') {
            $dir = Yii::app()->params['uploadUser'];
        }
        $dir = str_replace($file1,'',$file2);
        self::MkDirs($dir);
        $file2 = $dir. $file1;
        if ($extension) {
            $file2.= '.'.$extension;
        }

        self::KD($str);
        self::KD($file1);
        self::KD($file2,1);
        if ($isWrite||true) {
           file_put_contents($file2, file_get_contents($str,FILE_USE_INCLUDE_PATH));
        }
        
        return $file2;
    }

    public static function getFileIco($typeid){
        // 2压缩包,3是文档
        $arr = TakType::items('filetype');
        $result = isset($arr[$typeid])?$arr[$typeid]:current($arr);
        $result = Yii::app()->getBaseUrl().'/upload/ui/ico/'.$result.'.png';
        return $result;
    }

    public static function get(){
        $auth = Yii::app()->authManager;
    }

    //左栏菜单 
    public static function getMainMenu(){
        $controlName = Yii::app()->getController()->id;
        $arr = array(
            'manage'=>'manage,permission,'
            ,'addressbook'=>',AddressBook,AddressGroups,'
            ,'events'=>'events'
            ,'file'=>'file'
            ,'invite'=>',invite,'
            ,'job'=>',job,'
            ,'order'=>',order,'
            ,'training'=>',training,'
            ,'training'=>',training,'
            ,'clientele'=>',clientele,contactpPrson,contact,clienteles,'
            ,'Pss'=>',purchase,stocks,product,sell,'
            );
        $items = array(
            array(
              'icon' =>'isw-grid',
              'url' => array('/site/index'),
              'label'=>'<span class="text">主页</span>',
            ),
            array(
              'icon' =>'isw-grid',
              'url' => array('/site/wizard'),
              'label'=>'<span class="text">测试</span>',
              'visible'=>self::getAdmin(),
            ),
            'manage' => array(
              'icon' =>'isw-users',
              'label'=>'<span class="text">'.Tk::g(array('Manage','Setting')).'</span>',
              'url'=>array('/manage/admin'),
               'visible'=>self::checkAccess('Manage.*'),
              'items'=>array(
                    array(
                       'icon' =>'user',
                      'label'=>'<span class="text">'.Tk::g(array('Manage','Permissions')).'</span>', 
                      'url'=>array('/rights/assignment/view'), 
                      'visible'=>self::checkSuperuser()&&YII_DEBUG,
                      'linkOptions'=>array('id'=>'tak-permissions'),
                    ), 
                    array(
                       'icon' =>'user',
                      'label'=>'<span class="text">'.Tk::g(array('Manage','Permissions')).'</span>', 
                      'url'=>array('/permission/admin'), 
                      
                    ), 

                array('icon'=>'plus','label'=>'<span class="text">'.Tk::g(array('Create','Manage')).'</span>',  'url'=>array('/manage/create'),),               
                array('icon'=>'th','label'=>'<span class="text">'.Tk::g(array('Manage','Admin')).'</span>',  'url'=>array('/manage/admin'),),
                // array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/manage/recycle'),),
              ),
            ), 
            'clientele' => array(
              'icon' =>'isw-bookmark',
              'label'=>'<span class="text">我的客户</span>',
              'url'=>array('/clientele/admin'),
              'visible'=>self::checkAccess('Market')||self::checkAccess('Clientele.*'),
              'items'=>array(
                array('icon'=>'plus','label'=>'<span class="text">'.Tk::g(array('Clientele','Create')).'</span>',  'url'=>array('/clientele/create'),),
                array('icon'=>'th','label'=>'<span class="text">'.Tk::g(array('Clientele','Admin')).'</span>',  'url'=>array('/clientele/admin'),
                ),
                array('icon'=>'th','label'=>'<span class="text">联系人管理</span>',  'url'=>array('/contactpPrson/admin'),),
                array('icon'=>'th','label'=>'<span class="text">联系记录</span>',  'url'=>array('/contact/adminGroup'),),
                array('icon'=>'th','label'=>'<span class="text">所有客户</span>',  'url'=>array('/clienteles/'),'visible'=>self::checkAccess('Clienteles.*')),
                array('icon'=>'th','label'=>'<span class="text">客户转移</span>',  'url'=>array('/moves/clienteles'),'visible'=>self::checkSuperuser()),
                array('icon'=>'th','label'=>'<span class="text">公海</span>',  'url'=>array('/clientele/seas'),'visible'=>self::checkAccess('Clientele.*')),
                array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/clientele/recycle'),'visible'=>self::checkSuperuser()),
              ),
            ), 
            'addressbook' => array(
              'icon' =>'isw-archive',
              'label'=>'<span class="text">通讯录</span>',
              'visible'=>self::checkAccess('Addressbook.Index'),
              'items'=>array(
                array('icon'=>'plus','label'=>'<span class="text">'.Tk::g('Create').'部门</span>',  'url'=>array('/AddressGroups/admin'),'visible'=>self::checkAccess('Addressgroups.*'),),
                array('icon'=>'plus','label'=>'<span class="text">'.Tk::g(array('Create','AddressBook')).'</span>',  'url'=>array('/AddressBook/create'),'visible'=>self::checkAccess('Addressbook.*'),),
                array('icon'=>'th-list','label'=>'<span class="text">'.Tk::g(array('Admin','AddressBook')).'</span>', 'url'=>array('/AddressBook/admin'),'visible'=>self::checkAccess('Addressbook.*'),),
                array('icon'=>'th-list','label'=>'<span class="text">'.Tk::g(array('View','AddressBook')).'</span>', 'url'=>array('/AddressBook/index'),'visible'=>self::checkAccess('AddressBook.Index'),),
              ),
            ),
            'events' => array(
              'visible'=>self::checkAccess('Events.*'),
              'icon' =>'isw-calendar',
              'label'=>'<span class="text">'.Tk::g('Events').'事项</span>',
              'url'=>array('/events/index'),
              'items'=>array(
                array('icon'=>'th-list','label'=>'<span class="text">'.Tk::g(array('Events','Admin')).'</span>', 'url'=>array('/events/admin')),
                array('icon'=>'plus','label'=>'<span class="text">'.Tk::g('Events').'事项</span>',  'url'=>array('/events/create'),),
                // array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/events/recycle'),),
              ),
            ), 
           'file' => array(
              'visible'=>self::checkAccess('File.*'),
              'icon' =>'isb-cloud',
              'label'=>'<span class="text">具云盘</span>',
              'url'=>array('/file/index'),
              'items'=>array(
                array('icon'=>'hdd','label'=>'<span class="text">全部文档</span>', 'url'=>array('/file/index')),
                array('icon'=>'picture','label'=>'<span class="text">图片</span>', 'url'=>array('/file/image')),
                array('icon'=>'file','label'=>'<span class="text">文档</span>', 'url'=>array('/file/image')),
                array('icon'=>'film','label'=>'<span class="text">视频</span>', 'url'=>array('/file/video')),
                array('icon'=>'tasks','label'=>'<span class="text">其他</span>', 'url'=>array('/file/other ')),
                array('icon'=>'plus','label'=>'<span class="text">文件上传</span>',  'url'=>array('/file/create'),),
                array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/file/recycle'),),
              ),
            ), 
            
           'pss' => array(
              'visible'=>self::checkAccess('Pss.*')||self::checkAccess('Product.*'),
              'icon' =>'isw-list',
              'label'=>'<span class="text">库存管理</span>',
              'url'=>array('/pss/index'),
              'items'=>array(
                    array('icon'=>'th','label'=>'<span class="text">'.Tk::g('Product Type').'</span>',  'url'=>array('takType/admin?type=product'),),
                 array('icon'=>'th','label'=>'<span class="text">'.Tk::g('Product').'</span>',  'url'=>array('/product/admin'),),

                array('icon'=>'th','label'=>'<span class="text">入库录入</span>', 'url'=>array('/purchase/admin')),
                array('icon'=>'th','label'=>'<span class="text">出库录入</span>',  'url'=>array('/sell/admin'),),
                    array('icon'=>'th','label'=>'<span class="text">'.Tk::g('Stocks').'</span>',  'url'=>array('/stocks/index'),),
              ),
            ), 
           'order' => array(
              'visible'=>self::checkAccess('Order.*'),
              'icon' =>'isw-list',
              'label'=>'<span class="text">'.Tk::g('Order').'</span>',
              'url'=>array('/order/index'),
              'items'=>array(
                array('icon'=>'certificate','label'=>'<span class="text">订单流程</span>',  'url'=>array('/order/config'),),
                array('icon'=>'shopping-cart','label'=>'<span class="text">'.Tk::g(array('Order','Admin')).'</span>', 'url'=>array('/order/admin')),
                array('icon'=>'th-large','label'=>'<span class="text">订单变更</span>',  'url'=>array('/site/order'),'visible'=>YII_DEBUG),
                
                array('icon'=>'certificate','label'=>'<span class="text">协议</span>',  'url'=>array('/order/config'),'visible'=>YII_DEBUG),
                array('icon'=>'pencil','label'=>'<span class="text">自助下单</span>',  'url'=>'http://u.9juren.com/order/cart/'.self::getFormid(),'linkOptions'=>array('target'=>'_blank')),
              ),
            ), 

            'invite' => array(
              'visible'=>self::checkAccess('Minvite.*'),
              'icon' =>'isb-tag',
              'label'=>'<span class="text">招标</span>',
              'url'=>array('/invite/index'),
              'items'=>array(
                array('icon'=>'th-list','label'=>'<span class="text">招标管理</span>', 'url'=>array('/invite/index')),
                array('icon'=>'plus','label'=>'<span class="text">招标录入</span>',  'url'=>array('/invite/create'),),
                array('icon'=>'star','label'=>'<span class="text">投标记录</span>',  'url'=>array('/invite/create'),),
                array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/invite/recycle'),),
              ),
            ),   
           'job' => array(
              'visible'=>self::checkAccess('Mjob.*'),
              'icon' =>'isb-graph',
              'label'=>'<span class="text">招聘</span>',
              'url'=>array('/job/index'),
              'items'=>array(
                array('icon'=>'th-list','label'=>'<span class="text">招聘管理</span>', 'url'=>array('/job/index')),
                array('icon'=>'plus','label'=>'<span class="text">招聘录入</span>',  'url'=>array('/job/create'),),
                array('icon'=>'bookmark','label'=>'<span class="text">收藏的简历</span>',  'url'=>array('/job/create'),),
                array('icon'=>'fire','label'=>'<span class="text">收到的简历</span>',  'url'=>array('/job/create'),),
                array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/file/recycle'),),
              ),
            ), 
          'training' => array(
              'visible'=>self::checkAccess('Mtraining.*'),
              'icon' =>'isb-documents',
              'label'=>'<span class="text">培训</span>',
              'url'=>array('/training/index'),
              'items'=>array(
                array('icon'=>'th-list','label'=>'<span class="text">培训管理</span>', 'url'=>array('/training/index')),
                array('icon'=>'list-alt','label'=>'<span class="text">文档</span>',  'url'=>array('/training/doc'),),
                array('icon'=>'facetime-video','label'=>'<span class="text">视频</span>',  'url'=>array('/job/video'),),
                array('icon'=>'music','label'=>'<span class="text">音频</span>',  'url'=>array('/training/music'),),
                array('icon'=>'trash','label'=>'<span class="text">'.Tk::g('Recycle').'</span>',  'url'=>array('/training/recycle'),),
              ),
            ),           
          );  
        $items['msell'] = array(
              'visible'=>self::checkAccess('Msell.*'),
              'icon' =>'isb-graph',
              'label'=>'<span class="text">供应</span>',
              'url'=>array('/msell/index'),
              'items'=>array(
                array('icon'=>'th-list','label'=>'<span class="text">供应管理</span>', 'url'=>array('/msell/admin')),
                array('icon'=>'plus','label'=>'<span class="text">供应录入</span>',  'url'=>array('/msell/create'),),
              ),
        );
        $items['mbuy'] = array(
              'visible'=>self::checkAccess('Mbuy.*'),
              'icon' =>'isb-graph',
              'label'=>'<span class="text">求购</span>',
              'url'=>array('/msell/index'),
              'items'=>array(
                array('icon'=>'th-list','label'=>'<span class="text">求购管理</span>', 'url'=>array('/msell/admin')),
                array('icon'=>'plus','label'=>'<span class="text">求购录入</span>',  'url'=>array('/msell/create'),),
              ),
        );


        unset($items['file']);
        unset($items['job']);
        unset($items['invite']);
        unset($items['training']);
        unset($items['msell']);
        unset($items['mbuy']);
        // unset($items['events']);

         $items[] = array(
                       'icon' =>'isw-zoom',
                      'label'=>'<span class="text">帮助中心</span>', 
                      'url'=>array('/site/help'), 
                    );
         $items[] = array(
                      'icon' => 'isw-chat',
                      'label' => '<span class="text">系统其他功能</span>', 
                      'url' => Yii::app()->getBaseUrl().'/upload/functionality.jpg', 
                      'linkOptions'=>array('target'=>'_blank')
                    );
         $items[] = array(
                      'icon' =>'isw-target',
                      'label'=>'<span class="text">客户案例</span>', 
                      'url'=>'http://www.9juren.net/', 
                      'linkOptions'=>array('target'=>'_blank')
                    );


        $controlName = Yii::app()->getController()->id;  
        $controlName = strtolower($controlName);

        if (self::checkSuperuser()) {
         $items[] = array(
                      'icon' =>'isw-settings',
                      'label'=>'<span class="text">管理中心</span>',
                      'items'=>array(
                        // array('icon'=>'wrench','label'=>'<span class="text">网站设置</span>', 'url'=>array('/settin/index')),
                        array('icon'=>'list-alt','label'=>'<span class="text">网站日志</span>',  'url'=>array('/adminLog/admin'),),
                         array('icon'=>'fire','label'=>'<span class="text">网站备份</span>',  'url'=>array('/site/Database'),'visible'=>YII_DEBUG),
                         array('icon'=>'fire','label'=>'<span class="text">导入VIP</span>',  'url'=>array('/site/tak'),'visible'=>YII_DEBUG||self::getAdmin()),
                         array('icon'=>'','label'=>'<span class="text">Member</span>',  'url'=>array('/site/tak'),'visible'=>YII_DEBUG),
                      ),
                    );
         $items[] = array(
                      'icon' =>'isw-calc',
                      'label'=>'<span class="text">具人同行商务中心</span>',
                      'url'=>'http://www.9juren.com/member/',
                      'linkOptions' =>array('target'=>'_blank')
                    );
     }        

        // Tak::KD(Yii::app()->getController(),1);
        // Tak::KD(Yii::app(),1);
        // Tak::KD($controlName);
        $tname = '';
        foreach ($arr as $key=>$value)
        {
            $key = strtolower($key);
            $value = strtolower($value);
            if ($controlName==$key||strpos($value,",$controlName,")!==false)
            {
                $tname = $key;
                break;
                
            }
        }
        if ($tname==''&&count(Yii::app()->getController()->breadcrumbs)>0
            &&Yii::app()->getController()->breadcrumbs[0]=='授权') {
            $tname = 'manage';
        }
        if (isset($items[$tname]))
        {
            $items[$tname]['active'] = true; 
        }
        return $items;          
    }

    public static function isRecycle(){
        $result = Yii::app()->getController()->getAction()->id == 'recycle';
        return $result;
    }

    public static function getAdminPageCol($arr=false
        ,$gid='list-grid',$width='60px'){
    
     $items = array(
             'class'=>'bootstrap.widgets.TbButtonColumn'
              ,'header' => CHtml::dropDownList('pageSize'
                    ,Yii::app()->user->getState('pageSize')
                    ,TakType::items('pageSize')
                    ,array(
                        'onchange'=>"$.fn.yiiGridView.update('".$gid."',{data:{setPageSize: $(this).val()}})", 
                        'style'=>'width: '.$width.' !important',
                    )   
              )             
        );
     if (is_array($arr)) {
         // self::KD($arr);
         $items = array_merge_recursive($items, $arr);
     }
     // '.Tk::g('Recycle').'
     // self::KD($control->id);
     if(self::isRecycle()){
        $newItems = array('template'=>'{restore} | {del}'
              ,'buttons'=>array(
                    'restore' => array
                    (
                        'label'=>'',
                         'url'=>'Yii::app()->controller->createUrl("restore", array("id"=>$data->primaryKey))',
                         'options'=>array('title'=>'还原','class'=>'icon-repeat'),
                    ),
                    'del' => array
                    (
                        'label'=>'',
                         'url'=>'Yii::app()->controller->createUrl("del", array("id"=>$data->primaryKey))',
                         'options'=>array('title'=>'彻底删除','class'=>'icon-remove'),
                    ),

              ),
            );
          // 'imageUrl'=>$this->{$id.'ButtonImageUrl'},
        $items = array_merge_recursive($items, $newItems);
     }
     return $items;         
    }

    public static function getTakTypes(){
        $arr = array();
        $arr['product'] = array(
            'name' => 'Product',
            'file' => 'product',   
            'type' => 'product',   
        );

        return $arr;
    }
    public static function getMovingsType($type){

       $types = array(1=>'Purchase',2=>'Sell');
       if (isset($types[$type])) {
          $type = $types[$type];
       }else{
        $type = current($types);
       }
       return $type;
    }
    
    public static function msg($msg,$title='',$type='palert'){
        Yii::app()->clientScript->registerScript('bodyend', "show_stack('$title','$msg','$type')");
    }

    public static function copyright(){
        $arr = array('<div class="hide">');
        $arr[] = Yii::app()->params['copyright'];
        $arr[].= '<script type="text/javascript">
            var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
            document.write(unescape("%3Cscript src=\'" + _bdhmProtocol + "hm.baidu.com/h.js%3Fd98411661088365a052727ec01efb9d8\' type=\'text/javascript\'%3E%3C/script%3E"));
        </script></div>';
        echo join($arr);
    }

    public static function gredViewOptions($btnColumn=true){
      $arr =array(
            'type'=>'striped bordered condensed',
            'id' => 'list-grid',
            'dataProvider'=>null,
            'enableHistory'=>true,
            'afterAjaxUpdate' =>'kloadCGridview',
            'loadingCssClass' => 'grid-view-loading',
            'summaryCssClass' => 'dataTables_info',
            'pagerCssClass' => 'pagination dataTables_paginate',
            'template' => '{pager}{summary}<div class="dr"><span></span></div>{items}{pager}',
            'ajaxUpdate'=>true,    //禁用AJAX
            'enableSorting'=>true,
            'summaryText' => '共 <span class="badge">{pages}</span> 页,当前 <span class="badge badge-success">{page}</span> 页,总数 <span class="badge badge-info">{count}</span> ',
            'pager'=>array(
                'header'=>'',
                'maxButtonCount' => '5',
                'hiddenPageCssClass' => 'disabled'
                ,'selectedPageCssClass' => 'active disabled'
                ,'htmlOptions'=>array('class'=>'')
            ),
            'columns'=>array()
        );
        if ($btnColumn) {
           $arr['columns'] = self::getAdminPageCol();
        }
        return $arr;
    }

    public static function submitButton($label='submit',$htmlOptions=array()){
        $htmlOptions['type']='submit';
        $class = 'btn';
        if (isset($htmlOptions['class'])) {
            $class.=' '.$htmlOptions['class'];
        }
        $htmlOptions['class'] =$class;
        
        return CHtml::tag('button',$htmlOptions,$label);
    }

    public static function writeInfo($key='source',$defaultValue=null){
        $result = '';
        $html = self::getFlash($key,$defaultValue,true);
        if($html){
            $result = CHtml::tag('div',array('class'=>'alert alert-block alert-success'),$html);            
        }
        return $result;
    }

    public static function getNP($nps,$view='view'){
        $result = array();        
        if (!is_array($nps)) {
            return false;
        }
         foreach ($nps as $key => $value) {
            $result[$key] = array(
                'label'=>Tk::g($key),
                'url'=>array($view,'id'=>$value),
                'icon' =>'chevron-right',
                'linkOptions'=>array('class'=>'ajax-content')
            );
         }
        return $result;
    }

    public static function showMsg(){
      $result = self::getFlashes();      
      if ($result) {
        foreach ($result as $key => $value) {
          self::msg($value,'',$key); 
        }         
      }
    }

    public static function tagNum($text,$type=''){
      $badges = array('badge');
      if ($type) {
        $badges[]=$type;
      }
      return CHtml::tag('span',array('class'=>join(' ',$badges)),$text);
    }
}  
