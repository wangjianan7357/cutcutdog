<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$member_list = array();
$getdata = $my_db->selectRow('*', 'member');
while ($result = mysql_fetch_array($getdata)) {
    $member_list[$result['id']] = $result;
}

$cur_catalog = $my_db->fetchOne('catalog', array('type' => 4, 'id' => $_GET['id']));

require('head.php');
?>

<div class="title1">
    <div class="txt">討論區</div>
    <div class="arrow_right"></div>
    <div class="txt"><?= $cur_catalog['name']; ?></div>
</div>

<br>

<div class="two1"><img src="images/two.png"></div>

<div class="bd2 discuss">
    <p><img src="images/discuss-banner-<?= $cur_catalog['id']; ?>.jpg"></p>
    <ul class="list2">
    <?php
        $i = 0;
        $getdata = $my_db->selectRow('*', 'info', array('cid' => $cur_catalog['id'] . ','), array('method' => 'DESC', 'field' => 'date'), '0,3');
        while ($result = mysql_fetch_array($getdata)) {
            $i ++;
    ?>
        <li<?= $result['read'] ? '' : ' class="new"'; ?>>
            <a href="">
                <div class="name"><?= $result['name']; ?></div>
                <div class="cl1">
                    <div class="fl">
                        發帖人：<?= $member_list[$result['mid']]['name']; ?>
                    </div>
                    <div class="fr">
                        發帖日期：<?= $result['date']; ?>
                    </div>
                </div>
                <?php if ($last = $my_db->fetchOne('message', array('atype' => 4, 'aid' => $result['id']), array('method' => 'DESC', 'field' => 'date'))) { ?>
                <div class="cl1">
                    <div class="fl">
                        最後回復：<?= $member_list[$last['mid']]['name']; ?>
                    </div>
                    <div class="fr">
                        發帖日期：<?= $last['date']; ?>
                    </div>
                </div>
                <?php } ?>
            </a>
        </li>
    <?php } ?>
    </ul>
</div>

<?php require('foot.php'); ?>