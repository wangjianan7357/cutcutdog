<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

$cart = new Cart();

if ($_REQUEST['step']) {
    loginTimeout();
    $cur_member = $my_db->fetchOne('member', array('id' => $_COOKIE['cookie']['memberid']));

    $cur_address = array();
    $getdata = $my_db->selectRow('*', 'address', array('mid' => $cur_member['id']), array('field' => 'is_default', 'method' => 'desc'));
    while ($result = mysql_fetch_array($getdata)) {
        $cur_address = $result;
        break;
    }
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
        }
        else {
            $msg = $err;
            $err = '';
            $href = 'cart.php?step=1';
        }

    } else if ($_REQUEST['step'] == 3) {
        $cur_order = $my_db->fetchOne('order', array('number' => $_POST['number']));
        $cur_order['fields'] = json_decode($cur_order['fields'], true);
        $cur_order['fields']['shipping'] = $_POST['sbt_shipping'];

        if ($cur_order['status'] > 1) {
            $err = '訂單已完成支付';
        }

        if (!$err) {
            mysql_query('BEGIN');

            $done = 1;
            $done &= $my_db->saveRow('order', array('status' => 1, 'fields' => json_encode($cur_order['fields'])), array('number' => $_POST['number']));

            if ($done) {
                if ($cur_order['amount'] > 0) {
                    if ($_POST['sbt_payment'] == 2) {
                        mysql_query("COMMIT");
                        mysql_query("END");

                        require_once('include/cls_paypal.php');

                        $payment = new Paypal();
                        $payment->getOrderData($cur_order['id']);
                        echo $payment->createForm();
                        exit;

                    } else {
                        mysql_query("ROLLBACK");
                        mysql_query("END");

                        // 请选择支付方式
                        exit;
                    }
                }
            }
            else {
                mysql_query("ROLLBACK");
                mysql_query("END");

                // 订单保存失败
                //$err = '提交失败';
            }
        } else {
            $msg = $err;
            $err = '';
            $href = 'cart.php?number=' . $_POST['number'];
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
</div>

    <ul class="mui-table-view" id="list-view" style=" background-color:#fff;">
        <?php
            $code = '';
            $amonut = 0;

            if ($goods = $cart->getCart()) {
                foreach ($goods as $value) {
                    $amonut += $value['sale'] * $value['num'];
        ?>

        <li class="cart_list"> 
            <a class="pic inblock" href="<?=L_PATH . $value['path'];?>-p.html" target="_blank"> 
                <img src="<?=PIC_PRODUCT_M . $value['src'];?>"> 
            </a> 
            <div class="tit inblock item_name_block inblock_top"> 
                <a href="<?=L_PATH . $value['path'];?>-p.html" target="_blank">
                    <?= $value['name']; ?><br>
                </a> 
            </div> 

            <span class="price inblock"><?= $value['sale']; ?></span> 
            <div class="num inblock">
                <input name="product[<?=$value['id'];?>][num]" type="text" value="<?=$value['num'];?>" class="inblock" style="border: 1px solid #c8c7cc;"> 
            </div> 

            <span class="inblock count"> <strong><?= $value['sale'] * $value['num'];?></strong> </span> 
            <!--<span class="weight inblock"> <span>0.37kg</span> </span> -->
           
            <span class="cart3_btn inblock" style=" padding-left:80px;"> 
                <a href="cart.php?delete=<?=$value['id'];?>" style=" color:#F00;">刪除</a> 
            </span>
        </li>  
        <?php } } ?>

    </ul>

<div class="mui-table-view-cell mui-media" id="checkout" style="display: none;">
    <a href="checkout.html" data-action="openwindow">
        <div class="jq-cart-co" style=" float: left; line-height:35px ;">總價錢：$<span data-info="total">0.00</span></div>
        <div class="zh-good-go" style="background-color:#4ebdf4 ; float: right; text-align: center; margin-bottom: 0px;">確認下單</div>
    </a>    
</div>

<?php require('foot.php'); ?>