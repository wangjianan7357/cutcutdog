    </div>
</div>

<div class="foot">
    <div class="foot_bg">
        <ul>
            <li><a href="info.php">資料庫</a></li>
            <li><a href="discuss.php">討論區</a></li>
            <li><a href="photo.php">相片區</a></li>
            <li><a href="service.php">上門服務</a></li>
            <li><a href="buy.php">買賣平台</a></li>
            <li><a href="used.php">二手平台</a></li>
            <li><a href="contact.php">聯繫我們</a></li>
        </ul>
        <p style="margin-top:20px;">Copyrights 2016-2019 . PetChat-寵物潮流 </p>
    </div>
</div> 

<script type="text/javascript" src="javascript/script.js"></script>
<script type="text/javascript">
$("[data-select]").each(function(){
    var s = $(this);
    var z = parseInt(s.css("z-index"));
    var dt = $(this).children("dt");
    var dd = $(this).children("dd");
    var _show = function(){dd.slideDown(200); dt.addClass("cur");s.css("z-index", z + 1);};
    var _hide = function(){dd.slideUp(200); dt.removeClass("cur");s.css("z-index", z);};

    dt.click(function(){
        dd.is(":hidden") ? _show() : _hide();
    });

    //选择效果（如需要传值，可自定义参数，在此处返回对应的“value”值 ）
    dd.find("a").click(function(){
        dt.html("<span class='fl'>" + $(this).html() + "</span><b></b>"); 
        _hide();
    });

    $("body").click(function(i){ 
        !$(i.target).parents("[data-select]").first().is(s) ? _hide() : "";
    });
});

$("[name-cid]").click(function(){
    $("[name='cid']").val($(this).attr("name-cid"));
});
$("[name-service]").click(function(){
    $("[name='service']").val($(this).attr("name-service"));
});
</script>
</body>
</html>
