<?php
require('../include/common.php');
require('../include/fun_admin.php');
require('../include/cls_graphic.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

if ($_GET['type'] == 7) {
	$picture_src = '../' . systemConfig('property_img_path') . $con_pic['pre']['property'];
}

if ($_GET['action'] == 'upload'){
	preg_match('/\.(\w{2,6})$/', $_FILES[$_POST['fields']]['name'], $match);

	$file_name = time() . '.' . $match[1];
	move_uploaded_file($_FILES[$_POST['fields']]['tmp_name'], '../' . systemConfig('file_path') . $file_name);

	echo json_encode(array('filepath' => systemConfig('file_path') . $file_name, 'filename' => $file_name, 'error' => 0, 'msg' => ''));
	exit;

} else if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('property_content', array('id' => $_GET['num']));
		$_GET['sort'] = $outcome['sort'];
		$_GET['pid'] = $outcome['pid'];
	}

	if($_POST['del'] == 'true'){
		$done = 1;
		$imgarr = array();

		foreach($_POST as $key => $value){
			$tmp = explode('chk_', $key);

			if ((int)$tmp[1] > 0) {
				$exist = true;

				$getdata = $my_db->selectRow('*', 'property_content', array('id' => $tmp[1]));
				$res = mysql_fetch_array($getdata);
				if($res['content']) {
					array_push($imgarr, $res['content']);
				}

				$done &= $my_db->deleteRow('property_content', array('id' => $tmp[1]));
			}
		}

		if($done && $exist){
			mysql_query("COMMIT");
			mysql_query("END");

			for($i = 0; $i < count($imgarr); $i++){
				if (strpos($imgarr[$i], 'video_') === 0) {
					if(file_exists($picture_src . $imgarr[$i])) unlink($picture_src . $imgarr[$i]);
				}
				else {
					$big_img = $picture_src . $con_pic['suf']['big'] . $imgarr[$i];
					$mid_img = $picture_src . $con_pic['suf']['mid'] . $imgarr[$i];
					//$sml_img = $picture_src . $con_pic['suf']['sml'] . $imgarr[$i];

					if(file_exists($big_img)) unlink($big_img);
					if(file_exists($mid_img)) unlink($mid_img);
					//if(file_exists($sml_img)) unlink($sml_img);
				}
			}

			$msg[0] = '相冊刪除成功';
			instructLog('相冊刪除', 'del');
		}
		else {
			mysql_query("ROLLBACK");
			mysql_query("END");

			if(!$exist){
				$msg[0] = '未選擇要刪除的項目';
				$msg[1] = 'warn';
			}
			else {
				$msg[0] = '相冊刪除失敗';
				$msg[1] = 'fail';
			}
		}

		$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]*&/', '&', $_SERVER['QUERY_STRING']);
		header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
	}
	else if($_POST['save'] == 'true'){
		$chk_post = new ChkRequest('sbt_');

		$format = 'image';
		//$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);

		// 圖片格式
		$_POST['sbt_content'] = $chk_post->chkImage('img');

		if ($outcome['content']) {
			preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_content'], $match);

			if ($match[1]) {
				$_POST['sbt_content'] = preg_replace('/\.[\w]{3,4}$/', '', $outcome['content']) . $match[1];
				$outcome['content'] = $_POST['sbt_content'];
			} else {
				$_POST['sbt_content'] = $outcome['content'];
			}
		}

		$_POST['sbt_vid'] = ($_POST['property'] && $_POST['sbt_vid']) ? intval($_POST['sbt_vid']) : 0;
		$_GET['sort'] = 7;

		if(!$err){
			$num = 1;
			$done = 1;

			if ($_POST['sbt_vid']) {
				$done &= $my_db->deleteRow('property_content', array('vid' => $_POST['sbt_vid'], 'pid' => $_POST['sbt_pid'], 'sort' => $_GET['sort']));
			}

			$done &= $my_db->saveRow('property_content', array('content' => $_POST['sbt_content'], 'vid' => $_POST['sbt_vid'], 'pid' => $_POST['sbt_pid'], 'sort' => $_GET['sort']), $_GET['num'] ? array('id' => $_GET['num']) : '');
				
			if($done){

				if($_FILES['sbt_img']['tmp_name']) {
					if ($format == 'image') {
						$big_img = $picture_src . $con_pic['suf']['big'] . $_POST['sbt_content'];
						//$mid_img = $picture_src . $con_pic['suf']['mid'] . $_POST['sbt_content'];
						//$sml_img = $picture_src . $con_pic['suf']['sml'] . $_POST['sbt_content'];

						if(file_exists($big_img)) unlink($big_img);
						//if(file_exists($mid_img)) unlink($mid_img);
						//if(file_exists($sml_img)) unlink($sml_img);
						if(file_exists($_POST['tmp_img'])) unlink($_POST['tmp_img']);
						move_uploaded_file($_FILES['sbt_img']['tmp_name'], $big_img);

						$size = array('big' => systemConfig('img_max_size'), 'mid' => systemConfig('img_mid_size'), 'sml' => systemConfig('img_sml_size'));

						$imgop = new Graphic($big_img);

						$imgop->resizeImage($big_img, 500, 500);
						//$imgop->resizeImage($mid_img, $size['mid'], $size['mid']);
						//$imgop->resizeImage($sml_img, $size['sml'], $size['sml']);
					} 
					else if ($format == 'video') {
						move_uploaded_file($_FILES['sbt_img']['tmp_name'], $picture_src . $_POST['sbt_content']);
					}
				}

				instructLog('相冊編輯', ($poser_id == 1 ? 'add' : 'edt'));
				$msg[0] = '相冊編輯成功';
				$msg[1] = 'success';

				$href = $_SERVER['PHP_SELF'] . '?action=lst&pid=' . $_POST['sbt_pid'] . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				$msg[0] = '相冊編輯失敗';
				$msg[1] = 'fail';
			}
		}
	}

	$property_list = array();
	$property_value = array();
	$property_content = array();

	/*
	$getdata = $my_db->selectRow('id, name', 'property', array('type' => $_GET['type']));
	while($result = mysql_fetch_array($getdata)) {
		$property_list[$result['id']] = $result['name'];
	}

	$getdata = $my_db->selectRow('id, pid, value', 'property_value', array('valid' => 1));
	while($result = mysql_fetch_array($getdata)) {
		if (!isset($property_value[$result['pid']])) {
			$property_value[$result['pid']] = array();
		}

		$property_value[$result['pid']][] = $result;
	}

	$getdata = $my_db->selectRow('vid, content', 'property_content', array('sort' => 1, 'pid' => $_GET['pid']));
	while($result = mysql_fetch_array($getdata)) {
		$property_content[] = $result['vid'];
	}
	*/

} else {

	$q_url = queryPart('id', 'desc');

	$where = 'sort = 7' . ($_GET['pid'] ? ' AND pid = ' . $_GET['pid'] : '');

	class FieldFun {
		function __construct($namespace = 1){
			$this->namespace = $namespace;
		}

		function __call($method, $str) {
			global $picture_src;
			global $con_pic;
			global $my_db;

			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					return '<img src="' . $picture_src . $con_pic['suf']['big'] . $str[0] . '" height="50" />';
				case '2_fun1':
					if ($str[0]) {
						$result = $my_db->fetchOne('property_value', array('id' => $str[0]));
						return $result['value'];
					}
			}
		}
	}

	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'pid' => '產品ID',
			//'vid' => array('屬性', 'read', array(new FieldFun(2))),
			'content' => array('縮略圖', 'read', array(new FieldFun())),
			array('__edit', 'edit', array('power' => $cms_cata_type[$_GET['type']]['db'], 'method' => array('detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'property_content',
		)
	);
}

require('templates/head.html');
require('templates/picture.html');
require('templates/foot.html');