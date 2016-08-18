<?php

require_once('include/fun_web.php');
require_once('include/common.php');
require_once('include/initial.php');

if ($_REQUEST['action'] == 'add_cart') {
    $product = array();
    $product['id'] = $_REQUEST['id'];
    $product['num'] = $_REQUEST['num'];

    if ($cur_data = $my_db->fetchOne('product', array('id' => intval($_REQUEST['id']), 'valid' => 1))) {
        $cart = new Cart();
        $cart->addCart($product);
    }

} else if ($_REQUEST['action'] == 'info-list') {

    $data = array();

    if ($_GET['cid']) {
        $where = '`cid` = ' . intval($_GET['cid']);

    } else {
        $getdata = $my_db->selectRow('id, parent', 'catalog', array('type' => 3));
        while ($result = mysql_fetch_array($getdata)) {
            $catalog[] = $result['parent'] . $result['id'] . ',';
        }

        $where = '`cid` IN ("' . implode('", "', $catalog) . '")';
    }

    if ($_GET['service']) {
        $where .= ' AND `type` = ' . intval($_GET['service']);
    }

    if ($_GET['name']) {
        $where .= ' AND `name` LIKE "%' . addslashes($_GET['name']) . '%"';
    }

    $where .= ' AND `valid` = 1';

    $data['list'] = array();
    $data['total'] = ceil($my_db->existRow('info', array($where)) / 2);

    $getdata = $my_db->selectRow('*', 'info', array($where), array('method' => 'DESC', 'field' => 'date'), (intval($_GET['pages']) - 1) * 2 . ',2');
    while ($result = mysql_fetch_array($getdata)) {
        $result['src'] = PIC_INFO_M . $result['src'];
        $data['list'][] = $result;
    }

    echo json_encode($data);
}