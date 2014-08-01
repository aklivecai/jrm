/**
 *
 * @authors aklivecai (aklivecai@gmail.com)
 * @date    2014-06-11 17:59:46
 * @version $Id$
 */
jQuery(function($) {
    var wap = $('#wamp-wage-workshop');
    var addData = function(data, index) {
        var o = {
            product: data.name,
            serialid: data.serialid,
            order_time: data.add_time,
            company: data.company,
            model: data.model,
            standard: data.standard,
            color: data.color,
            unit: data.unit,
            amount: 1,
        };
        // log(index);
        // log(o);
        list.getLine(index).load(o);
    }, addDepartments = function(data, type, index) {
            var o = {
                'id': data.id,
                'name': data.name,
            }
            if (type == 'worker') {
                o.department_id = data.department_id;
                list.getLine(index).loadWorker(o);
            } else {
                o.price = data.price;
                list.getLine(index).loadProcess(o);
            }
            return true;
        };
    window.addDepartments = addDepartments;
    window.addData = addData;
    ko.extenders.numeric = function(target, precision) {
        var result = ko.computed({
            read: target,
            write: function(newValue) {
                var current = target(),
                    roundingMultiplier = Math.pow(10, precision),
                    newValueAsNum = isNaN(newValue) ? 0 : parseFloat(+newValue),
                    valueToWrite = Math.round(newValueAsNum * roundingMultiplier) / roundingMultiplier;
                if (valueToWrite !== current) {
                    target(valueToWrite);
                } else {
                    if (newValue !== current) {
                        target.notifySubscribers(valueToWrite);
                    }
                }
            }
        }).extend({
            notify: 'always'
        });
        result(target());
        return result;
    };
    var EditViewModel = function() {
        var self = this;
        self.uid = index = uuid();
        self.process = ko.observable(false);
        self.worker = ko.observable(false);
        self.department_id = ko.observable(0);
        self.lines = ko.observableArray();
        self.amount = ko.observable(1).extend({
            numeric: 4,
            rateLimit: 500
        });
        self.price = ko.observable(0).extend({
            numeric: 4,
            rateLimit: 500
        });
        self.note = ko.observable();
        /*总价*/
        self.sum = ko.computed(function() {
            var total = 0,
                number = formatFloat(self.amount(),4),
                price = formatFloat(self.price(),4);
            total = formatFloat(number * price,4);
            return formatFloat(total,4);
        });
        self.product = ko.observable();
        self.serialid = ko.observable();
        self.order_time = ko.observable();
        self.company = ko.observable();
        self.model = ko.observable();
        self.standard = ko.observable();
        self.color = ko.observable();
        self.unit = ko.observable();
        self.worker.subscribe(function(newValue) {
            if (newValue) {
                self.department_id(newValue.department_id);
            };
        });
        self.process.subscribe(function(newValue) {
            if (newValue) {
                self.price(newValue.price);
            };
        });
        self.load = function(data) {
            self.product(data.product);
            self.serialid(data.serialid);
            self.order_time(data.order_time);
            self.company(data.company);
            self.model(data.model);
            self.standard(data.standard);
            self.color(data.color);
            self.unit(data.unit);
        }
        self.loadWorker = function(data) {
            self.worker(data);
        }
        self.loadProcess = function(data) {
            self.process(data);
        }
        self.selectProduct = function(data, event) {
            ShowModal(createUrl('Order/Window?index=' + self.uid),{width:650,height:650});
        }
        self.selectWorker = function() {
            var url = createUrl('Department/Window?action=worker');
            url += "&index=" + self.uid;
            ShowModal(url, {
                width: 600,
                height: 650
            });
        }
        self.selectPrice = function() {
            var url = createUrl('Department/Window?action=price');
            url += "&index=" + self.uid;
            if (self.worker() && self.worker().department_id) {
                url += "&id=" + self.worker().department_id;
            };
            ShowModal(url, {
                width: 600,
                height: 650
            });
        }
        self.getName = function(name) {
            return "M[" + self.uid + "][" + name + "]";
        }
    }
    var List = function() {
        var self = this,
            count = 0;
        self.lines = ko.observableArray();
        self.add = function() {
            self.lines.push(new EditViewModel());
        }
        self.remove = function(item) {
            self.lines.remove(item);
        }
        self.getLine = function(uid) {
            var result = null;
            jQuery.each(self.lines(), function() {
                if (this.uid == uid) {
                    result = this;
                    return false;
                };
            });
            return result;
        }
        self.totals = ko.computed(function() {
            var total = 0;
            jQuery.each(self.lines(), function() {
                total += this.sum();
            });
            return formatFloat(total,4);
        });
        self.init = function(element, index, data) {
            $('#' + data.uid + ' .type-date').each(function(i, elem) {
                if ($(elem).val() == 0) {
                    $(elem).val('');
                };
                $(elem).on('focus', function() {
                    WdatePicker({
                        maxDate: '%y-%M-{%d+0}'
                    });
                });
            })
        }
    }
    var list = new List();
    ko.applyBindings(list, document.getElementById('wages'));
    list.add();
    var wageForm = $('#wage-form');
    wageForm.on('submit', function(event) {
        var error = false;
        wageForm.find('input[required][readonly]').removeClass('error').each(function(i, elem) {
            var el = $(elem);
            if (el.val() == '') {
                log(el);
                el.addClass('error');
                error = true;
            }
        });
        if (list.lines().length == 0) {
            alert('没有需要保存在工人薪资!\n请选择工人并录入工序信息');
            list.add();
            error = true;
        };
        if (error) {
            event.preventDefault();
        };
    });
});