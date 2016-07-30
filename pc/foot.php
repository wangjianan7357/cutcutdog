    </div>
</div>

<div class="foot">
    <div class="foot_bg">
        <ul>
            <li><a href="#">資料庫</a></li>
            <li><a href="#">討論區</a></li>
            <li><a href="#">相片區</a></li>
            <li><a href="#">上門服務</a></li>
            <li><a href="#">買賣平台</a></li>
            <li><a href="#">二手平台</a></li>
            <li><a href="#">聯繫我們</a></li>
        </ul>
        <p style="margin-top:20px;">Copyrights 2016-2019 . PetChat-寵物潮流 </p>
    </div>
</div> 

<script type="text/javascript">
$(function(){
    $(".select").each(function(){
        var s = $(this);
        var z = parseInt(s.css("z-index"));
        var dt = $(this).children("dt");
        var dd = $(this).children("dd");
        var _show = function(){dd.slideDown(200);dt.addClass("cur");s.css("z-index",z+1);};   //展开效果
        var _hide = function(){dd.slideUp(200);dt.removeClass("cur");s.css("z-index",z);};    //关闭效果
        dt.click(function(){dd.is(":hidden")?_show():_hide();});
        dd.find("a").click(function(){dt.html($(this).html());_hide();});     //选择效果（如需要传值，可自定义参数，在此处返回对应的“value”值 ）
        $("body").click(function(i){ !$(i.target).parents(".select").first().is(s) ? _hide():"";});
    });
});
</script>
</body>
</html>
