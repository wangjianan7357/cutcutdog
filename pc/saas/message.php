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

    if ($my_db->saveRow('message', $submit)) {
        callback(array('error' => 0));

    } else {
        callback(array('error' => 4));
    }

} else if ($_REQUEST['action'] == 'list') {
    $member = array();
    $getdata = $my_db->selectRow('*', 'member');
    while ($result = mysql_fetch_array($getdata)) {
        $member[$result['id']] = $result;
    }

    $list = array();
    $getdata = $my_db->selectRow('*', 'message', $_POST['where']);
    while ($result = mysql_fetch_array($getdata)) {
        $result['member'] = $member[$result['mid']];
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

}