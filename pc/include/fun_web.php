<?php

/* 登录时限 */
function loginTimeout(){
	session_start();
	header('Cache-control: private');

	if(!$_COOKIE['cookie']['memberid'] || !$_COOKIE['cookie']['password']){
		header('Content-type:text/html;charset=utf-8');
		setcookie('cookie[wanturl]', $_SERVER['REQUEST_URI'], time() + 99999, '/');
		echo '<script language="javascript">alert("請先登錄"); top.location.href = "' . L_PATH . 'login.php";</script>';
		exit();
	}
	else {
		setcookie('cookie[password]', $_COOKIE['cookie']['password'], time() + systemConfig('cms_login_time') * 60, '/');
		setcookie('cookie[memberid]', $_COOKIE['cookie']['memberid'], time() + systemConfig('cms_login_time') * 60, '/');
		setcookie('cookie[firstname]', $_COOKIE['cookie']['firstname'], time() + systemConfig('cms_login_time') * 60, '/');
		setcookie('cookie[lastname]', $_COOKIE['cookie']['lastname'], time() + systemConfig('cms_login_time') * 60, '/');
	}
}

function lang_url($abbr){
	preg_match('/(\/$)/i', $_SERVER['REQUEST_URI'], $match);
	if($match[1]){
		return $_SERVER['REQUEST_URI'] . $abbr . '/' . 'index.html';
	}
	else {
		preg_match('/(\/[\w]{2})\/[^\/]+$/i', $_SERVER['REQUEST_URI'], $match);
		if($match[1]) return preg_replace('/\/[\w]{2}\/([^\/]+$)/i', '/' . $abbr . '/\\1', $_SERVER['REQUEST_URI']);
		else return preg_replace('/\/([^\/]+$)/i', '/' . $abbr . '/\\1', $_SERVER['REQUEST_URI']);
	}
}

// define constant for different language
function basicConstant($lang = null){
	global $my_db;
	global $con_pic;
	global $con_posi;
	global $con_lang_default;
	global $con_lang_current;

	// 定义常量
	if($my_db->tableExist('constant')){	
		$getdata = $my_db->selectRow('*', 'constant');
		while($result = mysql_fetch_array($getdata)){
			define('C_' . $result['various'], $result[$con_lang_current . 'txt']);
		}
	}

	$def_data = array();
	// 多语没有公司信息时，读取默认语言信息
	if($con_lang_current != $con_lang_default){
		$getdata = $my_db->selectRow('*', 'system', array('tid' => 2, 'lang' => $con_lang_default));
		while($result = mysql_fetch_array($getdata)){
			$def_data[$result['varname']] = $result['value'];
		}
	}

	// 定义网站信息
	$getdata = $my_db->selectRow('*', 'system', array('tid' => 2, 'lang' => $con_lang_current));
	while($result = mysql_fetch_array($getdata)){
		define('CO_' . strtoupper($result['varname']), $result['value'] ? $result['value'] : $def_data[$result['varname']]);
	}

	// 定义图片路径
	foreach ($con_pic['pre'] as $key => $value) {
		$src = systemConfig($key . '_img_path') . $value;

		// 产品、信息、会员有多种尺寸
		if ($key == 'product' || $key == 'info' || $key == 'member') {
			foreach ($con_pic['suf'] as $key1 => $value1) {
				define('PIC_' . strtoupper($key) . '_' . strtoupper($value1), $src . $value1);
			}
		}
		else {
			define('PIC_' . strtoupper($key), $src);
		}
	}

	// 定义页面路径
	$path = '';
	if ($con_lang_default != $con_lang_current) {
		$path .= '../';
	}
	if ($con_posi == 'member') {
		$path .= '../';
	}
	define('L_PATH', $path);

	$path = '';
	if ($con_posi != '') {
		$path .= '../';
	}	
	define('P_PATH', $path);
}

// translation codes inside the information, use this when defined key1
function tr_code($msg, $lang = '', $tackled = false){
	if(strpos($msg, '<assign') === false) return $msg;

	global $my_db;
	
	// for pattern: <assign></assign> (require MatchHtml Class)
	$content = new MatchHtml($msg);
	$content->getElementsByTagName('assign');
	for($i = 0; $i < $content->length(); $i++){
		$str = '';
		$nest = 0;
		$innerhtml = $content->innerHtml($i);

		if(strpos($innerhtml, '<assign') !== false){
			$nest = substr_count($innerhtml, '<assign');
			$innerhtml = tr_code($innerhtml, $lang);
		}
		
		switch($content->getAttribute('from', $i)){
			// <assign from="constant">CO_WEBNAME</assign>
			case 'constant':
				if(defined($innerhtml)) eval('$str = ' . $innerhtml . ';');
				$content->outerHtml($i, $str);
			break;
			// <assign from="various">$cur_data['name']</assign>
			case 'various':
				$strlen = (int)$content->getAttribute('length', $i);
				$target = $content->getAttribute('target', $i);
				$source = $content->getAttribute('source', $i);

				// save protect
				$code = preg_replace('/[\(\) =;]/', '', $source);
				if(strpos($code, '$') === 0){
					$var_arr = explode('$', $code);
					for($j = 1; $j < count($var_arr); $j++){
						// set global array various
						eval('global ' . preg_replace('/^([^\[\]]*)[\w\W]*$/i', '$\\1', $var_arr[$j]) . ';');
					}
					eval('$str = ' . $code . ';');
					$str = strtr(strip_tags($str), array('&nbsp;' => ' '));
					if($strlen) $str = cutString($str, $strlen, ' ', false);
				}
				$content->setOuterHtml(preg_replace("/($target)/", $str, $innerhtml), $i);
			break;
			// <assign from="clone" path="index">P_TITLE</assign>
			case 'clone':
				if(!$tackled){
					$innerhtml = preg_replace('/^P_/i', '', strtolower($innerhtml));
					$getdata = $my_db->selectRow('*', 'page', array('path' => $content->getAttribute('path', $i)));
					while($result = mysql_fetch_array($getdata)){
						$content->outerHtml($i, tr_code($result[$innerhtml], true));
						break;
					}
				}
			break;
			// <assign from="catalog" sep="|" max="5" target="_CATALOG" where="type = 2">_CATALOG</assign>
			case 'catalog':
				$str = '';
				$cycle = $content->getAttribute('cycle', $i);
				$target = $content->getAttribute('target', $i);
				$sep = $content->getAttribute('sep', $i);
				$where = $content->getAttribute('where', $i);
				$offset = $content->getAttribute('offset', $i);
				$limit = ($limit = $content->getAttribute('max', $i)) ? $limit : '';
				if($offset) $limit = $offset . ', ' . $limit;

				$j = 0;
				$getdata = $my_db->selectRow('*', 'catalog', array($where), array('field' => 'queue', 'method' => 'DESC'), $limit);
				while($result = mysql_fetch_array($getdata)){
					$j++;
					$str .= preg_replace("/($target)/", $result['name'], $innerhtml) . ($j == (int)$limit ? '' : $sep);
				}
				$content->outerHtml($i, $str);
			break;
			// <assign from="selection" depart="|" condition="$cur_data['id'] % 5">Manufacturer|Exporter|Factory</assign>
			case 'selection':
				$sel_arr = array();
				$depart = $content->getAttribute('depart', $i);
				$depart = ($depart ? $depart : '|');

				$j = 0;
				$tmp_arr = explode($depart, $innerhtml);
				foreach($tmp_arr as $key => $value){
					if($value == '') unset($tmp_arr[$key]);
					else {
						$sel_arr[$j] = $tmp_arr[$key];
						$j++;
					}
				}

				$condition_data = $content->getAttribute('condition', $i);
				if($condition_data){
					preg_match_all('/\$([a-z_]+)(\[| |$)/i', $condition_data, $match);
					$various = array_unique($match[1]);
					for($j = 0; $j < count($various); $j++){
						eval('global $' . $various[$j] . ';');
					}

					$condition_data = preg_replace('/;/', ' ', $condition_data);
					$condition_data = preg_replace('/(^| )[a-z\-][^ ]+/i', ' ', $condition_data);

					eval('$condition = (int)(' . $condition_data . ');');
					$condition = ($condition >= count($sel_arr)) ? (count($sel_arr) - 1) : $condition;
				}
				else $condition = rand(0, (count($sel_arr) - 1));

				$content->outerHtml($i, $sel_arr[$condition]);
			break;
			case 'judgement':
				
			break;
		}

		$i += $nest;
	}
	return trim(preg_replace('/\s+/', ' ', $content->showHtml()));
}

/*
 * 记录浏览者
 * 用 cookie 记录是否浏览过
 */
function recordVisitor($table = array()){
	global $my_db;
	global $con_lang_current;

	$visit_time_out = 24 * 60 * 60;
	$page = $table['name'] . $table['where']['id'] . '-' . $con_lang_current;

	if(!$_COOKIE['cookie'][$page]){
		setcookie('cookie[' . $page . ']', true, time() + $visit_time_out);

		$getdata = $my_db->selectRow('visitor', $table['name'], $table['where']);
		$result = mysql_fetch_array($getdata);
		$my_db->saveRow($table['name'], array('visitor' => (int)$result['visitor'] + 1), $table['where']);
	}
}

/*
 * 缓存页面
 */
function pageCache($method = 'read'){
	$time = systemConfig('page_cache_time');

	// 判断是否启用缓存
	if($time && checkFolder('cache')){
		$cache_file = 'cache/'. md5($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '.php';

		if($method == 'read'){
			if(file_exists($cache_file) && time() - filemtime($cache_file) < $time){
				include($cache_file);
				exit;
			}
			else ob_start();
		}
		else if($method == 'save'){
			$fp = fopen($cache_file, 'w');
			fwrite($fp, preg_replace('/\n|\r|<!--[\s\S]*?-->/', '', ob_get_contents()));
			fclose($fp);
			ob_end_flush();
		}
	}
}

/*
 * 清除路径请求中的参数
 */
function clearPathRequest($para, $path = ''){
	$path_arr = explode('?', $_SERVER['REQUEST_URI']);

	$tmp = '';
	if(count($path_arr) > 1){
		$para = (array)$para;
		$path_arr[1] = '&' . $path_arr[1] . '&';
		for($i = 0; $i < count($para); $i++){
			$path_arr[1] = preg_replace('/&' . $para[$i] . '=[^&]*&/', '&', $path_arr[1]);
		}
		$tmp .= preg_replace('/^&|&$/', '', $path_arr[1]);
	}

	return ($path ? $path : $path_arr[0]) . '?' . ($tmp ? ($tmp . '&') : '');
}

?>