<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'insert') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $submit = array(
        'mid' => intval($_POST['id']),
        'atype' => intval($_POST['atype']),
        'aid' => intval($_POST['aid']),
        'valid' => 1,
    );

    $where = $submit;
    unset($where['valid']);

    if ($my_db->existRow('likes', $where) || $my_db->saveRow('likes', $submit)) {
        callback(array('error' => 0));

    } else {
        callback(array('error' => 4));
    }

} else if ($_REQUEST['action'] == 'delete') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $where = array(
        'mid' => intval($_POST['id']),
        'atype' => intval($_POST['atype']),
        'aid' => intval($_POST['aid']),
    );

    if ($my_db->deleteRow('likes', $where)) {
        callback(array('error' => 0));

    } else {
        callback(array('error' => 4));
    }
}