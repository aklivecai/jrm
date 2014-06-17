jQuery(function($) {
    var initUplaod = function(pickfiles, strcontainer, success) {
        var container = $('#' + strcontainer),
            filelist = container.find('.filelist'),
            uploader = new plupload.Uploader({
                runtimes: 'html5,flash,html4',
                browse_button: pickfiles, // you can pass in id...
                container: document.getElementById(strcontainer), // ... or DOM Element itself
                url: uploadUrl,
                flash_swf_url: CrmPath + '/js/Moxie.swf',
                multi_selection: false,
                resize: {
                    width: 1440,
                    height: 1440,
                    quality: 50
                },
                filters: {
                    max_file_size: '10mb',
                    chunk_size: '10mb',
                    mime_types: [{
                        title: "图片文件",
                        extensions: "jpg,gif,png"
                    }]
                },
                init: {
                    PostInit: function() {},
                    FilesAdded: function(up, files) {
                        plupload.each(files, function(file) {
                            filelist.html('<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>');
                        });
                        up.refresh();
                        uploader.start();
                    },
                    UploadProgress: function(up, file) {
                        $('#' + file.id).find('b').html('<span>' + file.percent + "%</span>");
                    },
                    Error: function(up, err) {
                        var str = "\nError #" + err.code + ": " + err.message;
                        alert(str);
                        up.refresh();
                    },
                    FileUploaded: function(up, file, msg) {
                        var elem = $('#' + file.id);
                        elem.find('b').html('100%');
                        if (msg && typeof msg['response'] != 'undefined') {
                            var obj = $.parseJSON(msg['response']);
                            if (obj && typeof obj['result'] != 'undefined') {
                                success.call(this, obj.result);
                            }
                        }
                    }
                }
            });
        uploader.init();
    }
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
    ko.extenders.requiredx = function(target, overrideMessage) {
        target.hasError = ko.observable();
        target.validationMessage = ko.observable();

        function validate(newValue) {
            target.hasError(newValue ? false : true);
            target.validationMessage(newValue ? "" : overrideMessage || "This field is required");
        }
        validate(target());
        target.subscribe(validate);
        return target;
    };
    ko.extenders.logChange = function(target, option) {
        target.subscribe(function(newValue) {
            console.log(option + ": " + newValue);
        });
        return target;
    };
    /*工序*/
    var ProcessLine = function(process_id) {
        var self = this;
        var id = null;
        if (typeof arguments[1] == 'undefined') {
            id = uuid();
        } else {
            id = arguments[1];
        }
        self.id = sprintf("%s[%s]", process_id, id);
        self.name = ko.observable('');
        self.note = ko.observable('');
        self.price = ko.observable(0).extend({
            numeric: 2,
            rateLimit: 500
        });
        self.getName = function(name) {
            return sprintf("%s[%s]", self.id, name);
        }
    }
    var Process = function(product_id) {
        var self = this;
        self.id = sprintf("%s[process]", product_id);
        self.lines = ko.observableArray();
        self.totals = ko.computed(function() {
            var total = 0;
            jQuery.each(self.lines(), function() {
                total += this.price();
            });
            return formatCurrency(total);
        });
        self.add = function() {
            var tempObj = new ProcessLine(self.id);
            self.lines.push(tempObj);
        }
        self.remove = function() {
            self.lines.remove(this);
        }
    }
    /*材料*/
    var MaterialLine = function(mid) {
        var self = this;
        var id = null;
        if (typeof arguments[1] == 'undefined') {
            id = uuid();
        } else {
            id = arguments[1];
        }
        self.id = sprintf("%s[%s]", mid, id);
        self.product_id = ko.observable('');
        self.name = ko.observable('');
        self.price = ko.observable(0).extend({
            numeric: 2,
            rateLimit: 500
        });
        self.number = ko.observable(0).extend({
            numeric: 4,
            rateLimit: 500
        });
        self.unit = ko.observable('');
        self.color = ko.observable('');
        self.note = ko.observable('');
        self.spec = ko.observable('');
        self.expenses = ko.observable(0).extend({
            numeric: 2,
            rateLimit: 500
        });
        /*总价*/
        self.total = ko.computed(function() {
            var total = self.expenses(),
                number = formatCurrency(self.number()),
                price = formatCurrency(self.price());
            total += formatCurrency(number * price);
            return formatCurrency(total);
        });
        self.getName = function(name) {
            return sprintf("%s[%s]", self.id, name);
        }
    };
    var Materia = function(type, product_id) {
        var self = this;
        self.lines = ko.observableArray();
        self.itype = type;
        this.typeName = self.itype == 2 ? '辅料' : '主料';
        self.id = sprintf("%s[materia][%s]", product_id, self.itype);
        self.totals = ko.computed(function() {
            var total = 0;
            jQuery.each(self.lines(), function() {
                total += this.total();
            });
            return formatCurrency(total);
        });
        self.add = function() {
            var tempObj = new MaterialLine(self.id);
            self.lines.push(tempObj);
        }
        self.remove = function() {
            self.lines.remove(this);
        }
    };
    var ProductLine = function() {
        var self = this;
        var id = null;
        if (typeof arguments[0] == 'undefined') {
            id = uuid();
        } else {
            id = arguments[0];
        }
        self.id = sprintf("M[product][%s]", id);
        self.itemid = id;
        self.type = ko.observable('');
        self.name = ko.observable('');
        self.color = ko.observable('');
        self.spec = ko.observable('');
        self.file_path = ko.observable('');
        //'http://i.9juren.com/file/upload/201406/06/09-26-29-85-4843.png'
        self.isfile = ko.computed(function() {
            return self.file_path() == '';
        });
        self.mainMaterias = new Materia(1, self.id);
        self.subMaterias = new Materia(2, self.id);
        self.process = new Process(self.id);
        self.expenses = ko.observable(0).extend({
            numeric: 2
        });
        self.number = ko.observable(1).extend({
            numeric: 0
        });
        var list = ['mainMaterias', 'subMaterias', 'process'];
        self.init = function() {
            for (var i in list) {
                self[list[i]].add();
            }
        }
        /*单价*/
        self.price = ko.computed(function() {
            var total = 0;
            for (var i in list) {
                total += self[list[i]].totals();
            };
            if (total != 0) {
                total += self.expenses();
            };
            return formatCurrency(total);
        });
        /*总价*/
        self.totals = ko.computed(function() {
            var total = 0;
            number = formatCurrency(self.number()),
            price = formatCurrency(self.price());
            total += formatCurrency(number * price);
            return formatCurrency(total);
        });
        self.removePic = function(item) {
            self.setPic('');
        }
        self.setPic = function(url) {
            self.file_path(url);
        }
        self.getName = function(name) {
            return sprintf("%s[%s]", self.id, name);
        }
        self.getId = function(id) {
            var id = self.getName(id);
            return id.replace(/\[/ig, '-').replace(/\]/ig, '');
        }
        self.initUpload = function() {
            var upload = initUplaod(self.getId('pickfiles'), self.getId('container'), function(src) {
                self.setPic(src);
            });
        }
    }
    var Product = function() {
        var self = this;
        self.lines = ko.observableArray();
        self.add = function() {
            var tempObj = arguments.length == 1 ? arguments[0] : new ProductLine();
            if (tempObj.totals() == 0) {
                tempObj.init();
            };
            self.lines.push(tempObj);
        }
        self.remove = function() {
            self.lines.remove(this);
        }
        self.init = function() {
            self.add();
        }
        /*总价*/
        self.totals = ko.computed(function() {
            var total = 0;
            jQuery.each(self.lines(), function() {
                total += this.totals();
            });
            return formatCurrency(total);
        });
        self.numbers = ko.computed(function() {
            var total = 0;
            jQuery.each(self.lines(), function() {
                total += this.number();
            });
            return formatCurrency(total);
        });
        self.initElement = function(element, index, data) {}
        self._lines = ko.observableArray();
        self.msg = ko.observable(false);
        self.doSomething = function(formElement) {
            if ($('.error').length == 0) {
                $('#constac1>.modc>div>.itable').addClass('action-active');
                var str = sprintf("  总成本:%s , 产品数量:%s件", self.totals(), self.numbers());
                self.msg(str);
                self._lines.push('');
                return true;
                lines = [];
                jQuery.each(self.lines(), function() {
                    // lines.push(sprintf(" %s"));                
                });
                /*self._lines(lines);*/
            }
        }
    }
    var productView = new Product();
    if (products.length == 0) {
        productView.init();
    } else {
        for (var i in products) {
            var obj = products[i],
                pro = new ProductLine(obj.itemid);
            pro.type(obj.type);
            pro.name(obj.name);
            pro.spec(obj.spec);
            pro.color(obj.color);
            pro.number(obj.amount);
            productView.add(pro);
        };
        // $('.action-fold').trigger('click').eq(0).trigger('click');
    }
    ko.applyBindings(productView, document.getElementById('wrapper'));
    $(document).on("click", '.action-deleted', function() {
        if (confirm('是否确认删除!')) {
            productView.lines.remove(ko.dataFor(this));
        };
    });
});