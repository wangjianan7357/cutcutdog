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

require('head.php');
?>

<div class="title1">
    <div class="txt">上門服務</div>
    <div class="arrow_right"></div>
    <div class="txt">預約</div>
</div>

<br>

<div class="service_booking cl1">
    <div class="cl1 input1">
        <div class="cap">服務類型</div>
        <div class="">
            <p class="fl">
                <img src="images/service-icon-1.png" class="vc"> &nbsp;
                寵物洗澡 &nbsp;&nbsp;&nbsp;
            </p>
            <p class="fl">
                <img src="images/service-icon-2.png" class="vc"> &nbsp;
                上門美容 &nbsp;&nbsp;&nbsp;
            </p>
            <p class="fl">
                <img src="images/service-icon-3.png" class="vc"> &nbsp;
                寵物傳心 &nbsp;&nbsp;&nbsp;
            </p>
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">寵物類型</div>
        <div>
            <p class="fl">
                <img src="images/service-icon-4.png" class="vc"> &nbsp;&nbsp;&nbsp;
            </p>
            <p class="fl">
                <img src="images/service-icon-5.png" class="vc"> &nbsp;&nbsp;&nbsp;
            </p>
            <input class="fl bd3" size="30" placeholder="品種請填寫">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">寵物SIZE</div>
        <div></div>
    </div>
    <div class="cl1 input1">
        <div class="cap">姓名</div>
        <div>
            <input class="fl bd3" size="50" name="">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">電話</div>
        <div>
            <input class="fl bd3" size="50" name="">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">地址</div>
        <div>
            <input class="fl bd3" size="50" name="">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">上門日期</div>
        <div>
            <input class="fl bd3" size="50" name="">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap">備註</div>
        <div>
            <input class="fl bd3" size="50" name="">
        </div>
    </div>
    <div class="cl1 input1">
        <div class="cap"></div>
        <div>
            <input class="fl bd3" size="50" name="">
        </div>
    </div>

   
        <p><img src="images/dog.png"></p>


</div><br>

<?php require('foot.php'); ?>