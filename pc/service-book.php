<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

loginTimeout();

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

if ($_POST['book'] == 'send') {
    $submit = array(
        'mid' => $_COOKIE['cookie']['memberid'],
        'tid' => $_POST['sbt_tid'],
        'service' => $_POST['sbt_service'],
        'pet' => $_POST['sbt_pet'],
        'size' => $_POST['sbt_size'],
        'name' => $_POST['sbt_name'],
        'phone' => $_POST['sbt_phone'],
        'address' => $_POST['sbt_address'],
        'number' => $_POST['sbt_number'],
        'time' => $_POST['sbt_time'],
        'remark' => $_POST['sbt_remark'],
    );

    if($my_db->saveRow('booking', $submit)){
        $technician = $my_db->fetchOne('member', array('id' => $_POST['sbt_tid']));
        $submit['technician'] = $technician['name'];

        $mail = new Emailer($con_mail_set);
        $mail->setFields(array('technician' => '美容師'));
        $mail->content($submit, '上門服務：' . $submit['service']);

        if($mail->send()) {

        }
    }
}

require('head.php');
?>

<div class="title1">
    <div class="txt">上門服務</div>
    <div class="arrow_right"></div>
    <div class="txt">預約</div>
</div>

<br>

<form class="service_booking cl1" method="post" action="service-book.php?id=<?= intval($_GET['id']); ?>">
    <input type="hidden" name="book" value="true">
    <input type="hidden" name="sbt_number" value="1">

    <div class="cl1 input1">
        <div class="cap">服務類型</div>
        <div class="input_select1" data-action="service">
            <input type="hidden" name="sbt_service">

            <div class="fl" data-value="寵物洗澡">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <img src="images/service-icon-1.png" class="vc"> &nbsp;
                寵物洗澡 &nbsp;&nbsp;&nbsp;
            </div>
            <div class="fl" data-value="上門美容">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <img src="images/service-icon-2.png" class="vc"> &nbsp;
                上門美容 &nbsp;&nbsp;&nbsp;
            </div>
            <div class="fl" data-value="寵物傳心">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <img src="images/service-icon-3.png" class="vc"> &nbsp;
                寵物傳心 &nbsp;&nbsp;&nbsp;
            </div>
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">寵物類型</div>
        <div class="input_select1" data-action="pet">
            <input type="hidden" name="sbt_pet[]">

            <div class="fl" data-value="狗">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <img src="images/service-icon-4.png" class="vc"> &nbsp;&nbsp;&nbsp;
            </div>
            <div class="fl" data-value="貓">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <img src="images/service-icon-5.png" class="vc"> &nbsp;&nbsp;&nbsp;
            </div>
            <input class="fl bd3" size="30" placeholder="品種請填寫" name="sbt_pet[]">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">寵物SIZE</div>
        <div class="size input_select2" data-action="size">
            <input type="hidden" name="sbt_size">

            <div class="fl" data-value="XS">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <b>XS</b><br>&lt;3KG
            </div>
            <div class="fl" data-value="S">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <b>S</b><br>3-7KG
            </div>
            <div class="fl" data-value="M">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <b>M</b><br>8-12KG
            </div>
            <div class="fl" data-value="L">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <b>L</b><br>13-20KG
            </div>
            <div class="fl" data-value="XL">
                <p class="pic" style="display: none;"><img src="images/xz.png"></p>
                <b>XL</b><br>&gt;21KG
            </div>
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">姓名</div>
        <div>
            <input class="fl bd3" size="50" name="sbt_name">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">電話</div>
        <div>
            <input class="fl bd3" size="50" name="sbt_phone">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">地址</div>
        <div>
            <input class="fl bd3" size="50" name="sbt_address">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">上門日期</div>
        <div>
            <input class="fl bd3" size="50" name="sbt_time">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">備註</div>
        <div>
            <input class="fl bd3" size="50" name="sbt_remark">
        </div>
    </div>
    <div class="input1">
        <div class="cap">&nbsp;</div>
        <div class="fl">
            <input type="hidden" name="sbt_tid" value="<?= $member_arr['id']; ?>">

            <dl class="select select_book" data-select>
                <dt>
                    <span class="fl"><?= $member_arr['name']; ?></span>
                    <b></b>
                </dt>
                <dd>
                    <ul>
                    <?php
                        $getdata = $my_db->selectRow('*', 'member', array('valid' => 1, 'type' => 10), array('method' => 'DESC', 'field' => 'date'));
                        while ($result = mysql_fetch_array($getdata)) {
                    ?>
                        <li><a href="javascript:;" name-member="<?= $result['id']; ?>"><?= $result['name']; ?></a></li>
                    <?php } ?>
                    </ul>
                </dd>
            </dl>
        </div>
    </div>
    <div class="cl1"></div>
    <div class="cl1">
        <input type="submit" value="提交" class="input2">
    </div>

    <p class="two2"><img src="images/two.png"></p>

</form><br>

<script type="text/javascript">
$("[data-action='service'] div").click(function(){
    $("[name='sbt_service']").val($(this).attr("data-value"));
    $(this).parent().find(".pic").hide();
    $(this).find(".pic").show();
});

$("[data-action='pet'] div").click(function(){
    $("[name='sbt_pet[]'][type='hidden']").val($(this).attr("data-value"));
    $(this).parent().find(".pic").hide();
    $(this).find(".pic").show();
});

$("[data-action='size'] div").click(function(){
    $("[name='sbt_size']").val($(this).attr("data-value"));
    $(this).parent().find(".pic").hide();
    $(this).find(".pic").show();
});

</script>

<?php require('foot.php'); ?>