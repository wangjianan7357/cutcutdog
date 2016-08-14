<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$member_arr = $my_db->fetchOne('member', array('id' => intval($_GET['id']), 'type' => 10));

if (!$member_arr) {
    header('Location: service.php');
    exit;
}

require('head.php');
?>

<div class="title1">
    <div class="txt">上門服務</div>
</div>

<br>

<div class="two1"><img src="images/two.png"></div>

<div class="photo">


</div>

<div class="service_detail">
    <?= $member_arr['desp']; ?>
</div>

<?php require('foot.php'); ?>