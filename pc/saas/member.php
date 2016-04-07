<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'login') {
    $submit = array(
        'name' => trim(urldecode($_REQUEST['account'])),
        'pass' => md5(trim(urldecode($_REQUEST['password']))),
    );

    $member = $my_db->fetchOne('member', $submit);

    if (!empty($member)) {
        callback(array('error' => 0, 'member' => $member));
    } else {
        callback(array('error' => 3));
    }

} else if ($_REQUEST['action'] == 'register') {    
    $submit = array(
        'name' => trim(urldecode($_REQUEST['account'])),
        'pass' => md5(trim(urldecode($_REQUEST['password']))),
        'email' => trim(urldecode($_REQUEST['email'])),
        'address' => trim(urldecode($_REQUEST['address'])),
        'type' => 1
    );

    if ($my_db->existRow('member', array('name' => $submit['name']))) {
        callback(array('error' => 2));
    }

    if ($my_db->saveRow('member', $submit)) {
        callback(array('error' => 0));
    } else {
        callback(array('error' => 1));
    }

} else if ($_REQUEST['action'] == 'profile') {  
    print_r($_FILES);
}
