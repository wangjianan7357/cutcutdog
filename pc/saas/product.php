<?php
require('../include/common.php');
require('../include/fun_saas.php');

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

            $product['comments'] = $comments;

            $product['total'] = array();
            $product['total']['comments'] = count($comments);

            $product['likes'] = $my_db->existRow('likes', array('atype' => $_POST['where']['type'], 'aid' => $product['id'], 'valid' => 1));
        }

    } else {
        callback(array('error' => 4));
    }

    callback(array('error' => 0, 'detail' => $product));
}