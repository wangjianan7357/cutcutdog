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
}
else if($_POST['verifycode']){
	if($_SESSION['verifycode'] == strtolower($_POST['verifycode'])){
		if(isset($_POST['name']) && isset($_POST['pass'])){
			$result = $my_db->fetchOne('admin', array('name' => $_POST['name'], 'pass' => $_POST['pass']));

			if($result){
				$_SESSION['admin_id'] = $result['id'];
				setcookie('admin[id]', $result['id'], time() + systemConfig('cms_login_time') * 60);
				setcookie('admin[pass]', $_POST['pass'], time() + systemConfig('cms_login_time') * 60);

				header('Location:index.php');
			}
			else $msg = '用戶名或密碼有誤';
		}
	}
	else $msg = '驗證碼有誤';
}

require('templates/login.html');
