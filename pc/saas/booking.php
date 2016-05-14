<?php
require('../include/common.php');
require('../include/fun_saas.php');
require('../include/cls_emailer.php');

if ($_REQUEST['action'] == 'send') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $submit = array(
        'mid' => $_POST['id'],
        'tid' => $_POST['params']['tid'],
        'service' => $_POST['params']['service'],
        'pet' => $_POST['params']['pet'],
        'size' => $_POST['params']['size'],
        'name' => $_POST['params']['name'],
        'phone' => $_POST['params']['phone'],
        'address' => $_POST['params']['address'],
        'time' => $_POST['params']['time'],
        'remark' => $_POST['params']['remark'],
    );

    if($my_db->saveRow('booking', $submit)){
        $technician = $my_db->fetchOne('member', array('id' => $_POST['params']['tid']));
        $submit['technician'] = $technician['name'];

        $mail = new Emailer($con_mail_set);
        $mail->setFields(array('technician' => '美容师'));
        $mail->content($submit);

        if($mail->send()) {
            callback(array('error' => 0));
        } else {
            callback(array('error' => 0));
        }

    } else {
        callback(array('error' => 4));
    }

} else if ($_REQUEST['action'] == 'list') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $list = array();
    $getdata = $my_db->selectRow('*', 'booking', $_POST['where']);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

}