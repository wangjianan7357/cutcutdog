<?php
require('../include/common.php');
require('../include/fun_saas.php');
require('../include/cls_graphic.php');

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
        'salt' => '',
        'src' => '',
        'sex' => '',
        'email' => trim(urldecode($_REQUEST['email'])),
        'address' => trim(urldecode($_REQUEST['address'])),
        'phone' => trim(urldecode($_REQUEST['phone'])),
        'type' => 1,
        'fields' => '',
        'desp' => ''
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

    if ($_POST['need']) {
        $need = explode(',', $_POST['need']);

        foreach ($need as $val) {
            if ($val == 'mypet') {
                $member['mypet'] = array();

                $getdata = $my_db->selectRow('id, name', 'mypet', array('mid' => $_REQUEST['id']));
                while ($result = mysql_fetch_array($getdata)) {
                    $member['mypet'][$result['id']] = $result;
                }
            } else if ($val == 'statistic') {
                $statistic = array();
                $statistic['photo'] = $my_db->existRow('info', array('mid' => $_REQUEST['id'], 'cid' => '15,'));
                $statistic['discuss'] = $my_db->existRow('info', array('mid = ' . intval($_REQUEST['id']) . ' AND (cid = "13," OR cid = "14," OR cid = "16," OR cid = "17," OR cid = "18," OR cid = "19,")'));
                $statistic['likes'] = $my_db->existRow('likes', array('mid' => $_REQUEST['id']));

                $member['statistic'] = $statistic;
            }
        }
    }

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

    $filename = '';

    if ($_FILES['src']['tmp_name']) {
        preg_match('/(\.[\w]{3,4})$/', $_FILES['src']['name'], $match);

        $filename = systemConfig('member_img_path') . $con_pic['pre']['member'] . '_pic_' . $_REQUEST['id'] . '_' . rand(0, 10) . $match[1];
        if (file_exists('../' . $filename)) {
            unlink('../' . $filename);
        }

        move_uploaded_file($_FILES['src']['tmp_name'], '../' . $filename);

        $imgarr = array();
        $imgop = new Graphic('../' . $filename);
        $imgarr['width'] = $imgop->getWidth();
        $imgarr['height'] = $imgop->getHeight();

        if($imgarr['width'] > 500 || $imgarr['height'] > 500){
            $imgop = new Graphic('../' . $filename);
            $imgop->resizeImage('../' . $filename, 500, 500);
        }
    }

    $submit = array(
        'name' => $_POST['sbt_name'],
        'phone' => $_POST['sbt_phone'],
        'email' => $_POST['sbt_email'],
        'address' => $_POST['sbt_address'],
    );

    if ($filename) {
        $submit['src'] = $filename;
    }

    if ($my_db->saveRow('member', $submit, $where)) {
        callback(array('error' => 0));
    } else {
        callback(array('error' => 5));
    }

} else if ($_REQUEST['action'] == 'list') {
    $where['valid'] = 1;

    $list = array();
    $getdata = $my_db->selectRow('id, src, name, phone, email', 'member', $where);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

} else if ($_REQUEST['action'] == 'detail') {
    if (is_array($_POST['where'])) {
        $where = $_POST['where'];
    }

    $where['valid'] = 1;
    $where['type'] = 10;

    $service = array();
    $getdata = $my_db->selectRow('*', 'service', array('valid' => 1));
    while ($result = mysql_fetch_array($getdata)) {
        $service[$result['id']] = $result;
    }

    $member = $my_db->fetchOne('member', $where);
    unset($member['pass']);
    unset($member['salt']);

    $member['fields'] = json_decode($member['fields'], true);

    $member['service'] = array();

    $getdata = $my_db->selectRow('vid', 'property_content', array('pid' => $member['id'], 'sort' => 3));
    while($result = mysql_fetch_array($getdata)) {
        $member['service'][] = $service[$result['vid']];
    }

    callback(array('error' => 0, 'member' => $member));

} else if ($_REQUEST['action'] == 'my-photo') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $list = array();
    $getdata = $my_db->selectRow('id, src', 'info', array('cid' => '13,', 'mid' => $_REQUEST['id']), array('field' => 'date', 'method' => 'desc'));
    while ($result = mysql_fetch_array($getdata)) {
        $list[] = $result;
    }

    callback(array('error' => 0, 'list' => $list, 'member' => array('id' => $_POST['id'])));
}

