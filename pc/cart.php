<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$cart = new Cart();

if ($_REQUEST['step']) {
    loginTimeout();
    $cur_member = $my_db->fetchOne('member', array('id' => $_COOKIE['cookie']['memberid']));

    /*
    $cur_address = array();
    $getdata = $my_db->selectRow('*', 'address', array('mid' => $cur_member['id']), array('field' => 'is_default', 'method' => 'desc'));
    while ($result = mysql_fetch_array($getdata)) {
        $cur_address = $result;
        break;
    }
    */
} 
else if ($_REQUEST['delete']) {
    $cart->delCart($_REQUEST['delete']);
    header('Location: cart.php');
    exit;
}

if (!$_REQUEST['step']) {
    $step = 1;
}
else {
    $err = '';

    if ($_REQUEST['step'] == 1) {
        if ($_POST['product']) {            
            foreach($_POST['product'] as $key => $value) {
                $cart->addCart(array('id' => $key, 'num' => intval($value['num'])));
            }
        }
        
        $goods = $cart->getCart();
    }
    else if ($_REQUEST['step'] == 2) {
        $_POST['sbt_amount'] = 0;

        $goods = $cart->getCart();
        foreach ($goods as $key => $value) {
            $_POST['sbt_amount'] += $value['sale'] * $value['num'];
        }

        $chk_post = new ChkRequest('sbt_');
        $chk_post->chkEmpty(array('name' => '姓名', 'phone' => '聯繫電話', 'address' => '地址'));

        if (!$err) {
            $_POST['sbt_id'] = $my_db->selectMax('order') + 1;
            $_POST['sbt_id'] = ($_POST['sbt_id'] < 1000000) ? ($_POST['sbt_id'] + 1000000) : $_POST['sbt_id'];

            $_POST['sbt_mid'] = $cur_member['id'];
            $_POST['sbt_number'] = $_POST['sbt_id'] . time();

            /*
            $exist_address = $my_db->fetchOne('address', array('mid' => $cur_member['id']), array('field' => 'is_default', 'method' => 'desc'));
            if (empty($exist_address)) {
                $adr_submit = array();
                $adr_submit_arr = array('name', 'phone', 'address', 'code', 'mid');

                for($i = 0; $i < count($adr_submit_arr); $i++){
                    if (isset($_POST['sbt_' . $adr_submit_arr[$i]])) {
                        $adr_submit += array($adr_submit_arr[$i] => $_POST['sbt_' . $adr_submit_arr[$i]]);
                    }
                }

                $adr_submit += array('is_default' => 1);
                $my_db->saveRow('address', $adr_submit);
            }
            */

            $_POST['sbt_amount'] = sprintf("%01.2f", $_POST['sbt_amount']);
            $_POST['sbt_status'] = 0;
            $_POST['sbt_read'] = 0;
            $_POST['sbt_fields'] = json_encode(array('products' => $goods));

            $submit = array();
            $submit_arr = array('id', 'mid', 'number', 'email', 'name', 'phone', 'address', 'amount', 'fields', 'status', 'read');

            for($i = 0; $i < count($submit_arr); $i++){
                $submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
            }

            $my_db->saveRow('order', $submit);
            $cur_order = $my_db->fetchOne('order', array('id' => $_POST['sbt_id']));
            $cur_order['fields'] = json_decode($cur_order['fields'], true);

            require_once('include/cls_paypal.php');

            $payment = new Paypal();
            $payment->getOrderData($cur_order['id']);
            echo $payment->createForm();
            exit;
        }
        else {
            $msg = $err;
            $err = '';
            $href = 'cart.php?step=1';
        }
    }

    $step = intval($_REQUEST['step']);

    if ($_POST['method'] != 'refresh' && !$err) {
        $step ++;
    }
}

// 直接访问订单
if ($_GET['number']) {
    loginTimeout();
    $cur_member = $my_db->fetchOne('member', array('id' => $_COOKIE['cookie']['memberid']));

    $cur_order = $my_db->fetchOne('order', array('number' => $_GET['number']));
    $cur_order['fields'] = json_decode($cur_order['fields'], true);

    if ($cur_order['mid'] != $cur_member['id']) {
        header('Location: cart.php');
        exit;
    } else {
        if ($cur_order['status'] < 2) {
            $step = 3;
        }
    }
}

require('head.php');
?>

<div class="title1">
    <div class="txt">購物車</div>
    <?php if ($step == 2) { ?>
    <div class="arrow_right"></div>
    <div class="txt">客户联系方式</div>
    <?php } ?>
</div>

<div class="cart_frame mgt10">
    <form method="post" action="cart.php" id="cart" <?=$step == 2 ? 'target="_blank"' : '';?>>
        <input type="hidden" name="step" value="<?=$step;?>" />

        <?php if ($step == 1) { ?>
        <table class="cart_list">
            <?php
                $code = '';
                $amount = 0;

                if ($goods = $cart->getCart()) {
                    foreach ($goods as $value) {
                        $amount += $value['sale'] * $value['num'];
            ?>
            <tr> 
                <td>
                    <a class="pic" href="<?=L_PATH . $value['path'];?>-p.html" target="_blank"> 
                        <img src="<?=PIC_PRODUCT_M . $value['src'];?>" width="150"> 
                    </a> 
                </td>
                <td class="cap"><?= $value['name']; ?></td> 
                <td class="num">
                    <input name="product[<?=$value['id'];?>][num]" type="text" value="<?=$value['num'];?>" size="4"> 
                </td> 
                <td class="count">$<?= sprintf("%01.2f", $value['sale'] * $value['num']);?></td> 
                <td class="btn"> 
                    <a href="cart.php?delete=<?=$value['id'];?>">刪除</a> 
                </td>
            </tr>  
            <?php } } ?>

        </table>

        <div class="cl1"> 
            <div class="fr"> <a href="javascript:;" class="btn_next" onclick="cart.submit()">下一步</a> </div> 
            <div class="fr">
                <span class="all">總價 $<?=sprintf("%01.2f", $amount);?></span>
            </div> 
            <div class="fr"> <a href="javascript:;" class="btn_refresh" onclick="submitFormAddAttr('cart', '({method:\'refresh\'})')">刷新購物車</a></div>
        </div>

        <?php } else if ($step == 2) { ?>

        <div class="cl1">
            <div class="form_item">
                <label>姓名：</label>
                <input type="text" name="sbt_name" class="input_txt" value="">
            </div>
            <div class="form_item">
                <label>聯繫電話：</label>
                <input type="text" name="sbt_phone" class="input_txt" value="">
            </div>
            <div class="form_item">
                <label>住所地址：</label>
                <textarea style="width: 250px;" name="sbt_address"></textarea>
            </div>
        </div>
        
        <div class="cl1"> 
            <div class="fr"><a href="javascript:;" class="btn_next" onclick="cart.submit()">確認付款</a> </div> 
        </div> 

        <?php } ?>

    </form>
</div>

<?php require('foot.php'); ?>