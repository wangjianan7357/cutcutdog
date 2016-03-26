<?php
session_start();

require('../include/common.php');

if($_GET['action'] == 'setup'){
	if(file_exists('../include/setup.php')) require('../include/setup.php');
	else $msg = '安装文件不存在';
}
else if($_GET['action'] == 'logout'){
	session_destroy();
	$_SESSION = array();

	setcookie('admin[id]', '', time() - 1);
	setcookie('admin[pass]', '', time() - 1);
	setcookie('admin[type]', '', time() - 1);
}
else if($_POST['verifycode']){
	if($_SESSION['verifycode'] == strtolower($_POST['verifycode'])){
		if(isset($_POST['name']) && isset($_POST['pass'])){
			$result = null;

			if ($_POST['type'] == 1) {
				$result = $my_db->fetchOne('admin', array('name' => $_POST['name'], 'pass' => $_POST['pass']));

			} else if ($_POST['type'] == 2) {
				$result = $my_db->fetchOne('teacher', array('mobile' => $_POST['name'], 'pass' => $_POST['pass']));

				if (!$result && $_POST['pass'] == md5($cms_default_pass)) {
					$result = $my_db->fetchOne('teacher', array('mobile' => $_POST['name'], 'pass' => ''));
				}

			} else if ($_POST['type'] == 3) {
				$result = $my_db->fetchOne('student', array('number' => $_POST['name'], 'pass' => $_POST['pass']));

				if (!$result && $_POST['pass'] == md5($cms_default_pass)) {
					$result = $my_db->fetchOne('student', array('number' => $_POST['name'], 'pass' => ''));
				}
			}

			if($result){
				$_SESSION['admin_id'] = $result['id'];
				$_SESSION['admin_type'] = $_POST['type'];

				setcookie('admin[id]', $result['id'], time() + systemConfig('cms_login_time') * 60);
				setcookie('admin[pass]', $_POST['pass'], time() + systemConfig('cms_login_time') * 60);
				setcookie('admin[type]', $_POST['type'], time() + systemConfig('cms_login_time') * 60);

				header('Location:index.php');
			}
			else $msg = '用户名或密码有误';
		}
	}
	else $msg = '验证码有误';
}

require('templates/login.html');
?>