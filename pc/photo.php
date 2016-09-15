<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$member_list = array();
$getdata = $my_db->selectRow('*', 'member');
while ($result = mysql_fetch_array($getdata)) {
    $member_list[$result['id']] = $result;
}

require('head.php');
?>

<div class="title1">
    <div class="txt">相片區</div>
</div>

<br>

<div class="two1"><img src="images/two.png"></div>

<div class="photo">
<?php
    $getdata = $my_db->selectRow('*', 'info', array('cid' => '13,'), array('method' => 'DESC', 'field' => 'date'), '0,18');
    while ($result = mysql_fetch_array($getdata)) {
?>

    <a class="list" href="<?= PIC_INFO_M . $result['src']; ?>" target="_blank"><img src="<?= PIC_INFO_M . $result['src']; ?>" width="231" height="231" /></a>

<?php } ?>

</div>

<?php require('foot.php'); ?>