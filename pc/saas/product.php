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

    } else {
        callback(array('error' => 4));
    }

    callback(array('error' => 0, 'detail' => $product));
}