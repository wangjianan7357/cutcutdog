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


function verifyImg(path){
	var d = new Date();
	document.getElementById('verify').src = path + "verifyimg.php?t=" + d.toTimeString();
}

function setTab(name, cur){
	for(i = 1; i <= 100; i++){
		if(!document.getElementById(name + i)) break;

		var menu = document.getElementById(name + i);
		var tab = document.getElementById(name + "_" + i);
		menu.className = (i == cur ? "hov" : "lnk");
		tab.style.display = (i == cur ? "block" : "none");
	}
}

