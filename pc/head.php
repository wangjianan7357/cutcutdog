<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PetChat-寵物潮流</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="javascript/jquery.min.js"></script>
<?php
    if($err) echo '<script language="javascript">alert("' . $err . '"); history.go(-1); </script>';
    if($msg) echo '<script language="javascript">alert("' . $msg . '"); </script>';
    if($href) echo '<script language="javascript">document.location.href = "' . $href . '"; </script>';
?>
</head>

<body>
<div class="wed_top">
    <div class="wed_width">
        <div class="wed_top_logo"><a href="/"><img src="images/petchat.png" /></a></div>
        <div class="wed_top_logo_right"><img src="images/catdog.png" /></div>
        <div class="wed_top_f"><a href="https://www.facebook.com/petchat2016/" target="_blank"><img src="images/f.jpg" /></a></div>
    </div>
</div>

<div class="wed_width">
    <div class="wed_right">
        <div class="wed_right_grzx">
            <a href="#"><img src="images/tu_04.jpg" /></a>
        </div>

        <div class="wed_right_sece">
            <p>
                <span>店舖名稱</span> 
                <input name="name" type="text" value="" class="text1">
            </p>

            <div class="demo">
                <input type="hidden" name="cid" value="">
                <dl class="select" data-select>
                    <dt>
                        <span class="fl">地區</span>
                        <b></b>
                    </dt>
                    <dd>
                        <ul>
                        <?php
                            $getdata = $my_db->selectRow('id, name, parent', 'catalog', array('type' => 3, 'parent' => ''));
                            while ($result = mysql_fetch_array($getdata)) {
                        ?>
                            <li><a href="javascript:;" name-cid="<?= $result['id']; ?>,"><?= $result['name']; ?></a></li>
                        <?php
                            $getdata1 = $my_db->selectRow('id, name, parent', 'catalog', array('type' => 3, 'parent' => $result['id'] . ','));
                            while ($result1 = mysql_fetch_array($getdata1)) {
                        ?>
                            <li><a href="javascript:;" name-cid="<?= $result['id']; ?>,<?= $result1['id']; ?>,"><?= $result['name'] . ' - ' . $result1['name']; ?></a></li>
                        <?php }} ?>
                        </ul>
                    </dd>
                </dl>

                <input type="hidden" name="service" value="">
                <dl class="select" data-select>
                    <dt>
                        <span class="fl">服務範圍</span>
                        <b></b>
                    </dt>
                    <dd>
                        <ul>
                        <?php foreach ($cms_service_type as $key => $value) { ?>
                            <li><a href="javascript:;" name-service="<?= $key; ?>"><?= $value; ?></a></li>
                        <?php } ?>
                        </ul>
                    </dd>
                </dl>

                <button class="wed_right_soso" data-action="search">搜 尋</button>
            </div>

            <div class="wed_dpzl">
                <ul data-list="info">
                <?php
                    $getdata = $my_db->selectRow('id, parent', 'catalog', array('type' => 3));
                    while ($result = mysql_fetch_array($getdata)) {
                        $catalog[] = $result['parent'] . $result['id'] . ',';
                    }

                    $where = array('valid' => 1);
                    $where['cid'] = array('in' => '("' . implode('", "', $catalog) . '")');

                    $getdata = $my_db->selectRow('*', 'info', $where, array('method' => 'DESC', 'field' => 'date'), '0,2');
                    while ($result = mysql_fetch_array($getdata)) {
                ?>
                    <li>
                        <div class="tu_border"><img src="<?= PIC_INFO_M . $result['src'];?>" /></div>
                        <div class="wed_dpzl_zi"><img src="images/info_icon_home.png" /><span><?= $result['name']; ?></span></div>
                        <div class="wed_dpzl_zi"><img src="images/info_icon_pin.png" /><span><?= $result['address']; ?></span></div>
                        <div class="wed_dpzl_zi"><img src="images/info_icon_tel.png" /><span><?= $result['tel']; ?></span></div>
                        <div class="wed_dpzl_zi"><a href="<?= $result['website']; ?>" target="_blank"><img src="images/info_icon_www.png" /><span><?= $result['website']; ?></span></a></div>
                    </li>
                <?php } ?>
                </ul>
            </div>

            <div class="wed_dpzl_sxy">
                <ul>
                    <li><a href="javascript:;" data-action="prev">上一頁</a></li>
                    <li><a href="javascript:;" data-action="next">下一頁</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var info_page = 1;
        var info_total = <?= ceil($my_db->existRow('info', $where) / 2); ?>;

        function searchInfo() {
            $.ajax({
                type: "get",
                url: "ajax.php",
                dataType: "json",
                data: {
                    action: "info-list",
                    name: $("[name='name']").val(),
                    cid: $("[name='cid']").val(),
                    service: $("[name='service']").val(),
                    pages: info_page
                },
                async: true,
                success: function(json) {
                    info_total = json.total;
                    $("[data-list='info']").html("");

                    $.each(json.list, function(con, val){
                        var code = '<li><div class="tu_border"><img src="' + val.src + '" /></div><div class="wed_dpzl_zi"><img src="images/info_icon_home.png" /><span>' + val.name + '</span></div><div class="wed_dpzl_zi"><img src="images/info_icon_pin.png" /><span>' + val.address + '</span></div><div class="wed_dpzl_zi"><img src="images/info_icon_tel.png" /><span>' + val.tel + '</span></div><div class="wed_dpzl_zi"><a href="' + val.website + '" target="_blank"><img src="images/info_icon_www.png" /><span>' + val.website + '</span></a></div></li>';

                        $("[data-list='info']").append(code);
                    });
                }
            })
        }

        $("[data-action='search']").click(function(){
            info_page = 1;
            searchInfo();
        });

        $("[data-action='prev']").click(function(){
            if (info_page > 1) {
                info_page --;
                searchInfo();
            }
        });

        $("[data-action='next']").click(function(){
            if (info_page < info_total) {
                info_page ++;
                searchInfo();
            }
        });

    </script>
 
    <div class="wed_left">
        <ul class="wed_menu_ul">
            <li><a href="info.php">資料庫 <img src="images/ico_01.png" /></a></li>
            <li><a href="discuss.php">討論區 <img src="images/ico_02.png" /></a></li>
            <li><a href="photo.php">相片區 <img src="images/ico_03.png" /></a></li>
            <li><a href="service.php">上門服務 <img src="images/ico_04.png" /></a></li>
            <li><a href="buy.php">買賣平台 <img src="images/ico_05.png" /></a></li>
            <li><a href="used.php">二手平台 <img src="images/ico_06.png" /></a></li>
            <li><a href="contact.php">聯繫我們 <img src="images/ico_07.png" /></a></li>
        </ul>
  
        <div class="banner">ff</div>