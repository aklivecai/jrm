<?php
Tak::regScriptFile($this->createUrl('/iak/index') , '', null, CClientScript::POS_END);
$scrpitS = array(
    '_ak/js/advanced/linq.min.js',
    '_ak/js/advanced/knockout-3.1.0.js',
    '_ak/js/lib.js',
    '_ak/js/plupload/plupload.full.min.js',
    '_ak/js/plupload/i18n/zh_CN.js',
);
Tak::regScriptFile($scrpitS, 'static', null, CClientScript::POS_END);

$scrpitS = array(
    'k-load-cost.js',
    'mvc-cost.js',
);
$this->regScriptFile($scrpitS, CClientScript::POS_END);

Tak::regScript('footer', '
var tags = []
, products = ' . CJSON::encode($products) . '
, uploadUrl = "' . $this->createUrl('/it/Upload') . '"
;	
', CClientScript::POS_HEAD);
?>
		<div id="wrapper">
			<form action="" method="post" id="form-const">
				<div class="mod" id="constac">
					<h2>物料成本清单核算</h2>
					<div class="modc">
						<div data-bind='foreach: {data:lines,afterRender:$root.initElement}'>
							<table class="itable table-product">
								<caption>
								<div class="product-info">
									<label>品 名：<input type="text" class="input-bborder" data-bind="value:type,attr:{name:getName('type')}" required/></label>
									<label>型 号：<input type="text" class="input-bborder" data-bind="value:name,attr:{name:getName('name')}" required/></label>
									<label>规格：<input type="text" class="input-bborder" data-bind="value:spec,attr:{name:getName('spec')}" required/></label>
									<label>颜色：<input type="text" class="input-bborder" data-bind="value:color,attr:{name:getName('color')}" required/></label>
				
									<label>制造管理费：<input type="number" class="expenses" min="0" value="0" data-bind="value: expenses,attr:{name:getName('expenses')}"/></label>
									<label>生产数量：<input type="number" class="input-bborder" value="1" data-bind="value:number,attr:{name:getName('numbers')}" required min="1" step="1" /></label>
									<label class="fbold">
										成本单价:￥<input type="text" readonly="readonly" value="0" class="text-show prices" data-bind="value:price,attr:{name:getName('price')}" tabIndex="-1"/>
									</label>
									<label class="fbold">
										总成本:￥<input type="text" readonly="readonly" value="0" class="text-show" data-bind="value:totals,attr:{name:getName('totals')}" tabIndex="-1"/>
									</label>
								</div>
								<div class="fr">
									<a class="icon action-deleted" title="删除">&nbsp;</a>
									<a class="icon action-fold" title="折叠">&nbsp;</a>
								</div>
								</caption>
								<colgroup align="center">
								<col width="80px"/>
								</colgroup>
								<tbody>
								<tr data-bind="template:{name: 'materia-template', data:mainMaterias }"></tr>
							<tr data-bind="template:{name: 'materia-template', data:subMaterias }"></tr>
						<tr data-bind="template:{name: 'process-template', data:process ,afterRender:initUpload}"></tr>
					</tbody>
				</table>
				<hr/>
			</div>
			</div>
			<!-- ko if: msg -->
			<div>
				<strong data-bind="html:msg"></strong>
				<hr />
			</div>
			 <!-- /ko -->

	<div class="footer-action">
			<a tabindex="-1" class="ibtn ibtn-cancel" onclick="window.close()">关闭窗口</a>
			<button class="ibtn" type="button" data-bind="click: add">添加产品</button>
			<button class="ibtn ibtn-ok" type="submit">保存</button>			
	</div>
			<div class="wap-tips">
				<span class="tips_icon_help">
				提示: 核算说明
				</span>
				<div class="tips-mod">
					<ul>
						<li>管理制造费:如本产品需要加人员管理,设备折旧或其他费用可统计在<span class="text-show">制造管理费</span>中;</li>
						<li>输入中,涉及数量,单价,为必填选项,不能为空或者0;</li>
						<li>红色边框为必填选项,不能为空</li>
						<li>上传文件支持: 格式为jpg,gif,png,jpeg,文件大小不要超过5M</li>
				</ul>
			</div>
			</div>
		</div>
		<input type="hidden" id="itemid" value="<?php echo $orderid ?>" />
		<input type="hidden" name='M[name]' id="cname" />
		<input type="hidden" name='M[totals]' data-bind="value:$root.totals" />
</form>
</div>
<a href="about" id="tak-load"></a>
<script type="text/html" id="materia-template">
<th>
	<span data-bind="text: typeName"></span>
	<br />
	<button type="button" class="ibtn" data-bind="click: add">添项</button>
</th>
<td>
	<div class="div-over">
		<table class="itable ilist">
			<colgroup align="center">
			<col width="160px" />
			<col span="6" width="auto"/>
			<col width="110px"/>
			<col width="45px"/>
			</colgroup>
			<thead>
				<tr>
					<th>材料</th>
					<th>规格</th>
					<th>单价</th>
					<th width="65">单位</th>
					<th>用量</th>
					<th>颜色</th>
					<th>备注说明</th>
					<th>合计</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody data-bind='foreach: {data:lines}' data="afterAdd: init">
				<tr>
					<td>
						<div class="tak-combobox">
							<input type="hidden" class="product-itemid" data-bind="value: product_id,attr:{name:getName('product_id')}">
							<input type="text" class="product-id" data-bind='value: name,css: { error: name.hasError },attr:{name:getName("name"),title:name.validationMessage}' required/>
							<span class="iselect">&nbsp;</span>
							<div class="dropdownlist tips-loading">
							</div>
						</div>
					</td>

					<td><input type="text" class="spec" data-bind="value: spec,attr:{name:getName('spec')}"></td>
					<td><input class="price" type="number" step="0.1" min="0" data-bind="value: price,attr:{name:getName('price')}" required/></td>					
					<td><input type="text" class="unit" data-bind="value: unit,attr:{name:getName('unit')}"/></td>
					<td><input class="number" type="number" step="0.1" min="0" data-bind="value: number,attr:{name:getName('numbers')}" required/></td>
					<td><input type="text" class="color" data-bind="value: color,attr:{name:getName('color')}"></td>

					<td><input type="text" class="note" data-bind="value: note,attr:{name:getName('note')}"/></td>
					<td>￥<input type="text" class="text-show total" readonly="readonly" value="0" data-bind="value: total,attr:{name:getName('total')}"  tabIndex="-1"/></td>
					<td><button type="button" class="ibtn btn-del" data-bind="click:$parent.remove">删除</button></td>
				</tr>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="9">
					<div class="txt-left">
						<span data-bind="text: typeName"></span>合计: ￥
						<input type="text" readonly="readonly" value="0" class="text-show" data-bind="value: totals"  tabIndex="-1"/>
					</div>
				</td>
			</tr>
			</tfoot>
		</table>
	</div>
</td>
</script>
<script type="text/html" id="process-template">
<th>
	工序
	<br />
	<button type="button" class="ibtn" data-bind="click: add">添项</button>
</th>
<td>
	<div class="div-over wap-process fl">
		<table class="itable ilist">
			<colgroup align="center">
			<col span="3" width="auto"/>
			<col width="45px"/>
			</colgroup>
			<thead>
				<tr>
					<th>工序设定</th>
					<th>工价</th>
					<th>备注说明</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody data-bind='foreach: {data:lines}' data="afterAdd: init">
				<tr>
					<td>
						<input type="text" data-bind="value: name,css: { error: name.hasError },attr:{name:getName('name'),title:name.validationMessage}" required/>
					</td>
					<td><input class="price" type="number" step="0.1" min="0" data-bind="value: price,attr:{name:getName('price')}" required/></td>
					<td><input  type="text" data-bind="value: note"/></td>
					<td><button type="button" class="ibtn btn-del" data-bind="click:$parent.remove">删除</button></td>
				</tr>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="4">
					<div class="txt-left">
						工序合计: ￥
						<input type="text" readonly="readonly" value="0" class="text-show" data-bind='value: totals'  tabIndex="-1"/>
					</div>
				</td>
			</tr>
			</tfoot>
		</table>
	</div>
  <div class="wap-file" data-bind="with:$parent">
  <input type="hidden"  data-bind="value:file_path, attr:{name:getName('file_path')}"/>
    <strong>产品图片</strong>
         <div data-bind="visible: isfile">
			<div data-bind="attr:{id:getId('container')}">
				<div class="filelist"></div>
			    <a data-bind="attr:{id:getId('pickfiles')}" class="ibtn" href="javascript:;">上传文件</a>
			</div>
        </div>
         <div data-bind="ifnot: isfile" class="img-preview">
    			<a data-bind="attr:{href:file_path}" target="_blank">
    			<img data-bind="attr:{src:file_path}"/>
    		</a>
    		<button type="button" class="ibtn btn-del" data-bind="click:removePic">删除</button>
        </div>
  </div>
</td>
</script>

<!--
<script type="text/javascript" src="js/plupload/plupload.full.min.js"></script>
-->
