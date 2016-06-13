<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $service = array();
    
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

        $service = $cms_service_type;

    } else {
        callback(array('error' => 5));
    }

    callback(array('error' => 0, 'list' => $list, 'service' => $service));

} else if ($_REQUEST['action'] == 'detail') {
    $list = array();
    $category = array();

    if ($_POST['where']['path']) {
        $category = $my_db->fetchOne('catalog', array('type' => $_POST['where']['type'], 'path' => $_POST['where']['path']));
    }

    if (!empty($category)) {
        $member = array();
        $getdata = $my_db->selectRow('*', 'member');
        while ($result = mysql_fetch_array($getdata)) {
            $member[$result['id']] = $result;
        }

        $getdata = $my_db->selectRow('*', $cms_cata_type[$category['type']]['db'], array('`cid` LIKE "%,' . $category['id'] . '," OR `cid` = "' . $category['id'] . ',"'), array('field' => 'date', 'method' => 'desc'));
        while ($result = mysql_fetch_array($getdata)) {
            if (!isset($member[$result['mid']])) {
                $member[$result['mid']] = array('name' => '');
            }

            $result['summary'] = cutString(strip_tags($result['desp']), 30);
            $result['member'] = $member[$result['mid']];
            $result['date'] = substr($result['date'], 0, 10);
            $list[] = $result;
        }
    } else if ($_POST['where']['search']) {
        $category['name'] = $_POST['where']['search'];

        $getdata = $my_db->selectRow('*', 'info', array('`name` LIKE "%' . addslashes($category['name']) . '%" OR `desp` LIKE "%' . addslashes($category['name']) . '%"'), array('field' => 'date', 'method' => 'desc'));
        while ($result = mysql_fetch_array($getdata)) {
            if (!isset($member[$result['mid']])) {
                $member[$result['mid']] = array('name' => '');
            }

            $result['summary'] = cutString(strip_tags($result['desp']), 30);
            $result['member'] = $member[$result['mid']];
            $result['date'] = substr($result['date'], 0, 10);
            $list[] = $result;
        }
    }

    callback(array('error' => 0, 'detail' => $category, 'list' => $list));
}