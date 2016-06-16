<?php
require('../include/common.php');
require('../include/fun_saas.php');
require('../include/cls_graphic.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $where = $_POST['where'];

    $where['valid'] = 1;

    if (!isset($where['cid'])) {
        callback(array('error' => 5));
    }

    $getdata = $my_db->selectRow('*', 'product', $where);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
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

        } else {
            $where = 'sort = 7 AND pid = ' . $product['id'];

            $product['picture'] = array();
            $product['picture'][] = systemConfig('product_img_path') . $con_pic['pre']['product'] . $con_pic['suf']['mid'] . $product['src'];

            $getdata = $my_db->selectRow('content', 'property_content', array($where));
            while($result = mysql_fetch_array($getdata)) {
                $product['picture'][] = systemConfig('property_img_path') . $con_pic['pre']['property'] . $con_pic['suf']['big'] . $result['content'];
            }
        }

    } else {
        callback(array('error' => 4));
    }

    callback(array('error' => 0, 'detail' => $product));

} else if ($_REQUEST['action'] == 'insert') {
    checkMember(array('name' => urldecode($_POST['name']), 'id' => $_POST['id']));

    $filename = '';
    if ($_FILES['src']['tmp_name']) {
        preg_match('/(\.[\w]{3,4})$/', $_FILES['src']['name'], $match);
        $filename = substr(time(), -8, 8) . rand(10, 99) . strtolower($match[1]);
        $filepath = '../' . systemConfig('product_img_path') . $con_pic['pre']['product'] . $con_pic['suf']['mid'] . $filename;

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

    $submit = array(
        'src' => $filename,
        'mid' => $_POST['id'],
        'name' => $_POST['sbt_name'],
        'sale' => $_POST['sbt_sale'],
        'desp' => isset($_POST['sbt_desp']) ? $_POST['sbt_desp'] : '',
        'cid' => $_POST['sbt_cid'] . ',',
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