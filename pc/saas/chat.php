<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'send') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $submit = array(
        'content' => strip_tags(urldecode($_POST['content'])),
        'mid' => intval($_POST['id']),
        'tid' => intval($_POST['tid']),
        'valid' => 1,
    );

    if ($my_db->saveRow('chat', $submit)) {
        callback(array('error' => 0));

    } else {
        callback(array('error' => 4));
    }

} else if ($_REQUEST['action'] == 'list') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $list = array();
    $member = array();

    $getdata = $my_db->selectRow('mid, content, date, tid', 'chat', array('(`tid` = ' . intval($_POST['tid']) . ' AND `mid` = ' . intval($_POST['id']) . ') OR (`tid` = ' . intval($_POST['id']) . ' AND `mid` = ' . intval($_POST['tid']) . ')'), array('field' => 'date', 'method' => 'ASC'));
    while ($result = mysql_fetch_array($getdata)) {
        if ($result['mid'] == $_POST['id']) {
            $result['me'] = true;
        }
        $list[] = $result;

        if (empty($member) && $result['mid'] != $_POST['id']) {
            $member = $my_db->fetchOne('member', array('id' => $result['mid']));
        }
    }

    $my_db->saveRow('chat', array('read' => 1), array('read' => 0, 'mid' => intval($_POST['tid']), 'tid' => $_POST['id']));

    callback(array('error' => 0, 'list' => $list, 'member' => $member));

}