<?php

require_once('../include/fun_web.php');
require_once('../include/cls_paypal.php');
require_once('../include/common.php');
require_once('../include/initial.php');

$_POST['sbt_amount'] = sprintf("%01.2f", $_POST['sbt_amount']);
$_POST['sbt_status'] = 0;
$_POST['sbt_read'] = 0;
$_POST['sbt_logistics'] = 0;
$_POST['sbt_voucher'] = '';
$_POST['sbt_fields'] = json_encode(array('products' => $goods, 'remark' => $_POST['sbt_remark']));

$submit = array();
$submit_arr = array('id', 'number', 'mid', 'email', 'gender', 'firstname', 'lastname', 'mobile', 'country', 'city', 'postcode', 'address', 'amount', 'logistics', 'voucher', 'fields', 'status', 'read');

for($i = 0; $i < count($submit_arr); $i++){
    $submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
}

$my_db->saveRow('order', $submit);
$cur_order = $my_db->fetchOne('order', array('id' => $_POST['sbt_id']));
$cur_order['fields'] = json_decode($cur_order['fields'], true);


$done = 1;

$done &= $my_db->saveRow('order', array('status' => 1), array('number' => $_POST['number']));

if ($done) {
    $cur_order = $my_db->fetchOne('order', array('number' => $_POST['number']));

    if ($cur_order['amount'] > 0) {
        $payment = new Paypal();
        $payment->getOrderData($cur_order['id']);
        echo $payment->createForm();
    }

}