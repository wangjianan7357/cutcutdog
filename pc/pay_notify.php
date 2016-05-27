<?php

$con_posi = 'member';

require_once('include/fun_web.php');
require_once('include/cls_paypal.php');
require_once('include/common.php');

$log = file_get_contents('admin/backup/' . $child_pre . '/pay_notify.txt');
file_put_contents('admin/backup/' . $child_pre . '/pay_notify.txt', $log . "\n\n" . date('Y-m-d H:i:s') . ' ' . json_encode(array('REQUEST' => $_REQUEST, 'SERVER' => $_SERVER)));

$cart = new Paypal();
$cart->notify();
