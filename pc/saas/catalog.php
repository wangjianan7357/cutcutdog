<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $getdata = $my_db->selectRow('*', 'catalog', $_POST['where']);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

} else if ($_REQUEST['action'] == 'detail') {
    $list = array();
    $category = $my_db->fetchOne('catalog', $_POST['where']);

    if (!empty($category)) {
        $getdata = $my_db->selectRow('*', $cms_cata_type[$category['type']]['db'], array('`cid` LIKE "%,' . $category['id'] . '," OR `cid` = "' . $category['id'] . ',"'));
        while ($result = mysql_fetch_array($getdata)) {
            $list[$result['id']] = $result;
        }
    }

    callback(array('error' => 0, 'detail' => $category, 'list' => $list));
}