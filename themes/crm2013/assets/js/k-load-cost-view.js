jQuery(function($) {
    $(document).on('click', '.action-fold', function() {
        $(this).parents('.itable').eq(0).toggleClass('action-active');
    }).on('click', '.action-print', function() {
        var html = $(this).parents('.div-over').eq(0).html();
        printHtml(html);
    });
    $('.ibtn-print').on('click', function() {
        var html = $('#wrapper').html();
        printHtml(html);
    });
    var printHtml = function(html) {
        html = html.replace(/itable/ig, 'list');
        newWin = window.open('about:blank', 'printf', 'width=800,height=650,resizable=0,scrollbars=1');
        var htmls = ['<link rel="stylesheet" type="text/css" href="' + CrmPath + '/css/tak-printf.css">', 
            html, '<div class="footer-print"><button onclick="window.print()">打印</button> <button onclick="window.close()">关闭</button</div>'
        ];
        newWin.document.write(htmls.join(''));
    }
});