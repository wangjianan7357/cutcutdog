<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$member_arr = $my_db->fetchOne('member', array('id' => intval($_GET['id']), 'type' => 10));
$member_arr['fields'] = json_decode($member_arr['fields'], true);

if (!$member_arr) {
    header('Location: service.php');
    exit;
}

$service = array();
$getdata = $my_db->selectRow('*', 'service', array('valid' => 1));
while ($result = mysql_fetch_array($getdata)) {
    $service[$result['id']] = $result;
}

$member_arr['service'] = array();
$getdata = $my_db->selectRow('vid', 'property_content', array('pid' => $member_arr['id'], 'sort' => 3));
while($result = mysql_fetch_array($getdata)) {
    $member_arr['service'][] = $service[$result['vid']];
}

require('head.php');
?>

<div class="title1">
    <div class="txt">上門服務</div>
</div>

<br>

<div class="member_detail cl1">
    <div class="fl">
        <img src="<?= $member_arr['src']; ?>" width="400">
    </div>
    <div class="fr desc">
        <div class="txt">
            <p>美容師名：<?= $member_arr['name']; ?></p>
            <p>年資：<?= $member_arr['fields']['experience']; ?>年</p>
            <p class="service">
                服務範圍：
                <?php 
                    foreach ($member_arr['service'] as $val) {
                        echo '<img src="' . PIC_SERVICE . $val['icon'] . '" height="40"> ';
                    }
                ?>
            </p>
            <p>查詢電話：<?= $member_arr['phone']; ?></p>
            <p class="mgt10"><a href="" class="btn">立即預約</a></p>
        </div>

        <p><img src="images/dog.png"></p>
    </div>

</div><br>

<div class="service_detail">
    <?= $member_arr['desp']; ?>
</div>

<?php require('foot.php'); ?>