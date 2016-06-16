<?php
require('../include/common.php');
require('../include/fun_saas.php');
require('../include/cls_graphic.php');
require('../include/cls_emailer.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $where = $_POST['where'];
    $catalog = array();
    $limit = '';

    if ($_POST['type'] == 3) {
        $where = array();

        if ($_POST['where']['name'] && $_POST['where']['name']['like'] != '%%') {
            $where['name'] = $_POST['where']['name'];
        }

        if ($_POST['where']['cid']) {
            $where['cid'] = array('like' => '%,' . $_POST['where']['cid'] . ',');
        } else {
            $getdata = $my_db->selectRow('id, parent', 'catalog', array('type' => $_POST['type']));
            while ($result = mysql_fetch_array($getdata)) {
                $catalog[] = $result['parent'] . $result['id'] . ',';
            }

            $where['cid'] = array('in' => '("' . implode('", "', $catalog) . '")');
        }

        if (intval($_POST['where']['service'])) {
            /*
            $pids = array();
            $getdata = $my_db->selectRow('pid', 'property_content', array('sort' => 1, 'vid' => intval($_POST['where']['service'])));
            while ($result = mysql_fetch_array($getdata)) {
                $pids[] = $result['pid'];
            }

            if (!empty($pids)) {
                $where['id'] = array('in' => '(' . implode(',', $pids) . ')');
            } else {
                callback(array('error' => 0, 'list' => $list));
            }
            */

            $where['type'] = intval($_POST['where']['service']);
        }

        $_POST['page'] = isset($_POST['page']) ? intval($_POST['page']) : 1;

        $limit = ($_POST['page'] - 1) * 4 . ',' . 4;

        //$getdata = $my_db->selectRow('id', 'catalog', array('type' => intval($_POST['type'])));

    } else if ($_POST['type'] == 5) {
        $member = array();
        $getdata = $my_db->selectRow('src, name, id', 'member');
        while ($result = mysql_fetch_array($getdata)) {
            $member[$result['id']] = $result;
        }

        $_POST['page'] = isset($_POST['page']) ? intval($_POST['page']) : 1;

        $limit = ($_POST['page'] - 1) * 5 . ',' . 5;
    }

    $where['valid'] = 1;

    $getdata = $my_db->selectRow('*', 'info', $where, array('field' => 'date', 'method' => 'desc'), $limit);
    while ($result = mysql_fetch_array($getdata)) {
        if (isset($_POST['type']) && $_POST['type'] == 5) {
            $result['likes'] = $my_db->existRow('likes', array('atype' => $_POST['type'], 'aid' => $result['id'], 'valid' => 1));
            $result['member'] = isset($member[$result['mid']]) ? $member[$result['mid']] : array();

            $comments = array();
            $getdata1 = $my_db->selectRow('content, mid', 'message', array('atype' => $_POST['type'], 'aid' => $result['id'], 'valid' => 1), array('field' => 'date', 'method' => 'DESC'), '0,2');
            while ($result1 = mysql_fetch_array($getdata1)) {
                $result1['member'] = $member[$result1['mid']];
                $comments[] = $result1;
            }

            $result['comment'] = $comments;

        }

        $list[] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

} else if ($_REQUEST['action'] == 'detail') {
    if (is_array($_POST['where'])) {
        $info = $my_db->fetchOne('info', array('path' => $_POST['where']['path']));

        $cid = explode(',', trim($info['cid'], ','));

        if ($_POST['where']['type'] == 4) {
            $order = array('field' => 'date', 'method' => 'ASC');
        } else {
            $order = array('field' => 'date', 'method' => 'DESC');
        }

        $comments = array();
        $getdata = $my_db->selectRow('*', 'message', array('atype' => $_POST['where']['type'], 'aid' => $info['id'], 'valid' => 1), $order);
        while ($result = mysql_fetch_array($getdata)) {
            $result['likes'] = $my_db->existRow('likes', array('aid' => $result['id'], 'valid' => 1));
            $comments[] = $result;
        }

        $info['catalog'] = $my_db->fetchOne('catalog', array('id' => $cid[count($cid) - 1]));

        $info['member'] = $my_db->fetchOne('member', array('id' => $info['mid']));

        if (!empty($comments)) {
            // 添加会员名
            $member = array();
            $getdata = $my_db->selectRow('id, name, src', 'member');
            while ($result = mysql_fetch_array($getdata)) {
                $member[$result['id']] = $result;
            }

            foreach ($comments as &$value) {
                $value['member'] = $member[$value['mid']];
            }
        }

        $info['comments'] = $comments;

        $info['total'] = array();
        $info['total']['comments'] = count($comments);

        $info['likes'] = $my_db->existRow('likes', array('atype' => $_POST['where']['type'], 'aid' => $info['id'], 'valid' => 1));
        $info['liked'] = 0;

        if ($_POST['where']['member_id']){
            $info['liked'] = $my_db->existRow('likes', array('atype' => $_POST['where']['type'], 'aid' => $info['id'], 'valid' => 1, 'mid' => $_POST['where']['member_id']));
        }

        $my_db->saveRow('info', array('read' => 1), array('id' => $info['id']));

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

        $imgarr = array();
        $imgop = new Graphic($filepath);
        $imgarr['width'] = $imgop->getWidth();
        $imgarr['height'] = $imgop->getHeight();

        if($imgarr['width'] > 640 || $imgarr['height'] > 640){
            $imgop = new Graphic($filepath);
            $imgop->resizeImage($filepath, 640, 640);
        }
    }

    $chk_post = new ChkRequest('sbt_');

    $cid = explode(',', trim($_POST['sbt_cid']));
    $catalog = $my_db->fetchOne('catalog', array('id' => $cid[0]));

    $submit = array(
        'src' => $filename,
        'mid' => $_POST['id'],
        'name' => $_POST['sbt_name'],
        'tel' => isset($_POST['sbt_tel']) ? $_POST['sbt_tel'] : '',
        'address' => isset($_POST['sbt_address']) ? $_POST['sbt_address'] : '',
        'desp' => isset($_POST['sbt_desp']) ? $_POST['sbt_desp'] : '',
        'cid' => rtrim($_POST['sbt_cid'], ',') . ',',
        'valid' => $catalog['type'] == 3 ? 0 : 1,
        'path' => $chk_post->traFromName('name', array('name' => 'info', 'field' => 'path')),
        'type' => isset($_POST['sbt_service']) ? intval($_POST['sbt_service']) : 0,
    );

    if ($my_db->saveRow('info', $submit)) {

        if ($catalog['type'] == 3) {
            $mail = new Emailer($con_mail_set);
            $mail->resetFields(array('name' => '標題', 'desp' => '描述', 'tel' => '電話', 'address' => '地址'));
            $mail->content($submit, '店鋪提交');
            $mail->send();
        }

        callback(array('error' => 0));
    } else {
        callback(array('error' => 4));
    }

}