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
        'phone' => trim(urldecode($_REQUEST['phone'])),
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
    $submit = array(
        'name' => urldecode($_REQUEST['name']),
        'id' => $_REQUEST['id'],
    );

    $member = $my_db->fetchOne('member', $submit);

    if (!empty($member)) {
        callback(array('error' => 0, 'member' => $member));
    } else {
        callback(array('error' => 5));
    }

} else if ($_REQUEST['action'] == 'update') {
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

