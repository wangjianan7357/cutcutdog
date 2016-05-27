<?php

$con_posi = 'member';

require_once('include/fun_web.php');
require_once('include/cls_paypal.php');
require_once('include/common.php');
require_once('include/initial.php');

$log = file_get_contents('admin/backup/' . $child_pre . '/pay_return.txt');
file_put_contents('admin/backup/' . $child_pre . '/pay_return.txt', $log . "\n\n" . date('Y-m-d H:i:s') . ' ' . json_encode(array('REQUEST' => $_REQUEST, 'SERVER' => $_SERVER)));

$payment = new Paypal();

/*
if (!$payment->notify()) {
	exit;
}
*/
