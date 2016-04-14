<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $getdata = $my_db->selectRow('*', 'info', $_POST['where']);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

} else if ($_REQUEST['action'] == 'detail') {
    $result = $my_db->fetchOne('info', $_POST['where']);

    callback(array('error' => 0, 'detail' => $result));

} else if ($_REQUEST['action'] == 'insert') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $where = array(
        'name' => urldecode($_REQUEST['name']),
        'id' => $_REQUEST['id'],
    );

    if ($_FILES['src']['tmp_name']) {
        preg_match('/(\.[\w]{3,4})$/', $_FILES['src']['name'], $match);

        $filename = '_pic_' . $_REQUEST['id'] . '_' . rand(0, 10) . $match[1];
        if (file_exists('../' . systemConfig('member_img_path') . $con_pic['pre']['member'] . $filename)) {
            unlink('../' . systemConfig('member_img_path') . $con_pic['pre']['member'] . $filename);
        }

        move_uploaded_file($_FILES['src']['tmp_name'], '../' . systemConfig('member_img_path') . $con_pic['pre']['member'] . $filename);

        $member = $my_db->saveRow('member', array('src' => systemConfig('member_img_path') . $con_pic['pre']['member'] . $filename), $where);
    }

    $member = $my_db->fetchOne('member', $where);

    if (!empty($member)) {
        callback(array('error' => 0, 'member' => $member));
    } else {
        callback(array('error' => 5));
    }

}