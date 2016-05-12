<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

$member_src = '../' . systemConfig('member_img_path') . $con_pic['pre']['member'];

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	$service = array();
	$getdata = $my_db->selectRow('*', 'service');
	while($result = mysql_fetch_array($getdata)) {
		$service[$result['id']] = $result;
	}

	if($_GET['num']){
		$outcome = $my_db->fetchOne('member', array('id' => $_GET['num']));
		$outcome['fields'] = json_decode($outcome['fields'], true);

		$outcome['service'] = array();

		$getdata = $my_db->selectRow('vid', 'property_content', array('pid' => $_GET['num'], 'sort' => 3));
		while($result = mysql_fetch_array($getdata)) {
			$outcome['service'][] = $result['vid'];
		}

	} else {
		$outcome = array();
		$outcome['valid'] = true;
		$outcome['service'] = array();
		$outcome['fields'] = array();
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('member', $power_id)) warning('權限不足');
		else delSelectedData('member', array('id' => $power_id), $member_src);
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('member', $power_id)) warning('權限不足');

		$_POST['sbt_name'] = trim($_POST['sbt_name']);

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名稱', 'phone' => '電話'));

		$_POST['sbt_src'] = $chk_post->chkImage('src');
		
		if ($outcome['src']) {
			if ($_POST['sbt_src']) {
				preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);
				$_POST['sbt_src'] = preg_replace('/\.[\w]{3,4}$/', '', $outcome['src']) . $match[1];
				$outcome['src'] = $_POST['sbt_src'];
			}
			else $_POST['sbt_src'] = $outcome['src'];
		}

		if(!$err){
			$submit_arr = initSubmitColumns('member', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('member') + 1);
			$_POST['sbt_valid'] = ($_POST['sbt_valid'] ? 1 : 0);
			$_POST['sbt_fields'] = json_encode($_POST['sbt_fields']);
			$_POST['sbt_desp'] = modEditorInfo($_POST['sbt_desp'], 'save');

			$submit = array();
			$submit_lan = array();
			for($i = 0; $i < count($submit_arr); $i++){
				if(!isset($_POST['sbt_' . $submit_arr[$i]])) continue;
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
				if($submit_arr[$i] != 'name' || !$_GET['num']) $submit_lan += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			mysql_query('BEGIN');

			$done = 1;
			$done &= $my_db->saveRow('member', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

			if ($_GET['num']) {
				$done &= $my_db->deleteRow('property_content', array('sort' => 3, 'pid' => $_GET['num']));
			}

			foreach ($_POST['sbt_service'] as $key => $value) {
				$done &= $my_db->saveRow('property_content', array('sort' => 3, 'pid' => $_POST['sbt_id'], 'vid' => $key));
			}

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				if($_FILES['sbt_src']['tmp_name']){
					$imgpath = $member_src . $_POST['sbt_src'];
					if(file_exists($imgpath)) unlink($imgpath);
					if(file_exists($_POST['tmp_img'])) unlink($_POST['tmp_img']);

					move_uploaded_file($_FILES['sbt_src']['tmp_name'], $imgpath);
				}


				instructLog($cms_admin_power['member'][$power_id] . $_POST['sbt_name'], ($poser_id == 1 ? 'add' : 'edt'));
				$msg[0] = $cms_admin_power['member'][$power_id] . '成功';
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
			'name' => array('名稱', 'text'), 
			'email' => array('電郵', 'text'), 
			'date' => array('加入時間', 'read'), 
			'valid' => array('有效', 'checkbox'),
			array('__edit', 'edit', array('power' => 'member', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'member'
		)
	);	
}

require('templates/head.html');
require('templates/member.html');
require('templates/foot.html');