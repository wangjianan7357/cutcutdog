/**
 * 基本配置脚本
 **/

var domain = "http://xd.com/";


/**
 * 公用方法
 **/

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

function assignTmpl(json) {
    var recurData = function (obj, data, len) {
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

        if (info[1]) {
            $(this).attr(info[0], recurData(info[1].split("."), json.data, 0));
        } else {
            $(this).html(recurData(info[0].split("."), json.data, 0));
        }        
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
