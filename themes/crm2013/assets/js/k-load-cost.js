jQuery(function($) {
    $(document).on('click', '.action-fold', function() {
        $(this).parents('.itable').eq(0).toggleClass('action-active');
    }).on('click', '.btn-add', function() {
        window.open('list.html?' + (new Date()).toTimeString(), 'newwindow', 'height=600, width=800, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no');
    });
    var initProduct = function(elem) {
        if (elem.attr('data-init')) {
            return false;
        };
        var input = $(elem),
            p = input.parent(),
            tr = p.parent().parent(),
            table = tr.parent(),
            dropdownlist = p.find('.dropdownlist'),
            span = p.find('.iselect'),
            datas = [],
            checkVale = function() {
                return input.attr('data-title') == input.val();
            },
            setProduct = function(data) {
                if (data) {
                    input.val(data.name);
                    input.attr('data-title', data.name);
                    tr.addClass('active-product');
                    tr.find('.product-itemid').val(data.itemid);
                    tr.find('.price').val(data.price).trigger('change');
                    tr.find('.unit').val(data.unit).prop('readonly', true);
                    tr.find('.color').val(data.color).prop('readonly', true);
                    tr.find('.spec').val(data.spec).prop('readonly', true);
                } else {
                    tr.find('.product-itemid').val('');
                    tr.removeClass('active-product').find('input[readonly]').prop('readonly', false);
                    input.attr('data-title', '');
                }
            },
            setValue = function(id) {
                if (id > 0 && datas.length > 0) {
                    var i = 0;
                    queryResult = Enumerable.from(datas).where(function(x) {
                        i++;
                        if (id == x.itemid) {
                            return true;
                        }
                    }).select(function(x) {
                        return x;
                    }).take(1).toObject(function(data) {
                        setProduct(data);
                        return 'data';
                    });
                }
            },
            init = function(qustr) {
                var queryResult = [],
                    html = '',
                    tdata = [];
                dropdownlist.empty().addClass('tips-loading');
                if (tags.length == 0) {
                    html = '仓库中没有产品!';
                } else if (trim(qustr) == '') {
                    html = '请输入要搜索的库存!';
                } else {
                    if (tags.length > 0) {
                        table.find('.product-itemid').each(function() {
                            tdata.push($(this).val());
                        });
                        tdata = tdata.join(',');
                        queryResult = Enumerable.from(tags).where(function(x) {
                            if (qustr == '') {
                                return true;
                            };
                            /*消除已经选择过的库存*/
                            if ((tdata == '' || tdata.indexOf(x.itemid) == -1) && (x.name.indexOf(qustr) >= 0 || (x.color && x.color.indexOf(qustr) >= 0) || (x.material && x.material.indexOf(qustr) >= 0) || (x.spec && x.spec.indexOf(qustr) >= 0))) {
                                return true;
                            }
                        }).select(function(x) {
                            return x;
                        }).toArray();
                    };
                    var htmls = [];
                    for (var i = 0; i < queryResult.length; i++) {
                        htmls.push(sprintf('<li data-id="%s">%s(%s-%s)</li>', queryResult[i].itemid, queryResult[i].name, queryResult[i].spec, queryResult[i].color));
                    };
                    if (htmls.length > 0) {
                        datas = queryResult;
                        html = '<ul>' + htmls.join('') + '</ul>';
                    } else {
                        datas = [];
                        html = '<b>库存中不存在!</b>';
                    }
                }
                dropdownlist.html(html).removeClass('tips-loading');
            };
        input.on('keyup', function(event) {
            if (!tr.hasClass('dropdownlist-active')) {
                return true;
            };
            var t = $(this);
            p = t.parent();
            if (event.which == 13) {
                v = dropdownlist.addClass('vhide').find('.light');
                if (v.length > 0) {
                    t.val(v.text());
                };
            } else {
                qustr = t.val();
                init(qustr);
            }
        }).on('change', function() {
            if (!checkVale()) {
                setProduct(false);
            } else {}
        });
        span.on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            if (!tr.hasClass('dropdownlist-active')) {
                if (!checkVale()) {
                    init(input.val());
                };
                $('.dropdownlist-active').removeClass('dropdownlist-active');
            } else {}
            tr.toggleClass('dropdownlist-active');
            if (tr.hasClass('dropdownlist-active')) {
                input.focus();
            }
        });
        dropdownlist.on('click', 'li', function(event) {
            var t = $(this);
            setValue(t.attr('data-id'));
            dropdownlist.find('.light').removeClass('light');
            t.addClass('light');
            span.trigger('click');
        });
        elem.attr('data-init', true);
        if (typeof arguments[1] != 'undefined') {
            input.attr('data-title', 'false');
            span.trigger('click');
        };
    }
    $('.product-id').each(function(i, el) {
        initProduct($(el));
    });
    $(document).on('click', '.iselect', function() {
        var t = $(this).parent();
        if (!t.attr('data-init')) {
            // $(this).unbind('click');
            initProduct(t.parent().find('.product-id'), true);
            t.attr('data-init', true);
        } else {}
        return true;
    });
    var form = $('#form-const'),
        ifm = getIfm(),
        isok = false;
    window.showError = function(data) {
        if (data.length > 0) {
            var ids = 'input[name="' + data.join('"],input[name="')
            var list = $(ids).addClass('error');
            parent.gotoElem(list.eq(0));
        };
    }
    window.showOk = function() {
        window.location.href = '';
    }
    form.find('.ibtn-ok').on('click', function(event) {
        isok = false;
        form.find('input[required]').each(function(i, elem) {
            var t = $(elem),
                v = t.val();
            if (v == '' || v == 0) {
                t.addClass('error');
                t.parents('.table-product').eq(0).removeClass('action-active');
                if (!isok) {
                    isok = t;
                };
            } else {
                t.removeClass('error');
            }
        });
        if (isok) {
            isok.focus();
            event.preventDefault();
            return false;
        }
    });
    form.attr('target', ifm.attr('name'));
    form.on('submit', function(event) {
        var itemid = $('#itemid').val();
        itemid += itemid != '' ? '-订单' : '';
        name = prompt('输出保存的标题', itemid);
        if (name != 'null') {
            $('#cname').val(name);
        };
    });
});