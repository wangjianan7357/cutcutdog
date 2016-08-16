<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$cur_catalog = $my_db->fetchOne('catalog', array('type' => 7, 'id' => $_GET['id']));

require('head.php');
?>

<div class="title1">
    <div class="txt">買賣平台</div>
    <div class="arrow_right"></div>
    <div class="txt"><?= $cur_catalog['name']; ?></div>
</div>

<div class="p_list1">
<?php
    $getdata = $my_db->selectRow('*', 'product', array('cid' => $_GET['id'] . ','), array('method' => 'DESC', 'field' => 'date'), '0,18');
    while ($result = mysql_fetch_array($getdata)) {
?>

    <a class="list" href="buy-detail.php?id=<?= $result['id']; ?>">
        <p><img src="<?= PIC_PRODUCT_M . $result['src']; ?>" width="216" height="200" /></p>
        <p><?= $result['name']; ?></p>
        <p>$<?= $result['sale']; ?></p>
    </a>

<?php } ?>

</div>

<?php require('foot.php'); ?>