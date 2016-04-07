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
		$outcome = $my_db->fetchOne('likes', array('id' => $_GET['num']));
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('likes', $power_id)) warning('權限不足');
		else delSelectedData('likes', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('likes', $power_id)) warning('權限不足');
		
		if(!$err) {
			$submit = array(
				'valid' => $_POST['sbt_valid'] ? 1 : 0
			);

			$done = 1;
			$done &= $my_db->saveRow('likes', $submit, array('id' => $_GET['num']));

			if ($_POST['sbt_reply']['id'] || $_POST['sbt_reply']['content']) {
				$submit = array(
					'valid' => 1,
					'tid' => $_GET['num'],
					'content' => $_POST['sbt_reply']['content'],
					'type' => $outcome['type'],
					'mid' => $_SESSION['admin_id']
				);

				$done &= $my_db->saveRow('likes', $submit, ($_POST['sbt_reply']['id'] ? array('id' => $_POST['sbt_reply']['id']) : ''));
			}

			if($done){
				$msg[0] = '提交成功';
				$msg[1] = 'success';
				$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				$msg[0] = '提交失敗';
				$msg[1] = 'fail';
			}
		}
	}

	if(!$outcome['read']) {
		$my_db->saveRow('likes', array('read' => 1), array('id' => $_GET['num']));
	}

} else {
	$q_url = queryPart('date', 'desc');

	$where = '1';

	class FieldFun {
		function __construct($namespace = 1){
			$this->namespace = $namespace;
		}

		function __call($method, $str) {
			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					return '<font color=' . ($str[0] ? '"green">是' : '"red">否') . '</font>';
			}
		}
	}

	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'date' => '日期', 
			'valid' => array('有效', 'checkbox'),
		),
		array(
			'where' => $where,
			'table' => 'likes'
		)
	);
}

require('templates/head.html');
require('templates/likes.html');
require('templates/foot.html');