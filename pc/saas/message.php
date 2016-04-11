<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'send') {
    if (!$my_db->existRow('member', array('name' => $_POST['name'], 'id' => $_POST['id']))) {
        callback(array('error' => 6));
    }

    $submit = array(
        'content' => strip_tags(urldecode($_POST['content'])),
        'type' => intval($_POST['type']),
        'mid' => intval($_POST['id'])
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