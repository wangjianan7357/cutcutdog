<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'list') {
    $total = 0;
    $list = array();
    $where = 'valid = 1';

    $tmp = array(0);
    $request = array();
    foreach ($_POST['list'] as $val) {
        if (is_numeric($val['id'])) {
            $tmp[] = $val['id'];
            $request[intval($val['id'])] = $val['number'];
        }
    }

    $getdata = $my_db->selectRow('*', 'product', $where . ' AND id IN (' . implode(',', $tmp) . ')');
    while ($result = mysql_fetch_array($getdata)) {
        if ($request[$result['id']]) {
            // 存在数量
            $result['number'] = $request[$result['id']];
            $list[$result['id']] = $result;

            $total += $result['number'] * $result['sale'];
        }
    }

    callback(array('error' => 0, 'list' => $list, 'total' => sprintf('%.2f', $total)));

}