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

if($_GET['action'] == "edt"){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('admin', array('id' => $_GET['num']));
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('admin', $power_id)) warning('權限不足');
		else delSelectedData('admin', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('admin', $power_id)) warning('權限不足');

		$chk_post = new ChkRequest('sbt_');

		if(!$_GET['num']){
			$chk_post->chkExist(array('name' => '用户账号'), array('name' => 'admin'), 'define:account');
			$chk_post->chkPassword(array('pass' => '用户密码'), array('confirm' => '确认密码'));
		}

		$chk_post->chkEmpty(array('realname' => '用户姓名', 'rid' => '用户角色'));

		if(!$err){
			$submit_arr = initSubmitColumns('admin', $_GET['num']);

			$_POST['sbt_pass'] = md5($_POST['sbt_pass']);

			$submit = array();
			for($i = 0; $i < count($submit_arr); $i++){
				if($_GET['num'] && ($submit_arr[$i] == 'name' || $submit_arr[$i] == 'pass')) continue;
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			if($my_db->saveRow('admin', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''))){
				instructLog($cms_admin_power['admin'][$power_id] . $_POST['sbt_name'], ($_GET['num'] ? 'edt' : 'add'));
				$msg[0] = '提交成功';

				if($_GET['num']){
					$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
					header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
				}
			}
			else {
				$msg[0] = '提交失敗';
				$msg[1] = 'fail';
			}
		}
	}
	
	$opt_role = '';
	$getdata = $my_db->selectRow('*', 'role', array('id != 1'));
	while($result = mysql_fetch_array($getdata)){
		$opt_role .= '<option value="' . $result['id'] . '"' . ($outcome['rid'] == $result['id'] ? ' selected="selecte"' : '') . '>' . $result['name'] . '</option>';
	}

	require('templates/head.html');
	require('templates/admin.html');

} else if($_GET['action'] == "lst"){
	$role_all = array();
	$getdata = $my_db->selectRow('*', 'role');
	while($result = mysql_fetch_array($getdata)){
		$role_all[$result['id']] = $result['name'];
	}

	require('templates/head.html');
	$q_url = queryPart('name');

	$where = '`rid` != 1';

	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
            'cid' => array('院系', 'read', array($department)),
			'name' => '账号',
			'realname' => array('姓名', 'text'), 
			'rid' => array('角色', 'read', array($role_all)), 
			array('__edit', 'edit', array('power' => 'admin', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'admin',
			'operate' => array('edt', 'delete')
		)
	);

	require('templates/admin.html');
	
} else if($_GET['action'] == "pwd"){
	if(isset($_POST['sbt_oldpwd'])){
		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkPassword(array('newpwd' => '新密码'), array('conpwd' => '确认密码'));

		$result = '';

		if ($_SESSION['admin_type'] == 1) {
			$result = $my_db->fetchOne('admin', array('id' => $_SESSION['admin_id'], 'pass' => md5($_POST['sbt_oldpwd'])));
		} else if ($_SESSION['admin_type'] == 2) {
			$result = $my_db->fetchOne('teacher', array('id' => $_SESSION['admin_id'], 'pass' => md5($_POST['sbt_oldpwd'])));

			if (!$result && $_POST['sbt_oldpwd'] == $cms_default_pass) {
				$result = $my_db->fetchOne('teacher', array('id' => $_SESSION['admin_id'], 'pass' => ''));
			}

		} else if ($_SESSION['admin_type'] == 3) {
			$result = $my_db->fetchOne('student', array('id' => $_SESSION['admin_id'], 'pass' => md5($_POST['sbt_oldpwd'])));

			if (!$result && $_POST['sbt_oldpwd'] == $cms_default_pass) {
				$result = $my_db->fetchOne('student', array('id' => $_SESSION['admin_id'], 'pass' => ''));
			}
		}

		if(!$result) warning('原始密码有误');

		if(!$err){
			if ($_SESSION['admin_type'] == 1) {
				$res = $my_db->saveRow('admin', array('pass' => md5($_POST['sbt_newpwd'])), array('id' => $_SESSION['admin_id']));
			} else if ($_SESSION['admin_type'] == 2) {
				$res = $my_db->saveRow('teacher', array('pass' => md5($_POST['sbt_newpwd'])), array('id' => $_SESSION['admin_id']));
			} else if ($_SESSION['admin_type'] == 3) {
				$res = $my_db->saveRow('student', array('pass' => md5($_POST['sbt_newpwd'])), array('id' => $_SESSION['admin_id']));
			}

			if($res){
				$msg[0] = '密码修改成功';
			}
			else {
				$msg[0] = '密码修改失败';
				$msg[1] = 'fail';
			}

			setcookie('admin[pass]', md5($_POST['newpwd']), time() + systemConfig('cms_login_time') * 60);
		}
	}

	require('templates/head.html');
	require('templates/password.html');
	
}

require('templates/foot.html');