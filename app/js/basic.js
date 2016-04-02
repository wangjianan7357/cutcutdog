/**
 * 基本配置脚本
 **/

var domain = "http://dog.ideal-max.com/";

if(window.plus){
    plusReady();
} else {
    document.addEventListener("plusready", plusReady, false);
}

// H5 plus事件处理
function plusReady(){
    // 创建并显示新窗口
    $("[data-action='webview']").click(function(){
        var w = plus.webview.open($(this).attr("href"));
        return false;
    });
    
    // 创建并滑动出新窗口
    $("[data-action='openwindow']").click(function(){
        var link = $(this).attr("href");

        mui.openWindow({
            url: link,
            id: link.replace(/\.[\w]{2,6}$/, ""),
            show: {
                aniShow: 'pop-in'
            },
            styles: {
                popGesture: 'hide'
            },
            waiting: {
                autoShow: false
            }
        });

        return false;
    });
}

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
        return decodeURI(r[2]);
    } else {
        return null; //返回参数值
    }
}

function getCookie(name){
    if (document.cookie.length > 0){
        var start = document.cookie.indexOf(name + "=");
        if (start != -1) { 
            start += name.length + 1;
            var end = document.cookie.indexOf(";", start);
            
            if (end == -1) {
                end = document.cookie.length;
            }
            return unescape(document.cookie.substring(start, end));
        }
    }
    return "";
}

function setCookie(name, value, expire){
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expire);
    document.cookie = name + "=" + escape(value) + ";path=/" + ((expire == null) ? "" : ";expires=" + exdate.toGMTString());
}