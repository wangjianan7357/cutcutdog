function a(x,y){
	try {
		l = $('#main').offset().right;
		w = $('#main').width();
		$('#tbox').css('right',0 + 'px');
		$('#tbox').css('bottom',y + 'px');
	} catch(e){}
}
function b(){
	try {
		h = $(window).height();
		t = $(document).scrollTop();
		if(t > h){
			$('#gotop').fadeIn('slow');
		}else{
			$('#gotop').fadeOut('slow');
		}
	} catch(e){}
}
$(document).ready(function(e) {		
	a(10,10);//#tbox的div距浏览器底部和页面内容区域右侧的距离
	b();
	$('#gotop').click(function(){
		$(document).scrollTop(0);	
	})
});
$(window).resize(function(){
	a(10,10);//#tbox的div距浏览器底部和页面内容区域右侧的距离
});

$(window).scroll(function(e){
	b();		
})
