<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

$message_type = (int)($_GET['type'] ? $_GET['type'] : ($_POST['sbt_type'] ? $_POST['sbt_type'] : 1));

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('message', array('id' => $_GET['num']));
		$outcome['member'] = $my_db->fetchOne('member', array('id' => $outcome['mid']));
		$outcome['target'] = $my_db->fetchOne($cms_cata_type[$outcome['atype']]['db'], array('id' => $outcome['aid']));
		$outcome['reply'] = $my_db->fetchOne('message', array('tid' => $outcome['id']));
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('message', $power_id)) warning('權限不足');
		else delSelectedData('message', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('message', $power_id)) warning('權限不足');
		
		if(!$err) {
			$submit = array(
				'valid' => $_POST['sbt_valid'] ? 1 : 0
			);

			$done = 1;
			$done &= $my_db->saveRow('message', $submit, array('id' => $_GET['num']));

			if ($_POST['sbt_reply']['id'] || $_POST['sbt_reply']['content']) {
				$submit = array(
					'valid' => 1,
					'tid' => $_GET['num'],
					'content' => $_POST['sbt_reply']['content'],
					'type' => $outcome['type'],
					'mid' => $_SESSION['admin_id']
				);

				$done &= $my_db->saveRow('message', $submit, ($_POST['sbt_reply']['id'] ? array('id' => $_POST['sbt_reply']['id']) : ''));
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
		$my_db->saveRow('message', array('read' => 1), array('id' => $_GET['num']));
	}

} else {
	$q_url = queryPart('date', 'desc');

	$where = '`tid` = 0 AND `type` = ' . $message_type;

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
			'content' => '内容', 
			'date' => '日期', 
			'valid' => array('有效', 'checkbox'),
			'read' => array('已读', 'read', array(new FieldFun())), 
			array('__edit', 'edit', array('power' => 'message', 'method' => array('detail' => '1|2')))
		),
		array(
			'where' => $where,
			'table' => 'message'
		)
	);
}

require('templates/head.html');
require('templates/message.html');
require('templates/foot.html');