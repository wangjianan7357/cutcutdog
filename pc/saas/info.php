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
    if (is_array($_POST['where'])) {
        $info = $my_db->fetchOne('info', array('path' => $_POST['where']['path']));

        $comments = array();
        $getdata = $my_db->selectRow('*', 'message', array('atype' => $_POST['where']['type'], 'aid' => $info['id'], 'valid' => 1), array('field' => 'date', 'method' => 'DESC'));
        while ($result = mysql_fetch_array($getdata)) {
            $comments[] = $result;
        }

        if (!empty($comments)) {
            // 添加会员名
            $member = array();
            $getdata = $my_db->selectRow('*', 'member');
            while ($result = mysql_fetch_array($getdata)) {
                $temp = array();
                $temp['name'] = $result['name'];

                $member[$result['id']] = $temp;
            }

            foreach ($comments as &$value) {
                $value['member'] = $member[$value['mid']];
            }
        }

        $info['comments'] = $comments;

        $info['likes'] = $my_db->existRow('likes', array('atype' => $_POST['where']['type'], 'aid' => $info['id'], 'valid' => 1));

    } else {
        callback(array('error' => 4));
    }

    callback(array('error' => 0, 'detail' => $info));

} else if ($_REQUEST['action'] == 'insert') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $filename = '';
    if ($_FILES['src']['tmp_name']) {
        preg_match('/(\.[\w]{3,4})$/', $_FILES['src']['name'], $match);
        $filename = substr(time(), -8, 8) . rand(10, 99) . strtolower($match[1]);
        $filepath = '../' . systemConfig('info_img_path') . $con_pic['pre']['info'] . $con_pic['suf']['mid'] . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        move_uploaded_file($_FILES['src']['tmp_name'], $filepath);
    }

    $chk_post = new ChkRequest('sbt_');

    $submit = array(
        'src' => $filename,
        'mid' => $_POST['id'],
        'name' => $_POST['sbt_name'],
        'desp' => $_POST['sbt_desp'],
        'cid' => $_POST['sbt_cid'] . ',',
        'valid' => 1,
        'path' => $chk_post->traFromName('name', array('name' => 'info', 'field' => 'path'))
    );

    if ($my_db->saveRow('info', $submit)) {
        callback(array('error' => 0));
    } else {
        callback(array('error' => 4));
    }

}