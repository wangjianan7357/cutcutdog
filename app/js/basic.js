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

function uploadFileData(filepath, data, url, success) {
    //var datas = options.datas || [];

    var task = plus.uploader.createUpload(domain + url, {  
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
                //alert(JSON.stringify(res))
            }  
        }  
    );

    if(filepath){  
        task.addFile(filepath, {key: 'src'}); 
    } else {
        task.addData("src", ""); 
    }
        
    var member = myStorage.getItem("member");
	task.addData("id", member.id); 
    task.addData("name", member.name); 
    
    for (d in data) {
    	task.addData(d, data[d]);  
    }
   
    task.start();  
}

function initComment(comment, likes, total) {
    document.getElementById(comment).addEventListener("tap", function(e) {
        //修复iOS 8.x平台存在的bug，使用plus.nativeUI.prompt会造成输入法闪一下又没了
        e.detail.gesture.preventDefault();

        mui.prompt("請輸入您的評語：", "", "", ["取消", "提交"], function(e) {
            if (e.index == 1) {
                mui.ajax({
                    type: "post",
                    url: domain + "saas/message.php",
                    dataType: "json",
                    data: {
                        action: "send",
                        id: member.id,
                        name: member.name,
                        content: e.value,
                        atype: cata_type,
                        aid: jQuery("#aid").val()
                    },
                    async: true,
                    success: function(json) {
                        //alert(JSON.stringify(json))
                    },
                    error: function(xhr, type, errorThrown) {
                        //alert(JSON.stringify(xhr))
                    }
                });
                
                jQuery("#comment-list").prepend("<li><p><b>" + member.name + "：</b>" + e.value + "</p></li>");
            }
        })
    });
    
    var setLikes = function(action) {
        mui.ajax({
            type: "post",
            url: domain + "saas/likes.php",
            dataType: "json",
            data: {
                action: action,
                id: member.id,
                name: member.name,
                atype: cata_type,
                aid: jQuery("#aid").val()
            },
            async: true,
            success: function(json) {
                //alert(JSON.stringify(json))
            },
            error: function(xhr, type, errorThrown) {
                //alert(JSON.stringify(xhr))
            }
        });
    };

    $("body").on("click", "#" + likes, function() {
        var count = parseInt($("#" + total).html());
        
        var rel = $(this).attr("rel");
       
        if(rel == 'like') {      
            setLikes("insert");
            $("#" + total).html(count + 1);
            $(this).attr("rel", "unlike");
        } else {
            setLikes("delete");
            $("#" + total).html(count - 1);
            $(this).attr("rel", "like");
        }
    });
}

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

    myStorage.getItem = function(key, useplus) {
    	//mui.plusReady(function(){
	        first = new Date().getTime();
	        return getItem(key) || getItemPlus(key);
	    //});
    };

    myStorage.setItem = function(key, value, useplus) {
        first = new Date().getTime();
        value = JSON.stringify({
            data: value
        });
        key = key.toString();
        
        //mui.plusReady(function(){
	        try {
	             window.localStorage.setItem(key, value);
	        } catch (e) {
	            console.log(e);
	            //TODO 超出localstorage容量限制则存到plus.storage中,且删除localStorage重复的数据
	            removeItem(key);
	            plus.storage.setItem(key, value);
	        }
	        //console.log(new Date().getTime() - first);
	    //});
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


function ajaxCache(params, refresh) {
	var mark = {};
    mark.url = params.url;
    mark.data = params.data;
				
    var identify = MD5(JSON.stringify(mark));
	    
    try {
	    var cache = myStorage.getItem(identify, true);
	
	    if (!cache || refresh) {
	        if (params.success) {
	            var fun = params.success;
	            params.success = function(json) {
	                if(!json.error) {
	                    myStorage.setItem(identify, json, true);
	                }
	
	                fun(json);
	            };
	
	        } else {
	            params.success = function(json) {
	                if(!json.error) {
	                    myStorage.setItem(identify, json, true);
	                }
	            };
	        }
	
	        mui.ajax(params);
	
	    } else {
	        if (params.success) {
	            params.success(cache);
	        }
	    }
	} catch(e){}
}

function initCart() {
    var cart = myStorage.getItem("cart");
    if (cart) {
        jQuery("[data-cart='number']").html(cart.length);
    }

    return cart;
}

function addCart(id, number) {
    var cart = myStorage.getItem("cart");
    if (cart) {
        var exist = false;

        for (var i = 0; i < cart.length; i ++) {
            if (cart[i]["id"] == id) {
                exist = true;
                cart[i]["number"] = isNaN(number) ? 1 : number;
            }
        }

        if (!exist) {
            cart.push({id: id, number: isNaN(number) ? 1 : number});
        }

    } else {
        cart = [{id: id, number: isNaN(number) ? 1 : number}];
    }

    myStorage.setItem("cart", cart);
    plus.nativeUI.toast("添加完成");
}

function delCart(id) {
    var cart = myStorage.getItem("cart");
    if (cart) {
        var tmp = [];

        for (var i = 0; i < cart.length; i ++) {
            if (cart[i]["id"] != id) {
                tmp.push(cart[i]);
            }
        }
    }

    myStorage.setItem("cart", tmp);
    plus.nativeUI.toast("刪除完成");

    setTimeout(function(){
        document.location.href = document.location.href;
    }, 50);
}

function MD5(str){
    var hex_chr = "0123456789abcdef";
    this.rhex = function(num){
        str = "";
        for(j = 0; j <= 3; j++)
        str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
        hex_chr.charAt((num >> (j * 8)) & 0x0F);
        return str;
    };
    this.str2blks_MD5 = function(str){
        nblk = ((str.length + 8) >> 6) + 1;
        blks = new Array(nblk * 16);
        for(i = 0; i < nblk * 16; i++) blks[i] = 0;
        for(i = 0; i < str.length; i++)
        blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
        blks[i >> 2] |= 0x80 << ((i % 4) * 8);
        blks[nblk * 16 - 2] = str.length * 8;
        return blks;
    };
    this.add = function(x, y){
        var lsw = (x & 0xFFFF) + (y & 0xFFFF);
        var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    };
    this.rol = function(num, cnt){
        return (num << cnt) | (num >>> (32 - cnt));
    };
    this.cmn = function(q, a, b, x, s, t){
        return this.add(this.rol(this.add(this.add(a, q), this.add(x, t)), s), b);
    };
    this.ff = function(a, b, c, d, x, s, t){
        return this.cmn((b & c) | ((~b) & d), a, b, x, s, t);
    };
    this.gg = function(a, b, c, d, x, s, t){
        return this.cmn((b & d) | (c & (~d)), a, b, x, s, t);
    };
    this.hh = function(a, b, c, d, x, s, t){
        return this.cmn(b ^ c ^ d, a, b, x, s, t);
    };
    this.ii = function(a, b, c, d, x, s, t){
        return this.cmn(c ^ (b | (~d)), a, b, x, s, t);
    };

    x = this.str2blks_MD5(str);
    var a = 1732584193;
    var b = -271733879;
    var c = -1732584194;
    var d = 271733878;
    for(i = 0; i < x.length; i += 16){
        var olda = a;
        var oldb = b;
        var oldc = c;
        var oldd = d;
        a = this.ff(a, b, c, d, x[i + 0], 7 , -680876936);
        d = this.ff(d, a, b, c, x[i + 1], 12, -389564586);
        c = this.ff(c, d, a, b, x[i + 2], 17, 606105819);
        b = this.ff(b, c, d, a, x[i + 3], 22, -1044525330);
        a = this.ff(a, b, c, d, x[i + 4], 7 , -176418897);
        d = this.ff(d, a, b, c, x[i + 5], 12, 1200080426);
        c = this.ff(c, d, a, b, x[i + 6], 17, -1473231341);
        b = this.ff(b, c, d, a, x[i + 7], 22, -45705983);
        a = this.ff(a, b, c, d, x[i + 8], 7 , 1770035416);
        d = this.ff(d, a, b, c, x[i + 9], 12, -1958414417);
        c = this.ff(c, d, a, b, x[i +10], 17, -42063);
        b = this.ff(b, c, d, a, x[i +11], 22, -1990404162);
        a = this.ff(a, b, c, d, x[i +12], 7 , 1804603682);
        d = this.ff(d, a, b, c, x[i +13], 12, -40341101);
        c = this.ff(c, d, a, b, x[i +14], 17, -1502002290);
        b = this.ff(b, c, d, a, x[i +15], 22, 1236535329);
        a = this.gg(a, b, c, d, x[i + 1], 5 , -165796510);
        d = this.gg(d, a, b, c, x[i + 6], 9 , -1069501632);
        c = this.gg(c, d, a, b, x[i +11], 14, 643717713);
        b = this.gg(b, c, d, a, x[i + 0], 20, -373897302);
        a = this.gg(a, b, c, d, x[i + 5], 5 , -701558691);
        d = this.gg(d, a, b, c, x[i +10], 9 , 38016083);
        c = this.gg(c, d, a, b, x[i +15], 14, -660478335);
        b = this.gg(b, c, d, a, x[i + 4], 20, -405537848);
        a = this.gg(a, b, c, d, x[i + 9], 5 , 568446438);
        d = this.gg(d, a, b, c, x[i +14], 9 , -1019803690);
        c = this.gg(c, d, a, b, x[i + 3], 14, -187363961);
        b = this.gg(b, c, d, a, x[i + 8], 20, 1163531501);
        a = this.gg(a, b, c, d, x[i +13], 5 , -1444681467);
        d = this.gg(d, a, b, c, x[i + 2], 9 , -51403784);
        c = this.gg(c, d, a, b, x[i + 7], 14, 1735328473);
        b = this.gg(b, c, d, a, x[i +12], 20, -1926607734);
        a = this.hh(a, b, c, d, x[i + 5], 4 , -378558);
        d = this.hh(d, a, b, c, x[i + 8], 11, -2022574463);
        c = this.hh(c, d, a, b, x[i +11], 16, 1839030562);
        b = this.hh(b, c, d, a, x[i +14], 23, -35309556);
        a = this.hh(a, b, c, d, x[i + 1], 4 , -1530992060);
        d = this.hh(d, a, b, c, x[i + 4], 11, 1272893353);
        c = this.hh(c, d, a, b, x[i + 7], 16, -155497632);
        b = this.hh(b, c, d, a, x[i +10], 23, -1094730640);
        a = this.hh(a, b, c, d, x[i +13], 4 , 681279174);
        d = this.hh(d, a, b, c, x[i + 0], 11, -358537222);
        c = this.hh(c, d, a, b, x[i + 3], 16, -722521979);
        b = this.hh(b, c, d, a, x[i + 6], 23, 76029189);
        a = this.hh(a, b, c, d, x[i + 9], 4 , -640364487);
        d = this.hh(d, a, b, c, x[i +12], 11, -421815835);
        c = this.hh(c, d, a, b, x[i +15], 16, 530742520);
        b = this.hh(b, c, d, a, x[i + 2], 23, -995338651);
        a = this.ii(a, b, c, d, x[i + 0], 6 , -198630844);
        d = this.ii(d, a, b, c, x[i + 7], 10, 1126891415);
        c = this.ii(c, d, a, b, x[i +14], 15, -1416354905);
        b = this.ii(b, c, d, a, x[i + 5], 21, -57434055);
        a = this.ii(a, b, c, d, x[i +12], 6 , 1700485571);
        d = this.ii(d, a, b, c, x[i + 3], 10, -1894986606);
        c = this.ii(c, d, a, b, x[i +10], 15, -1051523);
        b = this.ii(b, c, d, a, x[i + 1], 21, -2054922799);
        a = this.ii(a, b, c, d, x[i + 8], 6 , 1873313359);
        d = this.ii(d, a, b, c, x[i +15], 10, -30611744);
        c = this.ii(c, d, a, b, x[i + 6], 15, -1560198380);
        b = this.ii(b, c, d, a, x[i +13], 21, 1309151649);
        a = this.ii(a, b, c, d, x[i + 4], 6 , -145523070);
        d = this.ii(d, a, b, c, x[i +11], 10, -1120210379);
        c = this.ii(c, d, a, b, x[i + 2], 15, 718787259);
        b = this.ii(b, c, d, a, x[i + 9], 21, -343485551);
        a = this.add(a, olda);
        b = this.add(b, oldb);
        c = this.add(c, oldc);
        d = this.add(d, oldd);
    }
    return this.rhex(a) + this.rhex(b) + this.rhex(c) + this.rhex(d);
}