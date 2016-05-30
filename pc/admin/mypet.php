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

	if($_GET['num']){
		$outcome = $my_db->fetchOne('mypet', array('id' => $_GET['num']));

	} else {
		$outcome = array();
		$outcome['valid'] = true;
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('member', $power_id)) warning('權限不足');
		else delSelectedData('mypet', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('member', $power_id)) warning('權限不足');

		$_POST['sbt_name'] = trim($_POST['sbt_name']);

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名稱'));

		if(!$err){
			$submit_arr = initSubmitColumns('member', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('member') + 1);
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
			$done &= $my_db->saveRow('member', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

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

	$where = '1';
	
	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'mid' => '會員ID', 
			'type' => '類型',
			'name' => array('名稱', 'text'), 
			'size' => array('尺寸', 'text'), 
			'number' => array('數量', 'read'), 
			'valid' => array('有效', 'checkbox'),
			array('__edit', 'edit', array('power' => 'member', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'mypet'
		)
	);	
}

require('templates/head.html');
require('templates/mypet.html');
require('templates/foot.html');