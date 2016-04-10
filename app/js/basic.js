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
if (typeof(template) != "undefined") {
    template.helper("seturlparams", function (key, value) {
        var location = window.location.href;
        var reg = new RegExp("(\\?|&)" + key + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        location = location.replace(reg, "") + "&" + key + "=" + value;
        return location;
    });

    template.helper("getvarious", function (name) {
        var various;
        eval("various = " + name);
        return various;
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
            return typeof(res) == "undefined" ? "" : res;
        }
    }

    $("[data-info]").each(function(){
    	var before = "";
        var info = $(this).attr("data-info").split("=");
		var various = $(this).attr("data-before-info");
		
		if (various) {
			eval("before = " + various);
		}
		
        try {
            if (info[1]) {
                var current = $(this).attr(info[0]);
                if (current.indexOf(sign) != -1) {
                    current = current.replace(sign, recurData(info[1].split("."), json));
                } else {
                    current = recurData(info[1].split("."), json);
                }
                
            } else {
                var current = $(this).html();
                if (current.indexOf(sign) != -1) {
                    current = current.replace(sign, recurData(info[0].split("."), json));
                } else {
                    current = recurData(info[0].split("."), json);
                }
            }
            
            if (info[1]) {
                if (current) {
                    $(this).attr(info[0], before + current);
                }
                
            } else {
                if (current) {
                    $(this).html(before + current);
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

function uploadFile(filepath, success) {
    //var datas = options.datas || [];

    if(filepath){  
        var task = plus.uploader.createUpload(domain + "saas/member.php", {  
                method: "POST",  
                blocksize: 204800,  
                priority: 100  
            },  
            function(res, status){  
                if (status == 200){
                	var json = "";
                	eval("json = (" + res.responseText + ")");
                	
                    if (!json.error) {
	                    if (success) {
	                    	success(json);
	                    }
	                }
                } else {  
                    
                }  
            }  
        );

        task.addFile(filepath, {key: 'src'}); 
        
        var member = myStorage.getItem("member");
		task.addData("id", member.id); 
        task.addData("name", member.name); 
        task.addData("action", "update");  

        task.start();  
    }  
};  

/* canvas
function appendFile(path) {
    var img = new Image();
    img.src = path;

    img.onload = function () {
        var that = this;
        //生成比例 
        var w = that.width,
        h = that.height,
        scale = w / h; 
        //480  你想压缩到多大，改这里
        w = 480 || w;
        h = w / scale;

        //生成canvas
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        $(canvas).attr({width : w, height : h});
        ctx.drawImage(that, 0, 0, w, h);

        var base64 = canvas.toDataURL('image/jpeg', 1 || 0.8);   //1最清晰，越低越模糊。有一点不清楚这里明明设置的是jpeg。弹出 base64 开头的一段 data：image/png;却是png。

        //f1 = base64;   // 把base64数据丢过去，上传要用。

        var pic = document.getElementById("x");    
        pic.src = base64; //这里丢到img 的 src 里面就能看到效果了
    };
}
*/

(function(mui, com) {
    var myStorage = {};
    var first = null;

    function getItem(key) {
        var jsonStr = window.localStorage.getItem(key.toString());
        return jsonStr ? JSON.parse(jsonStr).data : null;
    };

    function getItemPlus(key) {
        var jsonStr = plus.storage.getItem(key.toString());
        console.log(new Date().getTime() - first);
        return jsonStr ? JSON.parse(jsonStr).data : null;
    };

    myStorage.getItem = function(key) {
        first = new Date().getTime();
        return getItem(key) || getItemPlus(key);
    };

    myStorage.setItem = function(key, value) {
        first = new Date().getTime();
        value = JSON.stringify({
            data: value
        });
        key = key.toString();
        try {
             window.localStorage.setItem(key, value);
        } catch (e) {
            console.log(e);
            //TODO 超出localstorage容量限制则存到plus.storage中
            //且删除localStorage重复的数据www.bcty365.com

            removeItem(key);
            plus.storage.setItem(key, value);
        }
        console.log(new Date().getTime() - first);
    };

    function getLength() {
        return window.localStorage.length;
    };
    myStorage.getLength = getLength;

    function getLengthPlus() {
        return plus.storage.getLength();
    };
    myStorage.getLengthPlus = getLengthPlus;

    function removeItem(key) {
        return window.localStorage.removeItem(key);
    };

    function removeItemPlus(key) {
        return plus.storage.removeItem(key);
    };
    myStorage.removeItem = function(key) {
        window.localStorage.removeItem(key);
        return plus.storage.removeItem(key);
    }
    myStorage.clear = function() {
        window.localStorage.clear();
        return plus.storage.clear();
    };

    function key(index) {
        return window.localStorage.key(index);
    };
    myStorage.key = key;

    function keyPlus(index) {
        return plus.storage.key(index);
    };
    myStorage.keyPlus = keyPlus;

    function getItemByIndex(index) {
        var item = {
            keyname: '',
            keyvalue: ''
        };
        item.keyname = key(index);
        item.keyvalue = getItem(item.keyname);
        return item;
    };
    myStorage.getItemByIndex = getItemByIndex;

    function getItemByIndexPlus(index) {
        var item = {
            keyname: '',
            keyvalue: ''
        };
        item.keyname = keyPlus(index);
        item.keyvalue = getItemPlus(item.keyname);
        return item;
    };
    myStorage.getItemByIndexPlus = getItemByIndexPlus;
    /**
     * @author liuyf 2015-05-04
     * @description 获取所有存储对象
     * @param {Object} key 可选，不传参则返回所有对象，否则返回含有该key的对象
     */
    myStorage.getItems = function(key) {
        var items = [];
        var numKeys = getLength();
        var numKeysPlus = getLengthPlus();
        var i = 0;
        if (key) {
            for (; i < numKeys; i++) {
                if (key(i).toString().indexOf(key) != -1) {
                    items.push(getItemByIndex(i));
                }
            }
            for (i = 0; i < numKeysPlus; i++) {
                if (keyPlus(i).toString().indexOf(key) != -1) {
                    items.push(getItemByIndexPlus(i));
                }
            }
        } else {
            for (i = 0; i < numKeys; i++) {
                items.push(getItemByIndex(i));
            }
            for (i = 0; i < numKeysPlus; i++) {
                items.push(getItemByIndexPlus(i));
            }
        }
        return items;
    };
    /**
     * @description 清除指定前缀的存储对象
     * @param {Object} keys
     * @default ["filePathCache_","ajax_cache_"]
     * @author liuyf 2015-07-21
     */
    myStorage.removeItemByKeys = function(keys, cb) {
        if (typeof(keys) === "string") {
            keys = [keys];
        }
        keys = keys || ["filePathCache_", "ajax_cache_", "Wedding", "wedding"];
        var numKeys = getLength();
        var numKeysPlus = getLengthPlus();
        //TODO plus.storage是线性存储的，从后向前删除是可以的 
        //稳妥的方案是将查询到的items，存到临时数组中，再删除  
        var tmpks = [];
        var tk,
            i = numKeys - 1;
        for (; i >= 0; i--) {
            tk = key(i);
            Array.prototype.forEach.call(keys, function(k, index, arr) {
                if (tk.toString().indexOf(k) != -1) {
                    tmpks.push(tk);
                }
            });
        }
        tmpks.forEach(function(k) {
            removeItem(k);
        });
        for (i = numKeysPlus - 1; i >= 0; i--) {
            tk = keyPlus(i);
            Array.prototype.forEach.call(keys, function(k, index, arr) {
                if (tk.toString().indexOf(k) != -1) {
                    tmpks.push(tk);
                }
            });
        }
        tmpks.forEach(function(k) {
            removeItemPlus(k);
        })
        cb && cb();
    };
    com.myStorage = myStorage;
    window.myStorage = myStorage;
    
}(mui, common = {}));
