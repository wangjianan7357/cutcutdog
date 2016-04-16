<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    
    if (is_array($_POST['where'])) {
        $getdata = $my_db->selectRow('*', 'catalog', $_POST['where']);
        while ($result = mysql_fetch_array($getdata)) {
            $result['list'] = array();

            if ($_POST['where']['type'] == 4) {
                // 加入部分内容
                $getdata1 = $my_db->selectRow('*', 'info', array('cid' => $result['id'] . ',', 'valid' => 1), array('field' => 'date', 'method' => 'DESC'), 3);
                while ($result1 = mysql_fetch_array($getdata1)) {
                    $result1['summary'] = cutString(strip_tags($result1['desp']), 30);
                    $result['list'][] = $result1;
                }
            }
            
            $list[$result['id']] = $result;
        }
    } else {
        callback(array('error' => 5));
    }

    callback(array('error' => 0, 'list' => $list));

} else if ($_REQUEST['action'] == 'detail') {
    $list = array();
    $category = $my_db->fetchOne('catalog', $_POST['where']);

    if (!empty($category)) {
        $getdata = $my_db->selectRow('*', $cms_cata_type[$category['type']]['db'], array('`cid` LIKE "%,' . $category['id'] . '," OR `cid` = "' . $category['id'] . ',"'));
        while ($result = mysql_fetch_array($getdata)) {
            $result['summary'] = cutString(strip_tags($result['desp']), 30);
            $list[$result['id']] = $result;
        }
    }

    callback(array('error' => 0, 'detail' => $category, 'list' => $list));
}