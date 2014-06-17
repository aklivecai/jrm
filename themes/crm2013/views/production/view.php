<?php
$scrpitS = array(
    'k-load-production-view.js',
);
$this->regScriptFile($scrpitS, CClientScript::POS_END);
?>
<div id="wrapper">
    <div class="mod" id="">
        <h2>生产进度详情</h2>
        <div class="modc production-view">
            <table class="itable iedit">
                <colgroup>
                <col width="8%" />
                <col width="auto" />
                <col width="8%" />
                <col width="auto" />
                </colgroup>
                <tbody>
                    <tr>
                        <th>名字：</th>
                        <td><?php echo $model->name ?></td>
                        <th>日期：</th>
                        <td ><?php echo Tak::timetodate($model->add_time, 6) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>客户：</th>
                        <td><?php echo $model->company ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
            <hr/>
            <table class="itable ilist" summary="" width="100%">
                <colgroup>
                <col width="15%" />
                <col width="5%" />
                <col width="auto" />
                </colgroup>
                <thead>
                    <tr>
                        <th>生产产品列表</th>
                        <th>车间</th>
                        <th>工序</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $value): ?>
                    <tr>
                        <td>
                            <?php
    $ul = array(
        '<ol class="list-production">'
    );
    foreach ($value['product'] as $iproduct) {
        $ul[] = sprintf("<li>%s</li>", $iproduct['name']);
    }
    $ul[] = '</ol>';
    echo implode('', $ul);
?>
                        </td>
                        <td class="txt-right">
                            <?php echo $value['name'] ?>
                        </td>
                        <td>
                            <table class="zebra" width="100%">
                                <?php
    $th = array();
    $td = array();
    foreach ($value['process'] as $process) {
        $th[] = sprintf("<th>%s </th>", $process['name']);
        // 实际完成
        $td[] = sprintf("<td>预期时间:%s天<hr /><div style='border:1px solid #00'>进度
    <ul >
        <li>2014-05: 10</li>
        <li>2014-05: 20</li>
        <li>2014-05: 30</li>
        <li>2014-05: 40</li>
        <li>2014-05: 50</li>
    </ul>
    <a class='ibtn btn-add'>添加进度</a>
    <div>
    <div>
        <label class=''>进度:
        <input type='text' class='progress'/>
        </label>
        <button class='ibtn ibtn-cancel'>取消</button>
        <button class='ibtn ibtn-save'>保存</button>
        <hr />
        </div>
        <button class='ibtn ibtn-ok'>工序已完成</button>
    </div>
    </div>
    </td>", $process['value']);
    }
?>
                                <thead>
                                    <tr>
                                        <?php echo implode('', $th); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php echo implode('', $td); ?>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
endforeach
?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer-action not-printf">
        <button class="ibtn-print ibtn" onclick="window.print()">打印当前页面</button>
    </div>
</div>