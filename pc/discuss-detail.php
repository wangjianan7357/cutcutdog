<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$member_list = array();
$getdata = $my_db->selectRow('*', 'member');
while ($result = mysql_fetch_array($getdata)) {
    $member_list[$result['id']] = $result;
}

$cur_data = $my_db->fetchOne('info', array('id' => $_GET['id']));

require('head.php');
?>

<div class="title1">
    <div class="txt">討論區</div>
    <div class="arrow_right"></div>
    <div class="txt"><?= $cur_data['name']; ?></div>
</div>

<br>

<div class="two1"><img src="images/two.png"></div>

<div class="bd2 discuss">
    <div>
        發帖人：<?= $member_list[$cur_data['mid']]['name']; ?>
    </div>
    <div class="fr">
        發帖日期：<?= $cur_data['date']; ?>
    </div>

    <div>
        <?= $cur_data['desp']; ?>
    </div>

    <?php if ($last = $my_db->fetchOne('message', array('atype' => 4, 'aid' => $cur_data['id']), array('method' => 'DESC', 'field' => 'date'))) { ?>
    <div class="cl1">
        <div class="fl">
            最後回復：<?= $member_list[$last['mid']]['name']; ?>
        </div>
        <div class="fr">
            發帖日期：<?= $last['date']; ?>
        </div>
    </div>
    <?php } ?>

</div>

<?php require('foot.php'); ?>