<?php
require('../include/common.php');
require('../include/fun_admin.php');
require('../include/cls_graphic.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

$catalog_type = (int)($_GET['type'] ? $_GET['type'] : ($_POST['sbt_type'] ? $_POST['sbt_type'] : 1));
$info_src = '../' . systemConfig('info_img_path') . $con_pic['pre']['info'];

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
		$outcome = $my_db->fetchOne('info', array('id' => $_GET['num']));
		$outcome['service'] = array();

		$getdata = $my_db->selectRow('vid', 'property_content', array('pid' => $_GET['num'], 'sort' => 1));
		while($result = mysql_fetch_array($getdata)) {
			$outcome['service'][] = $result['vid'];
		}

	} else {
		$outcome['valid'] = 1;
		$outcome['service'] = array();
	}

	if($_POST['del'] == 'true'){
		if(!adminPower('info', $power_id)) warning('權限不足');
		else delSelectedData('info', array('id' => $power_id), $info_src);
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('info', $power_id)) warning('權限不足');

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名稱', 'cid' => '分類'));
		$_POST['sbt_path'] = $chk_post->traFromName('name', array('name' => 'info', 'field' => 'path'), $info_arr['path']);
		$_POST['sbt_src'] = $chk_post->chkImage('src');

		if ($outcome['src']) {
			if ($_POST['sbt_src']) {
				preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);
				$_POST['sbt_src'] = preg_replace('/\.[\w]{3,4}$/', '', $outcome['src']) . $match[1];
				$outcome['src'] = $_POST['sbt_src'];
			}
			else $_POST['sbt_src'] = $outcome['src'];
		}

		if(!$err) {
			$submit_arr = initSubmitColumns('info', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('info') + 1);
			$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);
			$_POST['sbt_valid'] = $_POST['sbt_valid'] ? 1 : 0;
			$_POST['sbt_desp'] = modEditorInfo($_POST['sbt_desp'], 'save');

			$submit = array();
			for($i = 0; $i < count($submit_arr); $i++){
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			mysql_query('BEGIN');

			$done = 1;
			$done &= $my_db->saveRow('info', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

			if ($_GET['num']) {
				$done &= $my_db->deleteRow('property_content', array('sort' => 1, 'pid' => $_GET['num']));
			}

			foreach ($_POST['sbt_service'] as $key => $value) {
				$done &= $my_db->saveRow('property_content', array('sort' => 1, 'pid' => $_POST['sbt_id'], 'vid' => $key));
			}

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				if($_FILES['sbt_src']['tmp_name']){
					$big_img = $info_src . $con_pic['suf']['big'] . $_POST['sbt_src'];
					$mid_img = $info_src . $con_pic['suf']['mid'] . $_POST['sbt_src'];
					$sml_img = $info_src . $con_pic['suf']['sml'] . $_POST['sbt_src'];

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

				instructLog($cms_admin_power['info'][$power_id] . $_POST['sbt_name'], ($poser_id == 1 ? 'add' : 'edt'));
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

} else if ($_GET['action'] == 'import') {

	if($_POST['save'] == 'true'){

		if (1) {

		}

		preg_match('/(\.[\w]{3,4})$/', $_FILES['sbt_file']['name'], $match);
		$file = time() . $match[1];

		move_uploaded_file($_FILES['sbt_file']['tmp_name'], '../' . systemConfig('file_path') . $file);

		require_once '../include/excel/PHPExcel.php';
	    require_once '../include/excel/PHPExcel/IOFactory.php';
	    require_once '../include/excel/PHPExcel/Reader/Excel5.php';

		$objReader = PHPExcel_IOFactory::createReader('Excel5'); //use excel2007 for 2007 format 
		$objPHPExcel = $objReader->load('../' . systemConfig('file_path') . $file); 
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

        $catalog = array();
        $getdata = $my_db->selectRow('*', 'info', array('valid' => 1, 'type' => 3));
        while ($result = mysql_fetch_array($getdata)) {
            $catalog[$result['id']] = $result;
        }

		$error = array();

        for ($j = 1; $j <= $highestRow; $j ++) { 
            $error[$j] = '';

			$submit = array(
                'cid' => '',
                'valid' => 1,
            );

            for ($k = 'A'; $k <= $highestColumn; $k ++) {
                $cell = trim($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue());

                switch ($k) {
                    case 'A':
                        if ($my_db->fetchOne('info', array('path' => strtolower($cell)))) {
                            $submit['path'] = time();
                        } else {
                            $submit['path'] = strtolower($cell);
                        }
                    break;
                    case 'B':
                        if (!$cell) {
                            $error[$j] .= '店鋪名為空；';
                        } else {
                            $submit['name'] = $cell;
                        }
                    break;
                	case 'C':
                        if (!$cell) {
                            $error[$j] .= '一級地區分類為空；';

                        } else {
                            foreach ($catalog as $cid => $v) {
                                if ($v == $cell) {
                                    $submit['cid'] .= $cid . ',';
                                    break;
                                }
                            }
                        }
                	break;
                	case 'D':
                		if (!$cell) {
                            $error[$j] .= '二級地區分類為空；';

                        } else {
                            foreach ($catalog as $cid => $v) {
                                if ($v == $cell) {
                                    $submit['cid'] .= $cid . ',';
                                    break;
                                }
                            }
                        }
                	break;
                	case 'E':
                		$submit['address'] = $cell;
                	break;
                	case 'F':
                		$submit['tel'] = $cell;
                	break;
                    case 'H':
                        $submit['website'] = $cell;
                    break;
                    case 'I':
                        $submit['desp'] = $cell;
                    break;
                }
            }

            if (!$error[$j]) {
                $my_db->saveRow('info', $submit);
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
			global $q_url;

			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					return catalogOption($catalog_type, $str[0]);
				case '1_fun2':
					global $catalog_all;
					return $str[0] ? $catalog_all[preg_replace('/(^[\d,]+,|^)(\d+),$/', '\\2', $str[0])]['name'] : '<font color="red">未歸類</font>'; 
				case '3_fun1':
					$code = '<a href="' . $_SERVER['PHP_SELF'] . '?action=edt&num=' . $str[0]['id'] . $q_url['24567'] . '&flag=' . $_GET['flag'] . '">編輯</a>';

					if ($catalog_type == 3) {
						$code .= ' &nbsp; <a href="picture.php?action=lst&pid=' . $str[0]['id'] . '&type=2">相冊</a>';
					}

					return $code;
					
			}
		}
	}
	
	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			//'queue' => array('序列', 'text'), 
			'name' => '名稱',
			'cid' => array('分类', 'select', array(new FieldFun())),
			//'path' => 'URL', 
			'valid' => array('狀態', 'checkbox'),
			array('__edit', 'edit', array(new FieldFun(3))),
		),
		array(
			'where' => $where,
			'table' => 'info',
		)
	);
	
}

require('templates/head.html');
require('templates/info.html');
require('templates/foot.html');