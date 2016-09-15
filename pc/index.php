<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

require('head.php');

$info_list = array();
$getdata = $my_db->selectRow('*', 'info', array('cid = "104,"'), array('method' => 'ASC', 'field' => 'ID'), '0,3');
while ($result = mysql_fetch_array($getdata)) {
    $info_list[] = $result;
}
?>

<div class="wed_tlq">
    <p class="wed_tite">討論區</p>
    <p class="wed_tite_zi"><?= strip_tags($info_list[0]['desp']); ?></p>
</div>

<div class="wed_xpq">
    <p class="wed_xpq_tite">相片區</p>
    <p class="wed_xpq_zi"><?= strip_tags($info_list[1]['desp']); ?></p>
    <div class="wed_xpq_photo">
    <?php
        $i = 0;
        $getdata = $my_db->selectRow('*', 'info', array('cid = "13," AND src != ""'), array('method' => 'DESC', 'field' => 'date'), '0,2');
        while ($result = mysql_fetch_array($getdata)) {
            $i ++;
    ?>
        <div class="pic<?= $i; ?>"><img src="<?= PIC_INFO_M . $result['src']; ?>" width="138" height="162" /></div>
    <?php } ?>
    </div>
</div>

<div class="wed_buy">
    <p class="wed_buy_tite">買賣平台</p>
    <p class="wed_buy_zi"><?= strip_tags($info_list[2]['desp']); ?></p>
    <div class="wed_buy_photo">
    <?php
        $i = 0;
        $getdata = $my_db->selectRow('*', 'product', array('cid' => array('in' => '("29,", "30,", "31,", "32,")')), array('method' => 'DESC', 'field' => 'date'), '0,3');
        while ($result = mysql_fetch_array($getdata)) {
            $i ++;
    ?>
        <div class="pic pic<?= $i; ?>"><img src="<?= PIC_PRODUCT_S . $result['src']; ?>"></div>

    <?php } ?>
    </div>
</div>

<?php require('foot.php'); ?>