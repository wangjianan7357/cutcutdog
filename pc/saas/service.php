<?php
require('../include/common.php');
require('../include/fun_saas.php');

if ($_REQUEST['action'] == 'list') {
    $list = array();
    $where = $_POST['where'];
    $where['valid'] = 1;

    $getdata = $my_db->selectRow('*', 'service', $where);
    while ($result = mysql_fetch_array($getdata)) {
        $list[$result['id']] = $result;
    }

    callback(array('error' => 0, 'list' => $list));

} else if ($_REQUEST['action'] == 'detail') {
    if (is_array($_POST['where'])) {
        $service = $my_db->fetchOne('service', array('path' => $_POST['where']['path']));

        $comments = array();
        $getdata = $my_db->selectRow('*', 'message', array('atype' => $_POST['where']['type'], 'aid' => $service['id'], 'valid' => 1), array('field' => 'date', 'method' => 'DESC'));
        while ($result = mysql_fetch_array($getdata)) {
            $comments[] = $result;
        }

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

        $service['comments'] = $comments;

    } else {
        callback(array('error' => 4));
    }

    callback(array('error' => 0, 'detail' => $service));

}