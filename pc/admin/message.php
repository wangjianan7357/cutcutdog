<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();
$default = 1;

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_POST['del'] == 'true'){
		if(!adminPower('message', $power_id)) warning('权限不足');
		else delSelectedData('message', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('message', $power_id)) warning('权限不足');
		
		if(!$err) {
			$submit = array(
				'valid' => $_POST['sbt_valid'] ? 1 : 0,
				'sid' => $_SESSION['admin_id'],
				'stype' => $_SESSION['admin_type'],
				'rid' => 0,
				'rtype' => 0,
				'subject' => $_POST['sbt_subject'],
				'content' => $_POST['sbt_content'],
				'valid' => 1,
				'read' => 0,
				'type' => $_POST['sbt_type'],
			);

			$done = 1;
			$done &= $my_db->saveRow('message', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

			if($done){
				$msg[0] = '提交成功';
				$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				$msg[0] = '提交失败';
				$msg[1] = 'fail';
			}
		}
	}

	$outcome = $my_db->fetchOne('message', array('id' => $_GET['num']));

	if ($outcome['stype'] == 1) {
		$outcome['sender'] = $my_db->fetchOne('admin', array('id' => $outcome['sid']));

	} else if ($outcome['stype'] == 2) {
		$outcome['sender'] = $my_db->fetchOne('teacher', array('id' => $outcome['sid']));
	}

	if(!$message_arr['read']) $my_db->saveRow('message', array('read' => 1), array('id' => $_GET['num']));

	require('templates/head.html');

} else {
	require('templates/head.html');
	$q_url = queryPart('date', 'desc');

	$where = '(`rtype` = ' . $_SESSION['admin_type'] . ' AND `rid` = ' . $_SESSION['admin_id'] . ') OR `rtype` = 0';

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
			//'read' => array('已读', 'read', array(new FieldFun())), 
			'subject' => '主题', 
			'date' => '日期', 
			//'valid' => array('有效', 'checkbox'),
			array('__edit', 'edit', array('power' => 'message', 'method' => array('detail' => '1|2')))
		),
		array(
			'where' => $where,
			'table' => 'message',
			'operate' => ($_SESSION['admin_type'] == 1 && $_SESSION['admin_id'] == 1) ? array('edt', 'delete') : ''
		)
	);
}

require('templates/message.html');
require('templates/foot.html');