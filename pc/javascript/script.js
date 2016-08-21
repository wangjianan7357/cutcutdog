function $A(iterable) {
	var results = [];
	for (var i = 0; i < iterable.length; i++) results.push(iterable[i]);
	return results;
}

Function.prototype.bind = function() {
	var __method = this, args = $A(arguments), object = args.shift();
	return function() {
		return __method.apply(object, args.concat($A(arguments)));
	};
};

function setTab(name, cur){
	for(i = 1; i <= 100; i++){
		if(!document.getElementById(name + i)) break;

		var menu = document.getElementById(name + i);
		var tab = document.getElementById(name + "_" + i);
		menu.className = (i == cur ? "hov" : "lnk");
		tab.style.display = (i == cur ? "block" : "none");
	}
}


function addToCart(id, url) {
    var num = 1;

    $.ajax({
        type: "post",
        url: "/ajax.php?action=add_cart&num=" + num + "&id=" + id,
        data: {
            wanturl: window.location.href
        },
        success: function (data) {
        	if (url) {
        		document.location.href = url;
        	} else {
        		alert("成功加入！");
        	}
            //refreshCart();
        }
    });
}

function submitFormAddAttr(id, attr){
    var attrs = eval(attr);
    var getform = document.getElementById(id);

    for (k in attrs) {
        var hidden = document.createElement("input");

        hidden.setAttribute("type", "hidden");
        hidden.setAttribute("name", k);
        hidden.setAttribute("value", attrs[k]);
        getform.appendChild(hidden);
    }

    getform.submit();
}