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

<form class="service_booking cl1">
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
        <div class="size">
            <p class="fl"><b>XS</b><br>&lt;3KG</p>
            <p class="fl"><b>S</b><br>3-7KG</p>
            <p class="fl"><b>M</b><br>8-12KG</p>
            <p class="fl"><b>L</b><br>13-20KG</p>
            <p class="fl"><b>XL</b><br>&gt;21KG</p>
        </div>
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
    <div class="input1">
        <div class="cap">&nbsp;</div>
        <div class="fl">
            <dl class="select select_book" data-select>
                <dt>
                    <span class="fl">没有指定美容師</span>
                    <b></b>
                </dt>
                <dd>
                    <ul>
                    <?php foreach ($cms_service_type as $key => $value) { ?>
                        <li><a href="javascript:;" name-service="<?= $key; ?>"><?= $value; ?></a></li>
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

<?php require('foot.php'); ?>