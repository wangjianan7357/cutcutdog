<?php
require('../include/common.php');
require('../include/fun_admin.php');

if($_REQUEST['method'] == 'clearPageCache') {
    $dh = opendir('../cache/');
    while($item = readdir($dh)){
        if(is_file('../cache/' . $item)) unlink('../cache/' . $item);
    }

    echo 1;

} else if ($_REQUEST['method'] == 'plan') {
    $plan = array();
    $getdata = $my_db->selectRow('sid', 'plan', array('cid' => $_REQUEST['did']));
    while ($result = mysql_fetch_array($getdata)) {
        $plan[] = $result['sid'];
    }

    $course = array();
    $getdata = $my_db->selectRow('id, name, did', 'course', array('CONCAT(",", `did`, ",") LIKE "%' . $_REQUEST['did'] . '%"'));
    while ($result = mysql_fetch_array($getdata)) {
        if (!in_array($result['id'], $plan)) {
            $course[$result['id']] = $result['name'];
        }
    }

    $teacher = array();
    $teacher[1] = array();
    $teacher[2] = array();
    $getdata = $my_db->selectRow('id, name, type', 'teacher', array('cid' => $_REQUEST['did']));
    while ($result = mysql_fetch_array($getdata)) {
        $teacher[$result['type']][$result['id']] = $result['name'];
    }

    echo json_encode(array('course' => $course, 'teacher' => $teacher[1]));

} else if ($_REQUEST['method'] == 'classes') {
    $classes = array();
    
    $getdata = $my_db->selectRow('id, name', 'catalog', array('`type` = 2 AND `parent` = "' . $_REQUEST['did'] . '"'), array('valid' => 1));
    while ($res = mysql_fetch_array($getdata)) {
        $classes[$res['id']] = $res['name'];
    }

    echo json_encode($classes);

}

