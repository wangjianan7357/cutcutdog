<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('order', array('id' => $_GET['num']));
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('order', $power_id)) warning('權限不足');
		else delSelectedData('order', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('order', $power_id)) warning('權限不足');

		if(!$err){
			$submit_arr = initSubmitColumns('order', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('order') + 1);
			$_POST['sbt_valid'] = ($_POST['sbt_valid'] ? 1 : 0);

			$submit = array();
			$submit_lan = array();
			for($i = 0; $i < count($submit_arr); $i++){
				if(!isset($_POST['sbt_' . $submit_arr[$i]])) continue;
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
				if($submit_arr[$i] != 'name' || !$_GET['num']) $submit_lan += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			mysql_query('BEGIN');

			$done = 1;
			$done &= $my_db->saveRow('order', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				instructLog($cms_admin_power['order'][$power_id] . $_POST['sbt_name'], ($poser_id == 1 ? 'add' : 'edt'));
				$msg[0] = $cms_admin_power['order'][$power_id] . '成功';
				$msg[1] = 'success';
				$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");
				$msg[0] = $cms_admin_power['member'][$power_id] . '失败';
				$msg[1] = 'fail';
			}
		}
	}

} else {
	$q_url = queryPart('date', 'desc');

	$where = $_GET['type'] ? '`type` = "' . addslashes($_GET['type']) . '"' : '1';
	
	class FieldFun {
		function __construct($namespace = 1){
			$this->namespace = $namespace;
		}

		function __call($method, $str) {
			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					
			}
		}
	}

	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'type' => array('類型', 'select', array($cms_member_type)),
			'number' => array('訂單號', 'text'), 
			'email' => '郵箱', 
			'mobile' => '電話', 
			'date' => array('加入時間', 'read'), 
			'amount' => array('總價', 'read', array(new FieldFun(2))), 
			'valid' => array('有效', 'checkbox'),
			array('__edit', 'edit', array('power' => 'order', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'order'
		)
	);	
}

require('templates/head.html');
require('templates/order.html');
require('templates/foot.html');