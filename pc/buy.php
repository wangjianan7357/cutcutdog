<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

require('head.php');
?>

<div class="title1">
    <div class="txt">買賣平台</div>
</div>

<div class="buy_catalog">
    <div class="frame mgt25">
        <a href="used-list.php?id=29"><img src="images/buy-catalog1.jpg"></a>
        <?php
            $i = 1;
            $getdata = $my_db->selectRow('*', 'product', array('cid' => '29,'), array('method' => 'DESC', 'field' => 'date'), '0,3');
            while ($result = mysql_fetch_array($getdata)) {
                echo '<p class="pic pic' . $i . '"><img src="' . PIC_PRODUCT_M . $result['src'] . '" width="113" height="133"></p>';
                $i ++;
            }
        ?>
    </div>
    <div class="frame mgt25">
        <a href="used-list.php?id=30"><img src="images/buy-catalog2.jpg"></a>
        <?php
            $i = 1;
            $getdata = $my_db->selectRow('*', 'product', array('cid' => '30,'), array('method' => 'DESC', 'field' => 'date'), '0,3');
            while ($result = mysql_fetch_array($getdata)) {
                echo '<p class="pic pic' . $i . '"><img src="' . PIC_PRODUCT_M . $result['src'] . '" width="113" height="133"></p>';
                $i ++;
            }
        ?>
    </div>
    <div class="frame mgt25">
        <a href="used-list.php?id=31"><img src="images/buy-catalog3.jpg"></a>
        <?php
            $i = 1;
            $getdata = $my_db->selectRow('*', 'product', array('cid' => '31,'), array('method' => 'DESC', 'field' => 'date'), '0,3');
            while ($result = mysql_fetch_array($getdata)) {
                echo '<p class="pic pic' . $i . '"><img src="' . PIC_PRODUCT_M . $result['src'] . '" width="113" height="133"></p>';
                $i ++;
            }
        ?>
    </div>
    <div class="frame mgt25">
        <a href="used-list.php?id=32"><img src="images/buy-catalog4.jpg"></a>
        <?php
            $i = 1;
            $getdata = $my_db->selectRow('*', 'product', array('cid' => '32,'), array('method' => 'DESC', 'field' => 'date'), '0,3');
            while ($result = mysql_fetch_array($getdata)) {
                echo '<p class="pic pic' . $i . '"><img src="' . PIC_PRODUCT_M . $result['src'] . '" width="113" height="133"></p>';
                $i ++;
            }
        ?>
    </div>
</div>

<?php require('foot.php'); ?>