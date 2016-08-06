<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

require('head.php');
?>

<div class="title1">
    <div class="txt">上門服務</div>
</div>

<br>

<div class="two1"><img src="images/two.png"></div>

<div class="photo">
<?php
    $getdata = $my_db->selectRow('*', 'member', array('valid' => 1, 'type' => 10), array('method' => 'DESC', 'field' => 'date'));
    while ($result = mysql_fetch_array($getdata)) {
?>

    <a class="list" href="service-detail.php?id=<?= $result['id']; ?>"><img src="<?= $result['src']; ?>" width="231" height="231" /></a>

<?php } ?>

</div>

<?php require('foot.php'); ?>