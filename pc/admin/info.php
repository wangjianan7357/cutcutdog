<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();

$catalog_type = (int)($_GET['type'] ? $_GET['type'] : ($_POST['sbt_type'] ? $_POST['sbt_type'] : 1));

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('info', array('id' => $_GET['num']));
	} else {
		$outcome['valid'] = 1;
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('info', $power_id)) warning('權限不足');
		else delSelectedData('info', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('info', $power_id)) warning('權限不足');

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名稱'));

		if(!$err) {
			$submit_arr = initSubmitColumns('info', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('info') + 1);

			mysql_query('BEGIN');

			$done = 1;
			$submit = array();
			for($i = 0; $i < count($submit_arr); $i++){
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			$done &= $my_db->saveRow('info', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

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

				$msg[0] = '提交失敗';
				$msg[1] = 'fail';
			}
		}
	}

} else {
	$q_url = queryPart('date', 'desc');

	$where = '1';

	class FieldFun {
		function __construct($namespace = 1){
			$this->namespace = $namespace;
		}

		function __call($method, $str) {
			global $cms_flow_type;

			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					
			}
		}
	}
	
	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'name' => '名稱',
			array('__edit', 'edit', array('power' => 'info', 'method' => array('detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'info',
		)
	);
	
}

require('templates/head.html');
require('templates/info.html');
require('templates/foot.html');