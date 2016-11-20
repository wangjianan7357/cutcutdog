<?php

require_once('include/fun_web.php');
require_once('include/cls_paypal.php');
require_once('include/common.php');
require_once('include/initial.php');

$_POST['sbt_mid'] = urldecode($_REQUEST['sbt_mid']);
$_POST['sbt_name'] = urldecode($_REQUEST['sbt_name']);
$_POST['sbt_phone'] = urldecode($_REQUEST['sbt_phone']);
$_POST['sbt_address'] = urldecode($_REQUEST['sbt_address']);
$_POST['sbt_product'] = $_REQUEST['sbt_product'];

$_POST['sbt_status'] = 1;
$_POST['sbt_read'] = 0;
$_POST['sbt_fields']['products'] = array();

$_POST['sbt_id'] = $my_db->selectMax('order') + 1;
$_POST['sbt_id'] = ($_POST['sbt_id'] < 1000000) ? ($_POST['sbt_id'] + 1000000) : $_POST['sbt_id'];

$amount = 0;
foreach ($_POST['sbt_product'] as $key => $val) {
    $result = $my_db->fetchOne('product', array('id' => $key));

    $product = array();
    $product['name'] = $result['name'];
    $product['id'] = $result['id'];
    $product['sale'] = $result['sale'];
    $product['number'] = $val;

    $_POST['sbt_fields']['products'][] = $product;
    $amount += $result['sale'] * $val;
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

?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi ">  
</head>
<body>
<?php
if ($amount > 0) {
    $payment = new Paypal();
    $payment->getOrderData($_POST['sbt_id']);
    echo $payment->createForm();
}
?>
</body>
</html>
