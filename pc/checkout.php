<?php

require_once('include/fun_web.php');
require_once('include/cls_paypal.php');
require_once('include/common.php');
require_once('include/initial.php');

$_POST['sbt_status'] = 1;
$_POST['sbt_read'] = 0;
$_POST['sbt_fields']['products'] = array();

$_POST['sbt_id'] = $my_db->selectMax('order') + 1;
$_POST['sbt_id'] = ($_POST['sbt_id'] < 1000000) ? ($_POST['sbt_id'] + 1000000) : $_POST['sbt_id'];

$amount = 0;
foreach ($_POST['sbt_product'] as $key => $val) {
    $product = array();
    $product['name'] = $product_list[$key]['name'];
    $product['id'] = $product_list[$key]['id'];
    $product['sale'] = $product_list[$key]['sale'];
    $product['number'] = $val;

    $_POST['sbt_fields']['products'][] = $product;
    $amount += $product_list[$key]['sale'] * $val;
}

$_POST['sbt_amount'] = $amount;
$_POST['sbt_fields'] = json_encode($_POST['sbt_fields']);
$_POST['sbt_number'] = $_POST['sbt_id'] . time();

$submit = array();
$submit_arr = array('id', 'number', 'mid', 'email', 'name', 'phone', 'address', 'amount', 'fields', 'status', 'read');

for($i = 0; $i < count($submit_arr); $i++){
    $submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
}

$my_db->saveRow('order', $submit);

if ($amount > 0) {
    $payment = new Paypal();
    $payment->getOrderData($_POST['sbt_number']);
    echo $payment->createForm();
}
