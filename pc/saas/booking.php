<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'send') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $submit = array(
        'content' => strip_tags(urldecode($_POST['content'])),
        'type' => 1,
        'mid' => intval($_POST['id']),
        'atype' => intval($_POST['atype']),
        'aid' => intval($_POST['aid']),
        'valid' => 1,
    );

    if($my_db->saveRow('booking', $submit)){
        $mail = new Emailer($con_mail_set);
        $mail->content($content);

        if($mail->send()) {
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