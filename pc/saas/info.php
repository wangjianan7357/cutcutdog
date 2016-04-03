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
    $result = $my_db->fetchOne('info', $_POST['where']);

    callback(array('error' => 0, 'detail' => $result));
}