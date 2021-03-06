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

    if (!$_POST['where']['search'] || strtolower($_POST['where']['search']) == 'null') {
        unset($_POST['where']['search']);
        $category = $my_db->fetchOne('catalog', $_POST['where']);
    }

    $member = array();
    $getdata = $my_db->selectRow('*', 'member');
    while ($result = mysql_fetch_array($getdata)) {
        $member[$result['id']] = $result;
    }

    function infoModify($result) {
        global $member;
        global $my_db;

        if (!isset($member[$result['mid']])) {
            $member[$result['mid']] = array('name' => '');
        }

        $result['summary'] = cutString(strip_tags($result['desp']), 30);
        $result['member'] = $member[$result['mid']];
        $result['date'] = substr($result['date'], 0, 10);

        if ($_POST['where']['type'] == 4) {
            $result['total'] = $my_db->existRow('message', array('atype' => $_POST['where']['type'], 'aid' => $result['id'], 'valid' => 1));

            $latest = $my_db->fetchOne('message', array('atype' => $_POST['where']['type'], 'aid' => $result['id'], 'valid' => 1), array('field' => 'date', 'method' => 'DESC'), '0,1');

            if (!isset($member[$latest['mid']])) {
                $member[$latest['mid']] = array('name' => '');
            }

            $result['latest'] = $member[$latest['mid']];
        }

        return $result;
    }

    if (!empty($category)) {
        $getdata = $my_db->selectRow('*', $cms_cata_type[$category['type']]['db'], array('`cid` LIKE "%,' . $category['id'] . '," OR `cid` = "' . $category['id'] . ',"'), array('field' => 'date', 'method' => 'desc'));
        while ($result = mysql_fetch_array($getdata)) {
            $list[] = infoModify($result);
        }
    } else if ($_POST['where']['search']) {
        $category['name'] = $_POST['where']['search'];

        $getdata = $my_db->selectRow('*', 'info', array('`name` LIKE "%' . addslashes($category['name']) . '%"'), array('field' => 'date', 'method' => 'desc'));
        while ($result = mysql_fetch_array($getdata)) {
            $list[] = infoModify($result);
        }
    }

    callback(array('error' => 0, 'detail' => $category, 'list' => $list));
}