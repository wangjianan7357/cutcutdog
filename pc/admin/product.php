<?php
require('../include/common.php');
require('../include/fun_admin.php');
require('../include/cls_graphic.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

$catalog_type = (int)($_GET['type'] ? $_GET['type'] : ($_POST['sbt_type'] ? $_POST['sbt_type'] : 1));
$product_src = '../' . systemConfig('product_img_path') . $con_pic['pre']['product'];

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('product', array('id' => $_GET['num']));
	} else {
		$outcome['valid'] = 1;
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('product', $power_id)) warning('權限不足');
		else delSelectedData('product', array('id' => $power_id), $product_src);
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('product', $power_id)) warning('權限不足');

		$_POST['sbt_name'] = trim($_POST['sbt_name']);

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名稱', 'cid' => '分類'));
		$_POST['sbt_path'] = $chk_post->traFromName('name', array('name' => 'product', 'field' => 'path'), $outcome['path']);
		$_POST['sbt_src'] = $chk_post->chkImage('src');

		if(!$_GET['num'] && !$_POST['sbt_src']) warning('請添加縮略圖');
		else {
			if ($outcome['src']) {
				if ($_POST['sbt_src']) {
					preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);
					$_POST['sbt_src'] = preg_replace('/\.[\w]{3,4}$/', '', $outcome['src']) . $match[1];
					$outcome['src'] = $_POST['sbt_src'];
				}
				else $_POST['sbt_src'] = $outcome['src'];
			}
		}

		if(!$err) {
			$submit_arr = initSubmitColumns('product', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('product') + 1);
			$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);
			$_POST['sbt_recom'] = min(max((int)$_POST['sbt_recom'], 0), $cms_max_num['queue']);
			$_POST['sbt_price'] = sprintf("%01.2f", $_POST['sbt_price']);
			$_POST['sbt_sale'] = sprintf("%01.2f", $_POST['sbt_sale']);
			$_POST['sbt_valid'] = $_POST['sbt_valid'] ? 1 : 0;
			$_POST['sbt_desp'] = modEditorInfo($_POST['sbt_desp'], 'save');

			mysql_query('BEGIN');

			$done = 1;
			$submit = array();
			for($i = 0; $i < count($submit_arr); $i++){
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			$done &= $my_db->saveRow('product', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				if($_FILES['sbt_src']['tmp_name']){
					$big_img = $product_src . $con_pic['suf']['big'] . $_POST['sbt_src'];
					$mid_img = $product_src . $con_pic['suf']['mid'] . $_POST['sbt_src'];
					$sml_img = $product_src . $con_pic['suf']['sml'] . $_POST['sbt_src'];

					if(file_exists($big_img)) unlink($big_img);
					if(file_exists($mid_img)) unlink($mid_img);
					if(file_exists($sml_img)) unlink($sml_img);
					if(file_exists($_POST['tmp_img'])) unlink($_POST['tmp_img']);
					move_uploaded_file($_FILES['sbt_src']['tmp_name'], $big_img);

					$size = array('big' => explode(',', systemConfig('img_big_size')), 'mid' => explode(',', systemConfig('img_mid_size')), 'sml' => explode(',', systemConfig('img_sml_size')));

					$imgarr = array();
					$imgop = new Graphic($big_img);
					$imgarr['width'] = $imgop->getWidth();
					$imgarr['height'] = $imgop->getHeight();

					$imgop->resizeImage($big_img, $size['big'][0], $size['big'][1]);
					$imgop->resizeImage($mid_img, $size['mid'][0], $size['mid'][1]);
					$imgop->resizeImage($sml_img, $size['sml'][0], $size['sml'][1]);
				}

				$msg[0] = '提交成功';
				$msg[1] = 'success';
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
	$where = 0;

	$catalog_all = array();
	$getdata = $my_db->selectRow('id, name, parent', 'catalog', array('type' => $catalog_type));
	while($result = mysql_fetch_array($getdata)) {
		$catalog_all[$result['id']] = $result;
		$where .= ' OR `cid` LIKE "' . $result['id'] . ',%"';
	}

	$where = '(' . $where . ')';

	$q_url = queryPart('date', 'desc');

	class FieldFun {
		function __construct($namespace = 1){
			$this->namespace = $namespace;
		}

		function __call($method, $str) {
			global $catalog_type;

			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					return catalogOption($catalog_type, $str[0]);
				case '1_fun2':
					global $catalog_all;
					return $str[0] ? $catalog_all[preg_replace('/(^[\d,]+,|^)(\d+),$/', '\\2', $str[0])]['name'] : '<font color="red">未歸類</font>'; 
				case '2_fun1':
					return '$' . $str[0]; 
				case '3_fun1':
					$code = '<a href="' . $_SERVER['PHP_SELF'] . '?action=edt&num=' . $str[0]['id'] . $q_url['24567'] . '&flag=' . $_GET['flag'] . '">編輯</a>';

					if ($catalog_type == 7) {
						$code .= ' &nbsp; <a href="picture.php?action=lst&pid=' . $str[0]['id'] . '&type=' . $catalog_type . '">相冊</a>';
					}

					return $code;
					
			}
		}
	}
	
	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'queue' => array('序列', 'text'), 
			'name' => '名稱',
			'cid' => array('分类', 'select', array(new FieldFun())),
			'path' => 'URL', 
			'sale' => array('售價', 'read', array(new FieldFun(2))),
			'valid' => array('狀態', 'checkbox'),
			array('__edit', 'edit', array(new FieldFun(3))),
		),
		array(
			'where' => $where,
			'table' => 'product',
		)
	);
	
}

require('templates/head.html');
require('templates/product.html');
require('templates/foot.html');