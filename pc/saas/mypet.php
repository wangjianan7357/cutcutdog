<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'send') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $submit = array(
        'mid' => $_POST['id'],
        'src' => '',
        'size' => $_POST['params']['size'],
        'name' => $_POST['params']['name'],
        'number' => $_POST['params']['number'],
        'remark' => $_POST['params']['remark'],
        'valid' => 1,
    );

    if($my_db->saveRow('mypet', $submit)){
        callback(array('error' => 0));

    } else {
        callback(array('error' => 4));
    }

} else if ($_REQUEST['action'] == 'list') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $list = array();
    $getdata = $my_db->selectRow('*', 'mypet', $_POST['where']);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

}