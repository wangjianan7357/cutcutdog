<?php

require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'login') {

} else if ($_REQUEST['action'] == 'register') {    
    $submit = array(
        'name' => trim(urldecode($_REQUEST['account'])),
        'pass' => md5(trim(urldecode($_REQUEST['password']))),
        'email' => trim(urldecode($_REQUEST['email'])),
    );

    if ($my_db->saveRow('member', $submit)) {
        echo callback(array('error' => 0));
    } else {
        echo callback(array('error' => 1));
    }
}
