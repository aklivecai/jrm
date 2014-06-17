/**
 *
 * @authors aklivecai (aklivecai@gmail.com)
 * @date    2014-05-26 11:05:24
 * @version $Id$
 */
if (typeof window['log'] == 'undefined') {
    var log = function(msg) {
        if (typeof window['console'] == 'undefined') return false;
        var len = arguments.length;
        if (len > 1) {
            for (var i = 0; i < len; i++) {
                log(arguments[i] + '\n');
            }
        } else {
            console.log(msg);
        }
    }
}
var dlog = function(obj) {
    var str = '';
    for (var el in obj) {
        str += obj[el];
    };
    alert(str);
};
//用于动态生成网址
//$route,$params=array(),$ampersand='&'
var createUrl = function(route) {
    if (!CrmPath) {
        return false;
    }
    var ampersand = typeof arguments[2] != 'undefined' ? arguments[2] : '&',
        params = typeof arguments[1] != 'undefined' ? arguments[1] : [];
    if (!route || route == "undefined") {
        return CrmPath;
    }
    // var url = CrmPath + (CrmPath.indexOf('?')>0?'':'?');
    var url = CrmPath;
    url += route;
    url = url + (url.indexOf('?') > 0 ? '' : '?');
    if (params.length > 0) {
        url += ampersand + params.join(ampersand);
    };
    if (url.indexOf('?&') > 0) {
        url = url.replace('?&', '?');
    };
    return url;
}, dateFormat = function(date, format) {
        if (format === undefined) {
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
        format = format.replace(/([yMdhmsqS])+/g, function(all, t) {
            var v = map[t];
            if (v !== undefined) {
                if (all.length > 1) {
                    v = '0' + v;
                    v = v.substr(v.length - 2);
                }
                return v;
            } else if (t === 'y') {
                return (date.getFullYear() + '').substr(4 - all.length);
            }
            return all;
        });
        return format;
    }, sCF = function(msg) {
        return !confirm(msg);
    }, gotoElem = function(elem) {
        /*
        $("body,html").animate({
            scrollTop:$(elem).offset().top //让body的scrollTop等于pos的top，就实现了滚动
        },0);
    */
        $(window).scrollTop($(elem).offset().top);
    }, trim = function(str) {
        return $.trim(str);
    }, getTimes = function() {
        var time = new Date().getTime();
        time = parseInt(time / 1000);
        return time;
    }, checkDecimal = function(value) {
        var decimalReg = new RegExp("^\\d+(\\.\\d+)?$");
        return decimalReg.test(value);
    }, padLeft = function(str, lenght) { //位数不足补0，length是位数
        if (str.length >= lenght) return str;
        else return padLeft("0" + str, lenght);
    }
    //保留N位小数  
    , formatFloat = function(src, pos) {
        return Math.round(src * Math.pow(10, pos)) / Math.pow(10, pos);
    }, formatCurrency = function(value) {
        var digit = 100;
        if (typeof arguments[1] != 'undefined') {
            digit = arguments[1];
        }
        return parseFloat(Math.round(Number(value) * digit) / digit);
    }, uniencode = function(text) {
        return text.replace(/[\u4E00-\u9FA5]/ig, function(w) {
            return escape(w).toLowerCase().replace(/%/ig, '\\');
        });
    }, _uuid = function(len, radix) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
        var uuid = [],
            i;
        radix = radix || chars.length;
        if (len) {
            for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
        } else {
            var r;
            uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
            uuid[14] = '4';
            for (i = 0; i < 36; i++) {
                if (!uuid[i]) {
                    r = 0 | Math.random() * 16;
                    uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
                }
            }
        }
        return uuid.join('');
    }, uuid = function() {
        var len = typeof arguments[0] != 'undefined' ? arguments[0] : 17;
        return _uuid(len, len);
    }, ShowModal = function(url) {
        var options = arguments.length > 1 ? arguments[1] : {
            width: 500,
            height: 500
        };
        l = (screen.availWidth - 10 - options.width) / 2,
        t = (screen.availHeight - 30 - options.height) / 2,
        dataObj = arguments.length >= 3 ? arguments[2] : {},
        retValue = {},
        pars = [],
        winName = typeof options.name != 'undefined' ? options.name : '';
        options.height = screen.availHeight - screen.availHeight / 5;
        options.width = screen.availWidth < 1300 ? 1000 : screen.availWidth - screen.availWidth / 5;
        t = 25;
        if (window.showModalDialog) {
            pars.push("resizable:yes");
            pars.push("dialogWidth:" + options.width + 'px');
            pars.push("dialogHeight:" + options.height + 'px');
            if (!/chrome/.test(navigator.userAgent.toLowerCase())) {
                pars.push("dialogLeft:" + l + 'px');
            };
            pars.push("dialogTop:" + t + 'px');
            //传递window　为了窗口页面可以使用当前页面内容
            retValue = window.showModalDialog(url, window, pars.join(';'));
        } else {
            // for similar functionality in Opera, but it's not modal!
            pars.push("width=" + options.width);
            pars.push("height=" + options.height);
            pars.push("left=" + l);
            pars.push("top=" + t);
            var modal = window.open(url, winName, pars.join(','), null);
            modal.dialogArguments = dataObj;
            retValue = modal;
        }
        return retValue;
    };