<?php

require_once('include/fun_web.php');
require_once('include/cls_paypal.php');
require_once('include/common.php');
require_once('include/initial.php');

$_POST['sbt_status'] = 1;
$_POST['sbt_read'] = 0;
$_POST['sbt_fields']['products'] = array();

$account = 0;
foreach ($_POST['product'] as $key => $val) {
    $_POST['sbt_fields']['products'][] = $product_list[$key];
    $account += $product_list[$key]['sale'] * $val;
}

$submit = array();
$submit_arr = array('id', 'number', 'mid', 'email', 'name', 'phone', 'address', 'amount', 'fields', 'status', 'read');

for($i = 0; $i < count($submit_arr); $i++){
    $submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
}

$my_db->saveRow('order', $submit);

if ($cur_order['amount'] > 0) {
    $payment = new Paypal();
    $payment->getOrderData($cur_order['id']);
    //echo $payment->createForm();
}
