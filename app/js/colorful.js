// JavaScript Document
//这个只是让他好看一点而已，并没有什么卵用
Element.prototype.colorfulBg=function(){
function changeColor(e){ 
if (e.style.backgroundColor!=e.previousElementSibling.style.backgroundColor){ 
return; 
}else{ 
	var rd = parseInt(Math.random()*colors.length);
	e.style.backgroundColor=colors[rd];
return changeColor(e); 
} 
} 
	var colors=["#fff","#fff","#fff","#fff","#fff","#fff","#f2eff0"];
	var rd = parseInt(Math.random()*colors.length);
	this.style.backgroundColor=colors[rd];
	if(this.previousElementSibling){
	changeColor(this)
	}
	}
