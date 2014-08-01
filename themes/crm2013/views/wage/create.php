<?php
$this->breadcrumbs = array(
    Tk::g(array(
        'Wage',
        'Admin'
    )) => array(
        'Index'
    ) ,
    Tk::g('工时录入') ,
);

$scrpitS = array(
    '_ak/js/advanced/knockout-3.1.0.js',
    '_ak/js/lib.js',
);
Tak::regScriptFile($scrpitS, 'static', null, CClientScript::POS_END);

$scrpitS = array(
    'k-load-wage-create.js?tt123',
);
$this->regScriptFile($scrpitS, CClientScript::POS_END);
?>
<div class="row-fluid">
	<div class="head clearfix">
		<div class="isw-grid"></div>
		<h1>工时录入</h1>
	</div>
	<div class="block-fluid wage-create">
	<form action="" method="post" id="wage-form">
		<table cellpadding="0" cellspacing="0" width="100%" class="table" id="wages">
			<colgroup align="center">
			<col width="60px" />
			<col width="80px" />
			<col width="120px" />
			<col width="auto" span="5" />
			<col width="120px" />
			<col width="80px"/>
			<col width="auto" span="4" />			
			</colgroup>
			<thead>
				<tr>
					<th>序号</th>
					<th>工人姓名</th>
					<th>产品</th>
					<th>工单号</th>
					<th>下单日期</th>
					<th>客户</th>
					<th></th>
					<th>数量</th>
					<th>单位</th>
					<th>工序</th>
					<th>工价</th>
					<th>金额</th>
					<th>备注</th>
					<th>完成日期</th>
					<th></th>
				</tr>
			</thead>
			<tbody data-bind="template: { name: 'list-template', foreach: lines,afterAdd: init}"></tbody>
				<tfoot>
				<tr>
					<td colspan="11"><button class="btn" type="button" tabindex="-1" data-bind="click: add">添加</button></td>
					<td colspan="2">
					<strong data-bind="text: totals"></strong>
					</td>
					<td colspan="2"><button type="submit" class="btn">保存</button></td>
				</tr>
				</tfoot>
			</table>
			</form>
		</div>
	</div>
<label></label>
<script type="text/html" id="list-template">
<tr data-bind="attr: {id:uid}">
	<td align="center">
		<span data-bind="text:$index()+1"></span>
	</td>
	<td>
		<div class="tak-combobox">
			<span data-bind="with: worker">
			<input type="hidden"  data-bind="value: id,attr:{name:$parent.getName('worker_id')}">
			<input type="text" data-bind="value: name,attr:{name:$parent.getName('name')}" tabindex="-1" required="required" readonly="readonly">
			</span>
			<span data-bind="ifnot: worker">
			<input type="number" required="required" readonly="readonly"/>
			</span>
			<a data-bind="click:selectWorker">选择</a>
		</div>
	</td>
	<td>
		<div class="tak-combobox">
			<input type="text" data-bind="value: product,attr:{name:getName('product')}" required="required" >
			<a data-bind="click:selectProduct">选择</a>
		</div>
	</td>
	<td><input type="text" data-bind="value: serialid,attr:{name:getName('serialid')}"/></td>
	<td><input type="text" class="type-date"  data-bind="value: order_time,attr:{name:getName('order_time')}"/></td>
	<td><input type="text" data-bind="value: company,attr:{name:getName('company')}"/></td>
	<td>
	<label>型号
		<input type="text"  data-bind="value: model,attr:{name:getName('model')}"/>
	</label>
	<label> 颜色
		<input type="text"  data-bind="value: standard,attr:{name:getName('standard')}"/>
	</label>
	<label>规格
		<input type="text"  data-bind="value: color,attr:{name:getName('color')}"/>
	</label>
		</td>
	
	<td><input type="number" step="any" min="0"  data-bind="value: amount,attr:{name:getName('amount')}"/></td>
	<td><input type="text"  data-bind="value: unit,attr:{name:getName('unit')}"/></td>
	<td>
		<div class="tak-combobox">
			<span data-bind="with: process">
			<input type="hidden" data-bind="value: id,attr:{name:$parent.getName('process_id')}">
			<input type="text" data-bind="value: name,attr:{name:$parent.getName('process')}" tabindex="-1" required="required" readonly="readonly">
			</span>
			<span data-bind="ifnot: process">
			<input type="number" required="required" readonly="readonly"/>
			</span>
			<a data-bind="click:selectPrice">选择</a>
		</div>
	</td>
	<td><input type="number" step="any" min="0" data-bind="value:price,attr:{name:getName('price')}"/></td>
	<td><input type="number" step="any" min="0" readonly="readonly"  data-bind="value:sum,attr:{name:getName('sum')}"/></td>
	<td><input type="text" data-bind="value:note,attr:{name:getName('note')}"/></td>
	<td>
		<input type="text" class="type-date" data-bind="attr:{name:getName('complete_time')}" required="required"/>
	</td>
	<td>
		<a class="btn btn-mini" data-bind="click: $root.remove" href="#" title="取消"><i class="icon-trash"></i></a>
	</td>
</tr>
</script>
