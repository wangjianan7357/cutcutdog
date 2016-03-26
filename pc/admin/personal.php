<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();

$department = array();
$getdata = $my_db->selectRow('id, name', 'catalog', array('`type` = 1 AND `parent` = 0'), array('valid' => 1));
while ($res = mysql_fetch_array($getdata)) {
    $department[$res['id']] = $res['name'];
}

$classes = array();
$getdata = $my_db->selectRow('id, name', 'catalog', array('`type` = 2'), array('valid' => 1));
while ($res = mysql_fetch_array($getdata)) {
    $classes[$res['id']] = $res['name'];
}

if($_SESSION['admin_type'] == 2){
	$table = 'teacher';

} else if($_SESSION['admin_type'] == 3){
	$table = 'student';
}

$outcome = $my_db->fetchOne($table, array('id' => $_SESSION['admin_id']));

if($_POST['save'] == 'true'){
	$chk_post = new ChkRequest('sbt_');

	if($_SESSION['admin_type'] == 1){
		$chk_post->chkEmpty(array('name' => '教职工名称', 'mobile' => '手机'));
		$chk_post->chkFormat(array('mobile' => '手机'), 'define:mobile');
        $chk_post->chkExist(array('mobile' => '手机'), array('name' => 'teacher'));

	} else if($_SESSION['admin_type'] == 3){
		$chk_post->chkEmpty(array('name' => '名称', 'phone' => '联系方式'));
	}

	if(!$err) {
		$submit_arr = initSubmitColumns($table, $_SESSION['admin_id']);

		mysql_query('BEGIN');

		$done = 1;
		$submit = array();
		for($i = 0; $i < count($submit_arr); $i++){
			$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
		}

		$done &= $my_db->saveRow($table, $submit, array('id' => $_SESSION['admin_id']));

		if($done){
			mysql_query("COMMIT");
			mysql_query("END");

			$msg[0] = '提交成功';
			$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
			header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
		}
		else {
			mysql_query("ROLLBACK");
			mysql_query("END");

			$msg[0] = '提交失败';
			$msg[1] = 'fail';
		}
	}
}

require('templates/head.html');
require('templates/personal.html');
require('templates/foot.html');