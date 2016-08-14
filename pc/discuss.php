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
    <div class="txt">討論區</div>
</div>

<br>

<div class="bd1 discuss">
<?php
    $i = 0;
    $getdata = $my_db->selectRow('*', 'catalog', array('type' => 4));
    while ($result = mysql_fetch_array($getdata)) {
        $i ++;
?>
    <div class="row cl1">
        <div class="cap cell<?= $i; ?>">
            <a href="discuss-list.php?id=<?= $result['id']; ?>">
                <p><img src="images/discuss-<?= $i; ?>.gif"></p>
                <p><?= $result['name']; ?></p>
            </a>
        </div>
        <div class="desc">
        <?php
            $getdata1 = $my_db->selectRow('*', 'info', array('cid' => $result['id'] . ','), array('method' => 'DESC', 'field' => 'date'), '0,3');
            while ($result1 = mysql_fetch_array($getdata1)) {
        ?>

            <a class="list cl1" href="">
                <div class="name"><?= $result1['name']; ?></div>
                <div class="detail"><?= cutString(trim(strip_tags($result1['desp'])), 90); ?></div>

                <div class="member">
                    發帖人：<?= $member_list[$result1['mid']]['name']; ?>
                </div>
            </a>

        <?php } ?>
        </div>
    </div>
<?php } ?>
</div>

<?php require('foot.php'); ?>