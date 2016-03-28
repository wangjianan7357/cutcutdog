/**
 * 基本配置脚本
 **/

var domain = "http://xd.com/";


/**
 * 公用方法
 **/

template.helper("seturlparams", function (key, value) {
    var location = window.location.href;
    var reg = new RegExp("(\\?|&)" + key + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    location = location.replace(reg, "") + "&" + key + "=" + value;

    return location;
});

function fetchTmpl(path, target) {
    $.ajax({ 
        url: path, 
        dataType: "text",
        async: false,
        success: function(code){
            target.html(code);
        }
    });
}

function assignTmpl(json, sign) {
    if (!sign) {
        sign = "#";
    }

    var recurData = function (obj, data, len) {
        if (!len) {
            len = 0;
        }

        var res = data[obj[len]];
        len ++;
        
        if (obj.length > len) {
            return recurData(obj, res, len)
        } else {
            return res;
        }
    }

    $("[data-info]").each(function(){
        var info = $(this).attr("data-info").split("=");

        try {
            if (info[1]) {
                var current = $(this).attr(info[0]);
                if (current.indexOf(sign) != -1) {
                    $(this).attr(info[0], current.replace(sign, recurData(info[1].split("."), json.data)));
                } else {
                    $(this).attr(info[0], recurData(info[1].split("."), json.data));
                }
                
            } else {
                var current = $(this).html();
                if (current.indexOf(sign) != -1) {
                    $(this).html(current.replace(sign, recurData(info[0].split("."), json.data)));
                } else {
                    $(this).html(recurData(info[0].split("."), json.data));
                }
            }
        } catch(e){}
    });
}

function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) {
        return unescape(r[2]); 
    } else {
        return null; //返回参数值
    }
}
