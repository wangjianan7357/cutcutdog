<?php
require('../include/common.php');
require('../include/fun_saas.php');
require('../include/cls_graphic.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $where = $_POST['where'];
    $limit = '';

    if (!isset($where['cid'])) {
        callback(array('error' => 5));
    }

    if (isset($_POST['type']) && $_POST['type'] == 8) {
        $member = array();
        $getdata = $my_db->selectRow('src, name, id', 'member');
        while ($result = mysql_fetch_array($getdata)) {
            $member[$result['id']] = $result;
        }

        $where = array();
        $where['cid'] = array('in' => '("' . implode('", "', explode('|', $_POST['where']['cid'])) . '")');
        $limit = ($_POST['page'] - 1) * 4 . ',' . 4;
    }

    $where['valid'] = 1;

    $getdata = $my_db->selectRow('*', 'product', $where, array('field' => 'date', 'method' => 'desc'), $limit);
    while ($result = mysql_fetch_array($getdata)) {
        if (isset($_POST['type']) && $_POST['type'] == 8) {
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
    if (is_array($_POST['where']) && isset($_POST['where']['id'])) {
        $product = $my_db->fetchOne('product', array('id' => $_POST['where']['id']));

        $cid = explode(',', trim($product['cid'], ','));

        $product['catalog'] = $my_db->fetchOne('catalog', array('id' => $cid[count($cid) - 1]));

        if ($_POST['where']['type'] == 8) {
            $comments = array();
            $getdata = $my_db->selectRow('*', 'message', array('atype' => $_POST['where']['type'], 'aid' => $product['id'], 'valid' => 1), array('field' => 'date', 'method' => 'DESC'));
            while ($result = mysql_fetch_array($getdata)) {
                $comments[] = $result;
            }

            $product['member'] = $my_db->fetchOne('member', array('id' => $product['mid']));

            if (!empty($comments)) {
                // 添加会员名
                $member = array();
                $getdata = $my_db->selectRow('id, name', 'member');
                while ($result = mysql_fetch_array($getdata)) {
                    $member[$result['id']] = $result;
                }

                foreach ($comments as &$value) {
                    $value['member'] = $member[$value['mid']];
                }
            }

            $product['comments'] = $comments;

            $product['total'] = array();
            $product['total']['comments'] = count($comments);

            $product['likes'] = $my_db->existRow('likes', array('atype' => $_POST['where']['type'], 'aid' => $product['id'], 'valid' => 1));

        }

        $where = 'sort = ' . $_POST['where']['type'] . ' AND pid = ' . $product['id'];

        $product['picture'] = array();
        $product['picture'][] = systemConfig('product_img_path') . $con_pic['pre']['product'] . $con_pic['suf']['mid'] . $product['src'];

        $getdata = $my_db->selectRow('content', 'property_content', array($where));
        while($result = mysql_fetch_array($getdata)) {
            $product['picture'][] = systemConfig('property_img_path') . $con_pic['pre']['property'] . $con_pic['suf']['big'] . $result['content'];
        }

    } else {
        callback(array('error' => 4));
    }

    callback(array('error' => 0, 'detail' => $product));

} else if ($_REQUEST['action'] == 'insert') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $i = 0;
    $product_src = '';
    $property_arr = array();
    $product_id = $my_db->selectMax('product') + 1;

    foreach ($_FILES as $key => $value) {
        preg_match('/(\.[\w]{3,4})$/', $value['name'], $match);
        $filename = substr(time(), -8, 8) . rand(10, 99) . strtolower($match[1]);

        if (!$i) {
            $filepath = '../' . systemConfig('product_img_path') . $con_pic['pre']['product'] . $con_pic['suf']['mid'] . $filename;
            $product_src = $filename;
        } else {
            $filepath = '../' . systemConfig('property_img_path') . $con_pic['pre']['property'] . $con_pic['suf']['big'] . $filename;
            $my_db->saveRow('property_content', array('content' => $filename, 'vid' => 0, 'pid' => $product_id, 'sort' => 8));
        }

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        move_uploaded_file($value['tmp_name'], $filepath);

        $imgarr = array();
        $imgop = new Graphic($filepath);
        $imgarr['width'] = $imgop->getWidth();
        $imgarr['height'] = $imgop->getHeight();

        if($imgarr['width'] > 640 || $imgarr['height'] > 640){
            $imgop = new Graphic($filepath);
            $imgop->resizeImage($filepath, 640, 640);
        }

        $i ++;
    }

    $chk_post = new ChkRequest('sbt_');

    $submit = array(
        'id' => $product_id,
        'src' => $product_src,
        'mid' => $_POST['id'],
        'name' => $_POST['sbt_name'],
        'sale' => $_POST['sbt_sale'],
        'desp' => isset($_POST['sbt_desp']) ? $_POST['sbt_desp'] : '',
        'cid' => '33,',
        'valid' => 1,
        'path' => $chk_post->traFromName('name', array('name' => 'product', 'field' => 'path')),
        'fields' => ''
    );

    if ($my_db->saveRow('product', $submit)) {
        callback(array('error' => 0));
    } else {
        callback(array('error' => 4));
    }

}