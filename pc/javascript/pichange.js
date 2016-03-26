////////////////////////////////////////////////////////////
// Script to change the picture (By wjn)
// Supported Browsers: IE, Mozilla, Safari
////////////////////////////////////////////////////////////

function pichange(){ 
	this.initialize.apply(this, arguments);
}

pichange.prototype = {
	initialize: function(picarr) {
		for(var i = 0; i < picarr.length; i++){
			switch(picarr[i][0]){
				case "id_obj": this.idpic = picarr[i][1]; break;
				case "id_objbg": this.idbg = picarr[i][1]; break;
				case "id_objinfo": this.idinfo = picarr[i][1]; break;
				case "id_click": this.idnum = i; break;
				case "tag_img": this.tagname = picarr[i][1]; break;
				case "time_delay": this.delay = parseInt(picarr[i][1]); break;
				case "z_index": this.minindex = parseInt(picarr[i][1]); break;
				case "previcon": this.previcon = picarr[i][1]; break;
				case "nexticon": this.nexticon = picarr[i][1]; break;
				case "playicon": this.playicon = picarr[i][1]; this.stopicon = picarr[i][2]; break;
				case "iconcss": this.iconcss = i; break;
				case "change_speed": this.rundelay = parseInt(picarr[i][1]); break;
				case "change_step": this.runstep = parseInt(picarr[i][1]); break;
				case "del_repimg": this.delrepimg = picarr[i][1]; break;
				case "playing": this.playing = picarr[i][1]; break;
				case "img_ele": this.imgele = i;
			}
		}
		
		// define the shade and the info object
		if(!this.idbg) this.idbg = this.idpic + "bg";
		if(!this.idinfo) this.idinfo = this.idpic + "info";
		this.idbg = this.getNodeById(this.idbg);
		this.idinf = this.getNodeById(this.idinfo);
		this.idpic = this.getNodeById(this.idpic);

		if(!this.idpic) return;

		// append image to the list
		if(this.imgele){
			for(var e = 1; e < picarr[this.imgele].length; e++){
				if(picarr[this.imgele][e][0].indexOf("<") != -1){
					var imgtag = this.idpic.getElementsByTagName(picarr[this.imgele][e][0].replace(/<|>/g, ""));
					this.cycImgChild(imgtag[0].parentNode, picarr[this.imgele][e]);
					//this.cycImgChild(this.idpic, picarr[this.imgele][e]);
				}
			}
		}

		if(!this.tagname){
			if(this.imgele){
				this.tagname = picarr[this.imgele][1][0].replace(/<|>/g, "");
				this.tagname = this.tagname.split(" ")[0];
			}
			else this.tagname = "img";
		}

		if(this.delrepimg != false){
			try {
				var alltags = this.idpic.getElementsByTagName(this.tagname);
				for(var t = 1; t < alltags.length; t++){
					var tsrc = alltags[t].getElementsByTagName("img")[0].src;
					if(!tsrc) tsrc = alltags[t].src;
					for(var u = 0; u < t; u++){
						var usrc = alltags[u].getElementsByTagName("img")[0].src;
						if(!usrc) usrc = alltags[u].src;
						if(tsrc == usrc){
							this.idpic.removeChild(alltags[t]);
							t--;
							break;
						}
					}
				}
			}
			catch (e){}
		}
		
		this.icontag = "span";
		if(isNaN(this.iconcss)){
			this.alink = this.tagname + "link";
			this.ahover = this.tagname + "hover";
		}
		else {
			this.alink = picarr[this.iconcss][1];
			this.ahover = picarr[this.iconcss][2];
		}

		this.tags = this.idpic.getElementsByTagName(this.tagname);

		if(this.tags.length < 0) return;
		if(isNaN(this.rundelay) || (this.rundelay <= 0)) this.rundelay = 50;
		if(isNaN(this.runstep) || (this.runstep <= 0)) this.runstep = 1;
		if(isNaN(this.delay) || (this.delay < this.minindex)) this.delay = 4000;
		if(isNaN(this.minindex) || (this.minindex < 0)) this.minindex = 2;

		if(this.playing != false) this.playing = true;

		var obj = this;
		this.lino = 0;
		this.picop = [];

		if(this.idnum){
			if(typeof(picarr[this.idnum][1]) == "string"){
				var dif = 1;
				this.idnum = document.getElementById(picarr[this.idnum][1]);
				this.idnum.innerHTML = "";
				this.idnum.style.zIndex = this.tags.length + 12;

				if(this.previcon) this.append_node(this.idnum, this.icontag, this.previcon, this.alink);
				for(var a = 1; a <= this.tags.length; a++){
					this.append_node(this.idnum, this.icontag, a, this.alink);
				}
				if(this.nexticon) this.append_node(this.idnum, this.icontag, this.nexticon, this.alink);
				if(this.playicon){
					dif++;
					this.append_node(this.idnum, this.icontag, this.stopicon, this.alink);
				}

				var getspan = this.idnum.getElementsByTagName(this.icontag);
				for(var s = 0; s < getspan.length; s++){
					getspan[s].onmouseover = function(){ this.className = obj.ahover; };
					getspan[s].onmouseout = function(){
						this.className = obj.alink;
						obj.current(obj.lino);
					};
					if((s == 0) && (this.previcon)) getspan[s].onclick = function(){ obj.prext(-1); };
					else if((s == getspan.length - dif) && (this.nexticon)) getspan[s].onclick = function(){ obj.prext(0); };
					else if((s == getspan.length - 1) && (this.playicon)) getspan[s].onclick = function(){
						if(this.innerHTML == obj.stopicon){
							this.innerHTML = obj.playicon;
							obj.runstop();
						}
						else {
							this.innerHTML = obj.stopicon;
							obj.runstart();
						}
					};
					else getspan[s].onclick = function(){	obj.gonum(this.innerHTML); };
				}
			}
			else if(typeof(picarr[this.idnum][1]) == "object"){
				this.clickimgid;
				this.clickimgtag;
				var curtaglen = this.tags.length;
				for(var i = 1; i < picarr[this.idnum].length; i++){
					if(picarr[this.idnum][i][0].indexOf("_") == 0){
						switch(picarr[this.idnum][i][0].replace("_", "")){
							case "id": this.clickimgid = picarr[this.idnum][i][1]; break;
							case "tag": this.clickimgtag = picarr[this.idnum][i][1]; break;
						}
					}
				}

				// add image click not the number icon
				this.clickimgid = this.getNodeById(this.clickimgid);
				if(this.clickimgid){
					var existwidth = existheight = false;
					this.clickimgtag = this.clickimgid.getElementsByTagName(this.clickimgtag);

					for(var s = 0; s < this.clickimgtag.length; s++){
						var newimg = new Image();
						for(var t = 1; t < picarr[this.idnum].length; t++){
							if(picarr[this.idnum][t][0].indexOf("_") != 0){
								if(picarr[this.idnum][t][1].indexOf("_") == 0){
									eval("newimg." + picarr[this.idnum][t][0] + "=this.clickimgtag[s]." + picarr[this.idnum][t][0].replace("_", ""));
								}
								else {
									eval("newimg." + picarr[this.idnum][t][0] + "=picarr[this.idnum][t][1]");
								}

								if(picarr[this.idnum][t][0] == "width") existwidth = true;
								else if(picarr[this.idnum][t][0] == "hieght") existheight = true;
							}
						}
						if(!existwidth) newimg.removeAttribute('width');
						if(!existheight) newimg.removeAttribute('height');

						var newtag = document.createElement(this.tagname);
						newtag.appendChild(newimg);
						this.idpic.appendChild(newtag);

						// add a new property(imgclicknum) to define the sequence of pictures
						this.clickimgtag[s].imgclicknum = curtaglen + s + 1;
						this.clickimgtag[s].onclick = function(){ obj.gonum(this.imgclicknum); };
					}
					this.clickimg = true;
				}
				this.idnum = null;
			}
		}

		for(var i = 0; i < this.tags.length; i++){
			this.picop[i] = 10;
			this.tags[i].style.zIndex = parseInt(this.tags.length - 1 + this.minindex - i);

			switch(this.playicon){
				case "onmouseover":
				case "onmouseout":
				case "onmouseclick": 
					eval("this.tags[i]." + this.playicon + " = function(){ obj.runstart(); };");
				break;
			}

			switch(this.stopicon){
				case "onmouseover":
				case "onmouseout":
				case "onmouseclick": 
					eval("this.tags[i]." + this.stopicon + " = function(){ obj.runstop(); };");
				break;
			}
		}

		if(this.playicon && (this.playicon.indexOf("onmouse") != -1)){
			this.playicon = null;
		}

		if(this.idbg){
			this.idbg.style.zIndex = this.tags.length + 8;
			if(this.idbg.offsetWidth == 0) this.idbg.style.width = this.idpic.offsetWidth + "px";
		}

		if(this.idinf){
			this.idinf.style.zIndex = this.tags.length + 10;
			if(this.idinf.offsetWidth == 0) this.idinf.style.width = this.idpic.offsetWidth + "px";
		}

		if(this.idpic.getElementsByTagName(this.tagname).length > 1){	
			this.current(this.lino);
			if(this.playing) this.runop = setTimeout(this.bind(this.run, this.lino, 10), this.delay);
		}
	},

	bind: function(){
		var args = Array.prototype.slice.apply(arguments);
		var obj = this;
		return function(){ args.shift().apply(obj, args); };
	},

	getNodeById: function(id){
		var tags;
		var bodyhtml = document.body.innerHTML;
		var reg = new RegExp("<(\\w*)([^>]*) id=[\'\"]?" + id + "[\'\"]?([^>]*)>", "i");
		bodyhtml.replace(reg, function(){ tags = arguments[1]; });

		var getags = document.getElementsByTagName(tags);
		for(var i = 0; i < getags.length; i++){
			if(getags[i].id == id) return getags[i];
		}
		return false;
	},

	cycImgChild: function(parent_node, tag_child){
		var htmltag = tag_child[0].replace(/<|>/g, "");
		var newtag = document.createElement(htmltag);
		var setsize = false;

		for(var c = 1; c < tag_child.length; c++){
			if(tag_child[c][0].indexOf("<") == -1){
				if((tag_child[c][0] == "width") || (tag_child[c][0] == "height")) setsize = true;
				try { eval("newtag." + tag_child[c][0] + "='" + tag_child[c][1] + "'"); }
				catch (e){}
			}
			else {
				this.cycImgChild(newtag, tag_child[c]);
			}
		}
		// IE may create different size 
		if((!setsize) && (htmltag == "img")){
			newtag.removeAttribute("width");
			newtag.removeAttribute("height");
		}
		parent_node.appendChild(newtag);
	},

	append_node: function(parent_node, child_element, child_text, child_class){
		child_element = document.createElement(child_element);
		if(child_text){
			child_text = document.createTextNode(child_text);
			child_element.appendChild(child_text);
		}
		if(child_class) child_element.className = child_class;

		parent_node.appendChild(child_element);
	},

	current: function(now){
		var cur = 0;
		var end = 0;
		
		if(this.clickimg){
			for(var s = 0; s < this.clickimgtag.length; s++){
				if((now + 1) == this.clickimgtag[s].imgclicknum) this.clickimgtag[s].className = this.ahover;
				else this.clickimgtag[s].className = this.alink;
			}		
		}

		if(this.previcon){
			cur = 1;
			now++;
		}
		if(this.nexticon) end++;
		if(this.playicon) end++;

		if(this.idnum) {
			var atag = this.idnum.getElementsByTagName(this.icontag);
			for(; cur < atag.length - end; cur++){
				atag[cur].className = this.alink;
			}
			atag[now].className = this.ahover;
		}
		if(this.idinf){
			if(this.previcon) now--;
			if(this.tags[now].getElementsByTagName("img").length){
				this.idinf.innerHTML = this.tags[now].getElementsByTagName("img")[0].getAttribute("alt");
			}
			else {
				this.idinf.innerHTML = this.tags[now].getAttribute("alt");
			}
		}
	},

	opacity: function(num, percent){
		this.picop[num] = percent;
		if(document.all) this.tags[num].style.filter = "Alpha(opacity=" + percent*10 + ")";
		else this.tags[num].style.opacity = percent / 10;
	},

	run: function(linum, value){
		value = value - this.runstep;

		if(value >= 0){
			this.opacity(linum, value);
			this.runop = setTimeout(this.bind(this.run, linum, value), this.rundelay);
		}
		else {
			this.opacity(linum, 0);
			this.alter_z(linum + 1);
			this.alter_o(10, 0, this.tags.length);
			this.cycle(linum);
		}
	},

	runback: function(linum, value){
		value = value + this.runstep;

		if(value <= 10){
			this.opacity(linum, value);
			this.runop = setTimeout(this.bind(this.runback, linum, value), this.rundelay);
		}
		else {
			this.lino = linum;
			this.opacity(linum, 10);
			this.current(this.lino);
			this.alter_o(10, 0, this.tags.length);
			this.alter_z(this.lino);
			if(this.playing) this.runop = setTimeout(this.bind(this.run, linum, 10), this.delay);
		}
	},

	runstop: function(){
		this.playing = false;
		clearTimeout(this.runop);
	},

	runstart: function(){
		this.playing = true;
		this.runop = setTimeout(this.bind(this.run, this.lino, this.picop[this.lino]), 0);
	},

	alter_z: function(linum) {
		var n = 0;
		for(; n < linum; n++){
			this.tags[n].style.zIndex = parseInt(this.minindex + linum - n - 1);
		}
		for(; n < this.tags.length; n++){
			this.tags[n].style.zIndex = parseInt(this.tags.length + this.minindex - n - 1 + linum);
		}
	},

	alter_o: function(value, begin, end){
		for(var n = begin; n < end; n++){
			this.opacity(n, value);
		}
	},

	cycle: function(linum) {
		if(linum == this.tags.length - 1) this.lino = 0;
		else this.lino = linum + 1;
		this.current(this.lino);
		if(this.playing) this.runop = setTimeout(this.bind(this.run, this.lino, 10), this.delay);
	},

	prext: function(dir) {
		clearTimeout(this.runop);
		if(!dir) this.run(this.lino, this.picop[this.lino]);
		else {
			var litemp = this.lino + dir;
			if(litemp < 0) litemp = this.tags.length - 1;
			this.opacity(litemp, 0);
			this.alter_z(litemp);
			this.runback(litemp, this.picop[litemp]);
		}
	},

	gonum: function(num) {
		num--;
		clearTimeout(this.runop);
		switch(num - this.lino){
			case 0:
				this.runop = setTimeout(this.bind(this.run, this.lino, 10), this.delay);
				break;
			case 1: 
				this.prext(0);
				break;
			case -1:
				this.prext(-1);
				break;
			default: {
				if(num > this.lino){
					this.alter_o(0, 0, this.lino);
					this.alter_o(0, num, this.tags.length);
				}
				else this.alter_o(0, num, this.lino);
				this.alter_z(num);
				this.runback(num, this.picop[num]);
			}
		}
	}
};

