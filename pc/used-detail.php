<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$cur_data = $my_db->fetchOne('product', array('id' => $_GET['id']));

require('head.php');
?>

<div class="title1">
    <div class="txt">二手平台</div>
    <div class="arrow_right"></div>
    <div class="txt"><?= $cur_catalog['name']; ?></div>
</div>

<div class="photo">
<?php
    $getdata = $my_db->selectRow('*', 'product', array('cid' => $_GET['id'] . ','), array('method' => 'DESC', 'field' => 'date'), '0,18');
    while ($result = mysql_fetch_array($getdata)) {
?>

    <a class="list" href=""><img src="<?= PIC_PRODUCT_M . $result['src']; ?>" width="231" height="231" /></a>

<?php } ?>

</div>

<?php require('foot.php'); ?>