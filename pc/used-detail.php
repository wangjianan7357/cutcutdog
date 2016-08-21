<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$cur_data = $my_db->fetchOne('product', array('id' => $_GET['id']));

$cid = explode(',', $cur_data['cid']);
$cur_catalog = $my_db->fetchOne('catalog', array('id' => $cid[count($cid) - 2]));

require('head.php');
?>

<div class="title1">
    <div class="txt">二手平台</div>
    <div class="arrow_right"></div>
    <div class="txt"><?= $cur_catalog['name']; ?></div>
</div>

<div class="product_detail mgt10">
    <div>
        <img src="<?= PIC_PRODUCT_M . $cur_data['src']; ?>" width="500">
    </div>

    <div class="desc1 mgt5">
        <b class="cap">產品名稱：</b><?= $cur_data['name']; ?>
    </div>
    <div class="desc1">
        <b class="cap">價錢：</b>$<?= $cur_data['sale']; ?>
    </div>
    <div class="desc1">
        <b class="cap">產品資料：</b><?= $cur_data['desc']; ?>
    </div>

    <div class="cl1 cart_btn mgt5">
        <a href="javascript:;" onclick="addToCart(<?= $cur_data['id']; ?>, 'cart.php')" style="background: #59be96;">立即購買</a>
        <a href="cart.php" style="background: #5abeef;">檢視購物車</a>
        <a href="javascript:;" onclick="addToCart(<?= $cur_data['id']; ?>)" style="background: #9073b3;">加入購物車</a>
        <!--<a href="" style="background: #f89a76;">收藏</a>-->
    </div>
</div>

<?php require('foot.php'); ?>