
function checkForm(method){
	this.formConfig = {classFocus: "focus", classBlur: "blur", warnColor: "red"};

	this.check = function(method){
		var obj = this;
		var dhref = document.location.href;
		var text_please_enter = document.getElementById("c_please_enter").value;
		var text_please_select = document.getElementById("c_please_select").value;
		var text_email_is_invalid = document.getElementById("c_email_invalid").value;

		// check all match tags
		if(method == "init"){
			var alltag = ["input", "textarea"];
			for(j = 0; j < alltag.length; j++){
				var getag = document.getElementsByTagName(alltag[j]);
				for(i = 0; i < getag.length; i++){
					if(getag[i].className == obj.formConfig.classBlur){
						getag[i].onfocus = function(){
							this.className = obj.formConfig.classFocus;
							if(this.id && this.id.indexOf("_dob") != -1) WdatePicker({maxDate:'%y-%M-%d'});
						};
						getag[i].onblur = function(){ this.className = obj.formConfig.classBlur; };
					}
				}
			}
		}

		var mailreg = /^[._a-zA-Z0-9-]+@([-_a-zA-Z0-9]+\.)+[a-zA-Z0-9]{2,4}$/;
		var getform = document.getElementsByTagName("form");

		for(i = 0; i < getform.length; i++){
			if(method == "init" || method == getform[i]){
				if(getform[i].id != "contact_form") continue;

				var getr = getform[i].getElementsByTagName("tr");
				for(j = 0; j < getr.length; j++){
					var tdesp = getr[j].getElementsByTagName("td")[0].innerHTML;
					if(tdesp.indexOf("*") != -1){
						var operate = "enter";
						//var getd = getr[j].getElementsByTagName("p")[1];
						//if(!getd) continue;

						var getele = getr[j].getElementsByTagName("input")[0];
						if(!getele) getele = getr[j].getElementsByTagName("textarea")[0];
						if(!getele){
							operate = "select";
							getele = getr[j].getElementsByTagName("select")[0];
						}

						if(!getele) continue;
						
						if(method == "init"){
							getele.onblur = function(){
								this.className = obj.formConfig.classBlur;
								var chvalid = true;
								var ftd = this.parentNode.parentNode.getElementsByTagName("td")[0];

								if(this.id.indexOf("_email") != -1) chvalid = mailreg.test(this.value);

								if(!this.value || !chvalid) ftd.style.color = obj.formConfig.warnColor;
								/*
								else if(this.name == "reg_username"){
									var xmlHttp = null;
									if(window.XMLHttpRequest) xmlHttp = new XMLHttpRequest();
									else if(window.ActiveXObject){
										try { xmlHttp = new ActiveXObject("Msxml2.XMLHTTP"); }
										catch(e){
											try { xmlHttp = new ActiveXObject("Microsoft.XMLHTTP"); }
											catch(e){}
										}
									}

									var tempdiv = document.createElement("div");
									xmlHttp.onreadystatechange = function(){
										if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
											if(xmlHttp.responseText) alert(xmlHttp.responseText)
										}
									};

									xmlHttp.open("POST", "login.php", true);
									xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
									xmlHttp.send("register=check&reg_username=" + this.value);	
								}
								*/
							};
							
							getele.onfocus = function(){
								this.className = obj.formConfig.classFocus;
								this.parentNode.parentNode.getElementsByTagName("td")[0].style.color = "";
							};
						}
						else {
							tdesp = tdesp.replace(/<\/?[^>]*>|\*|:|\s*/gi, "");
							if(!getele.value){
								if(operate == "enter")	alert(text_please_enter + " " + tdesp);
								else if(operate == "select")	alert(text_please_select + " " + tdesp);
								return false;
							}
							else if(getr[j].getElementsByTagName("input")[0].id.indexOf("_email") != -1){
								if(!mailreg.test(getele.value)){
									alert(text_email_is_invalid);
									return false;
								}
							}
						}
					}
				}
			}
		}
		return true;
	};

	return this.check(method);
}

checkForm("init");