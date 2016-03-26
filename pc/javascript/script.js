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

function setTextFont(size, id){
	if(typeof(size) == "string"){
		switch(size){
			case "big": size = 24; break;
			case "mid": size = 18; break;
			case "sml": size = 14; break;
		}
	}

	document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace(/<([a-z\d\:]+) [^>]+>/gi, "<$1>");
	document.getElementById(id).style.fontSize = size + "px";
	document.getElementById(id).style.lineHeight = size + 18 + "px";
}


function setFavor(){ 
	var pageName = window.location.href; 
	var nameArr = pageName.split("?"); 
	pageName = nameArr[0] + "?" + nameArr[1]; 
	if(window.sidebar){ 
		window.sidebar.addPanel(document.title,pageName,""); 
	} 
	else if(document.all){  
		window.external.AddFavorite(pageName,document.title); 
	} 
	else return true; 
}


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



/* 弹出菜单 */
(function(){
	function flipMenu(){};

	flipMenu.prototype = {
		initialize: function(ele, igleft){
			if(!ele) return;

			this.delay = "";
			this.percent = 0;
			this.freq = 30;
			this.igleft = igleft;
			var obj = this;
			var aimleft = -1;
			
			if(!igleft){
				if(this.checkNav("MSIE 6") || this.checkNav("MSIE 7")){
					aimleft = obj.nodeRecursion(ele, "parentNode", "offsetLeft") - 143;
				}
				else aimleft = ele.offsetLeft;
			}

			aimleft -= 30;

			ele.onmouseover = function(){
				clearTimeout(obj.delay);

				var getdiv = document.getElementById(this.id.replace(/^m/gi, "s"));
				if(!getdiv) return;

				getdiv.style.display = "block";
				
				//if(aimleft + getdiv.offsetWidth > 1000) aimleft = 1080 - getdiv.offsetWidth;

				if(!obj.igleft) getdiv.style.left = aimleft + "px";
				obj.delay = setTimeout(obj.changeOpacity.bind(obj, getdiv, 1), obj.freq);

				getdiv.onmouseover = function(){
					clearTimeout(obj.delay);
					this.style.display = "block";
					if(!obj.igleft && aimleft != -1) this.style.left = aimleft;
					obj.delay = setTimeout(obj.changeOpacity.bind(obj, this, 1), obj.freq);
				};

				getdiv.onmouseout = function(){
					clearTimeout(obj.delay);
					obj.delay = setTimeout(obj.changeOpacity.bind(obj, this, -1), obj.freq);
				};
			};

			ele.onmouseout = function(){
				clearTimeout(obj.delay);

				var getdiv = document.getElementById(this.id.replace(/^m/gi, "s"));
				if(!getdiv) return;

				getdiv.style.display = "block";
				obj.delay = setTimeout(obj.changeOpacity.bind(obj, getdiv, -1), obj.freq);
			};
		},
		
		getProperty: function(flipObj, property){
			this.opacity(flipObj, 0);
			flipObj.style.display = "block";
			return eval("flipObj." + property);
		},

		opacity: function(flipObj){
			if(document.all) flipObj.style.filter = "Alpha(opacity=" + this.percent * 10 + ")";
			else flipObj.style.opacity = this.percent / 10;
		},

		changeOpacity: function(flipObj, flag){
			if(flag > 0){
				if(this.percent < 9){
					this.percent += 2;
					this.opacity(flipObj);
				}
				else {
					clearTimeout(this.delay);
					return;
				}
			}
			else {
				if(this.percent > 0){
					this.percent -= 2;
					this.opacity(flipObj);
				}
				else {
					clearTimeout(this.delay);
					flipObj.style.display = "none";
					return;
				}
			}

			this.delay = setTimeout(this.changeOpacity.bind(this, flipObj, flag), this.freq);
		},

		checkNav: function(ver){
			var nav = navigator.userAgent.indexOf(ver);
			if(nav == -1) return false;
			else return true;
		},

		nodeRecursion: function(tobj, tarnodes, nodepro){
			var provalue;
			if(!provalue) provalue = 0;

			try {
				tobj = eval("tobj." + tarnodes);
				provalue = this.nodeRecursion(tobj, tarnodes, nodepro);
			}
			catch (e){}
			var temp = eval("tobj." + nodepro);
			temp = parseInt(temp);

			if(tobj.tagName.toLowerCase() == "div") temp = 0;
			if(!isNaN(temp)) provalue += temp;

			return provalue;
		},
		
		getNodeById: function(idname){
			var tags;
			var bodyhtml = document.body.innerHTML;
			var reg = new RegExp("<(\\w*)([^>]*) id=([\'\"]?)" + idname + "\\3(>| [^>]+>)", "i");
			bodyhtml.replace(reg, function(){ tags = arguments[1]; });
			var getags = document.getElementsByTagName(tags);
			for(i = 0; i < getags.length; i++){
				if(getags[i].id == idname) return getags[i];
			}
		}
	}

	if(document.getElementById("menu")){
		var getp = document.getElementById("menu").getElementsByTagName("a")
		for(i = 0; i < getp.length; i++){
			if(getp[i].id == "m_sort1"){
				var newflip = new flipMenu();
				newflip.initialize(getp[i]);
			}
		}
	}

	var newflip = new flipMenu();
	newflip.initialize(document.getElementById("m_sort1"));

	var newflip = new flipMenu();
	newflip.initialize(document.getElementById("m_sort2"));

})();