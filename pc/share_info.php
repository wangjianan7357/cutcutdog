<?php
require_once('include/fun_web.php');
require_once('include/common.php');

$cur_data = $my_db->fetchOne('info', array('id' => $_GET['id']));
$info_img_path = systemConfig('info_img_path');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
.tu_stlye{ float:left;width:50%;}
.tu_stlye img{width:100%;}
.tu_stlye_zi{ float:left; padding-left:10px; width:45%;}
.tu_stlye_zi div{width:100%; font-size:14px; color:#333; margin-bottom:10px; line-height:25px;}
</style>
</head>
<body>
    <div style="overflow:hidden; border:#CCC solid 1px; padding:10px;">
        <div class="tu_stlye">
            <img src="<?= $info_img_path . $con_pic['pre']['info'] . $con_pic['suf']['mid'] . $cur_data['src']; ?>" />
        </div>
        <div class="tu_stlye_zi">
            <div>名稱: <?= $cur_data['name'] ;?></div>
            <div>電話: <?= $cur_data['tel'] ;?></div>
            <div>地址: <?= $cur_data['address'] ;?></div>
            <div>網址: <?= $cur_data['website'] ;?></div>
        </div>
    </div>
</body>
</html>