<?php
/**
 * 检测上传
 */

class ChkRequest {
	protected $req;
	protected $err;
	protected $lang;

	function __construct($prefix = '', $method = 'post'){
		$this->prefix = $prefix;
		$this->err = '';

		switch (strtolower($method)) {
			case 'post':
				$this->req = $_POST;
				break;
			case 'get':
				$this->req = $_GET;
				break;
			default:
				$this->req = $_REQUEST;
				break;
		}

		$this->lang = array(
			'required' => ' 为必填项目，不可留空',
			'contain_invalid_code' => ' 含无效字符：',
			'invalid' => ' 格式有误',
			'existed' => ' 已使用',
			'character' => ' 字符',
			'no_less_than' => ' 不得少于 ',
			'no_more_than' => ' 不得多于 ',
			'and' => ' 与 ',
			'is_not_the_same' => ' 不符',
		);
	}

	public function autoTranslate() {
		$this->lang = array(
			'required' => ' ' . C_REQUIRED,
			'contain_invalid_code' => ' ' . C_CONTAIN_INVALID_CODE,
			'invalid' => ' ' . C_INVALID,
			'existed' => ' ' . C_EXISTED,
			'character' => ' ' . C_CHARACTER,
			'no_less_than' => ' ' . C_NO_LESS_THAN . ' ',
			'no_more_than' => ' ' . C_NO_MORE_THAN . ' ',
			'and' => ' ' . C_AND . ' ',
			'is_not_the_same' => ' ' . C_IS_NOT_THE_SAME,
		);
	}

	public function warning($str){
		global $err;
		if(!$err) $err = $str;
		if(!$this->err) $this->err = $str;
	}

	/*
	 * 检测是否为空
	 */
	public function chkEmpty($name = array()){
		foreach ($name as $key => $value) {
			if(empty($this->req[$this->prefix . $key])) {
				$this->warning($value . $this->lang['required']);
				return false;
			}
		}
		return true;
	}

	/*
	 * 检测是否含无效字符
	 */
	public function chkInvalid($name = array(), $exam = null){
		if(!$exam) {
			return false;
		}

		// 预设检测
		$define = array(
			'password' => '0-9a-zA-Z_@#&\-\$',
			'account' => '0-9a-zA-Z_@#\-\.',
			'mark' => '0-9a-z_',
			'number' => '\d\.',
		);

		if(strpos($exam, 'match:') === 0) {
			$exam = '/(' . substr($exam, 6) . ')/';
		}
		else if(strpos($exam, 'regexp:') === 0) {
			$exam = substr($exam, 7);
		}
		else if(strpos($exam, 'define:') === 0) {
			$exam = '/([^' . $define[substr($exam, 7)] . '])/';
		}

		if($exam) {
			foreach ($name as $key => $value) {
				$match = null;
				preg_match($exam, $this->req[$this->prefix . $key], $match);

				if($match[1]){
					$this->warning($value . $this->lang['contain_invalid_code'] . $match[1]);
					return false;
				}
			}
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * 检测格式是否正确
	 */
	public function chkFormat($name = array(), $exam = null){
		if(!$exam) {
			return false;
		}

		// 预设检测
		$define = array(
			'email' => '^[._a-zA-Z0-9-]+@([-_a-zA-Z0-9]+\.)+[a-zA-Z0-9]{2,4}$',
			'date' => '^\d{4}-\d{2}-\d{2}$',
			'phone' => '^(\d{7,8}|\d{3,4}\-\d{7,8}|\d{3,4}\-d{7,8}\-\d{1,4}|\d{7,8}\-\d{1,4})$',
			'mobile' => '^\d{11}$',
		);

		if(strpos($exam, 'regexp:') === 0) {
			$exam = substr($exam, 7);
		}
		else if(strpos($exam, 'define:') === 0) {
			$exam = '/' . $define[substr($exam, 7)] . '/';
		}

		if($exam) {
			foreach ($name as $key => $value) {
				if(!preg_match($exam, $this->req[$this->prefix . $key])) {
					$this->warning($value . $this->lang['invalid']);
					return false;
				}
			}
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * 检测字符长度
	 */
	public function chkLength($name = array(), $min = '', $max = ''){
		foreach ($name as $key => $value) {
			if($min && strlen($this->req[$this->prefix . $key]) < $min) {
				$this->warning($value . $this->lang['no_less_than'] . $min . $this->lang['character']);
				return false;
			}

			if($max && strlen($this->req[$this->prefix . $key]) > $max) {
				$this->warning($value . $this->lang['no_more_than'] . $max . $this->lang['character']);
				return false;
			}
		}

		return true;
	}

	/* 
	 * 检测字符存在与否
	 * where 附加其他判断条件
	 */
	public function chkExist($name, $table = array(), $exam = null){
		global $my_db;

		$passed = 1;
		$passed &= $this->chkEmpty($name);
		$passed &= !((bool)$exam ^ (bool)$this->chkInvalid($name, $exam));

		if ($passed) {
			if (!isset($table['where'])) {
				// 默认设置判断排除自身
				$table['where'] = 'id != ' . intval($_GET['num']);
			}

			foreach ($name as $key => $value) {
				$field = $table['field'] ? $table['field'] : $key;
				$where = array($table['where'] . ' AND `' . $field . '` = "' . addslashes($this->req[$this->prefix . $key]) . '"');

				$result = '';
				$getdata = $my_db->selectRow($field, $table['name'], $where);
				$result = mysql_fetch_array($getdata);

				if ($result) {
					$passed = false;
					$this->warning($value . $this->lang['existed']);
				}
			}
		}

		return $passed;
	}

	// 在保持后缀的情况下，按时间生成随机文件名
	public function createName($name){
		// 匹配后缀
		preg_match('/(\.[\w]{3,4})$/', $name, $match);
		return substr(time(), -8, 8) . rand(10, 99) . strtolower($match[1]);
	}

	/*
	 * 会员注册时检测用户名有效性
	 */
	public function chkUsername($name = array()){
		global $my_db;

		$this->chkInvalid($name, "regexp:/[^\x{4e00}-\x{9fa5}|\d|a-zA-Z|_]/u");

		$getused = systemConfig('ban_member_name');
		$used = explode(',', $getused);

		foreach ($name as $key => $value) {
			$getdata = $my_db->selectRow('username', 'member', array($key => $this->req[$this->prefix . $key]));
			if(mysql_fetch_array($getdata)) {
				$used[] = $this->req[$this->prefix . $key];
			}

			for($i = 0; $i < count($used); $i++){
				if($this->req[$this->prefix . $name] == $used[$i]){
					$this->warning($value . $this->req[$this->prefix . $key] . $this->lang['existed']);
				}
			}
		}
	}

	/* 
	 * 检测密码
	 */
	public function chkPassword($name = array(), $confirm = array()){
		$passed = 1;
		$passed &= $this->chkEmpty($name);
		$passed &= $this->chkLength($name, 6, 14);
		$passed &= $this->chkInvalid($name, 'define:password');

		if($this->req[$this->prefix . key($name)] != $this->req[$this->prefix . key($confirm)]) {
			$this->warning(current($name) . $this->lang['and'] . current($confirm) . $this->lang['is_not_the_same']);
			$passed = 0;
		}

		return $passed;
	}

	/* 
	 * 检测上传图片
	 * 成功则生成图片档名称，否则返回空
	 */
	public function chkImage($from){
		global $my_db;
		$passed = true;

		if(empty($_FILES[$this->prefix . $from]['name'])){
			$passed = false;
		}
		else {
			$maxsize = systemConfig('img_max_bytes');

			if($_FILES[$this->prefix . $from]['size'] / 1024 > $maxsize){
				$passed = false;
				$this->warning('上传缩略图片不得大于 ' . $maxsize . ' kb');
			}

			$valid = false;
			$format = explode(',', systemConfig('img_format'));
			for($i = 0; $i < count($format); $i++){
				if($_FILES[$this->prefix . $from]['type'] == 'image/' . $format[$i]){
					$valid = true;
					break;
				}
			}

			if(!$valid){
				$passed = false;
				$this->warning('不支持该上传缩略图格式');
			}
		}

		if($passed){
			return $this->createName($_FILES[$this->prefix . $from]['name']);
		}
		else return '';
	}

	/*
	 * 检测上传
	 */
	public function chkUpload($name){
		switch($_FILES[$name]['error']){
			case 1:
			case 2: $this->warning('上传的文件大于服务器限定值'); break;
			case 3: $this->warning('未能完整上传文件'); break;
			case 4: $this->warning('上传文件失败'); break;
		}

		if(!is_uploaded_file($_FILES[$name]['tmp_name'])) {
			$this->warning('文件上传失败');
		}
	}

	/* 
	 * 利用名称自动转换成路径
	 */
	public function traFromName($from, $table = array(), $origin = ''){
		global $my_db;
		require_once('cls_pinyin.php');

		$sep = '-';
		$auto = true;
		$pinyin = new PinYin();
		$temp = $pinyin->transfer(strtolower($this->req[$this->prefix . $from]));

		if($table['field'] == 'path'){
			if (preg_match('/(^http:\/\/|\.html$)/', $this->req[$this->prefix . $table['field']])) {
				return $this->req[$this->prefix . $table['field']];
			}

			$auto = systemConfig('auto_refresh_path');

			if(!$auto) {
				$transfer = $this->req[$this->prefix . $table['field']] ? $this->req[$this->prefix . $table['field']] : $origin;

				if (!$transfer) {
					$auto = true;
				}
			}
			
			if($auto) {
				$transfer = $temp;
				$transfer = preg_replace('/[\- \/]+/is', '-', trim($transfer));
				$transfer = cutString(preg_replace('/[^0-9a-z_\-]/', '', $transfer), systemConfig('path_max_length'), '-', false);
			}

			if(!$transfer) $this->warning('请输入路径');
			$transfer = strtolower($transfer);
		}

		if($transfer){
			$where = '`' . $table['field'] . '` LIKE "' . $transfer . '%" AND ';
			// 默认设置判断排除自身
			$where .= isset($table['where']) ? $table['where'] : ('id != "' . addslashes($_GET['num']) . '"');

			$getdata = $my_db->selectRow($table['field'], $table['name'], array($where), array('length(`' . $table['field'] . '`), `' . $table['field'] . '` ASC'));
			while ($result = mysql_fetch_array($getdata)) {
				if ($result[$table['field']] == $transfer) {
					if (!$auto) {
						if($table['field'] == 'path'){
							$this->warning('路径已使用');
						}

						break;
					}
					else {
						preg_match('/[_\-]([0-9]*)$/is', $transfer, $match);
						$transfer = preg_replace('/[_\-][0-9]*$/is', '', $transfer) . $sep . ((int)$match[1] + 1);
					}
				}
			}
		}

		return $transfer;
	}


	/* 验证银行卡号，bankno为银行卡号 */
	public function chkLuhmCheck($bankno){
	    $lastNum = substr($bankno, -1);//取出最后一位（与luhm进行比较）
	  
	    $first15Num = substr($bankno, 0, strlen($bankno) - 1);//前15或18位

	    $newArr = array();
	    for($i = strlen($first15Num) - 1; $i > -1; $i--){//前15或18位倒序存进数组
	        array_push($newArr, $first15Num[$i]);
	    }

	    $arrJiShu = array();  //奇数位*2的积 <9
	    $arrJiShu2 = array(); //奇数位*2的积 >9
	    $arrOuShu = array();  //偶数位数组

	    for($j = 0; $j < count($newArr); $j++){
	        if(($j + 1) % 2 == 1){//奇数位
	            if($newArr[$j] * 2 < 9) array_push($arrJiShu, $newArr[$j] * 2);
	            else array_push($arrJiShu2, $newArr[$j] * 2);
	        }
	        else array_push($arrOuShu, $newArr[$j]); //偶数位
	    }
	     
	    $jishu_child1 = array();//奇数位*2 >9 的分割之后的数组个位数
	    $jishu_child2 = array();//奇数位*2 >9 的分割之后的数组十位数

	    for($h = 0; $h < count($arrJiShu2); $h++){
	        array_push($jishu_child1, $arrJiShu2[$h] % 10);
	        array_push($jishu_child2, $arrJiShu2[$h] / 10);
	    }

	    $sumJiShu = 0; //奇数位*2 < 9 的数组之和
	    $sumOuShu = 0; //偶数位数组之和
	    $sumJiShuChild1 = 0; //奇数位*2 >9 的分割之后的数组个位数之和
	    $sumJiShuChild2 = 0; //奇数位*2 >9 的分割之后的数组十位数之和
	    $sumTotal = 0;

	    for($m = 0; $m < count($arrJiShu); $m++){
	        $sumJiShu = $sumJiShu + $arrJiShu[$m];
	    }

	    for($n = 0; $n < count($arrOuShu); $n++){
	        $sumOuShu = $sumOuShu + $arrOuShu[$n];
	    }

	    for($p = 0; $p < count($jishu_child1); $p++){
	        $sumJiShuChild1 = $sumJiShuChild1 + (int)$jishu_child1[$p];
	        $sumJiShuChild2 = $sumJiShuChild2 + (int)$jishu_child2[$p];
	    }     
	   
	    //计算总和
	    $sumTotal = $sumJiShu + $sumOuShu + $sumJiShuChild1 + $sumJiShuChild2;

	    //计算Luhm值
	    $k = ($sumTotal % 10 == 0) ? 10 : ($sumTotal % 10);
	    $luhm = 10 - $k;

	    if($lastNum == $luhm) {
	    	$this->warning('银行卡格式有误');
	    	return true;
	    }
	    else return false;
	}

	/* 身份证验证 */
	public function checkIdCard($name = array()){
	    $passed = 1;
		$passed &= $this->chkEmpty($name);

		$city = array(11 => '北京', 12 => '天津', 13 => '河北', 14 => '山西', 15 => '内蒙古', 21 => '辽宁', 22 => '吉林', 23 => '黑龙江', 31 => '上海', 32 => '江苏', 33 => '浙江', 34 => '安徽', 35 => '福建', 36 => '江西', 37 => '山东', 41 => '河南', 42 => '湖北', 43 => '湖南', 44 => '广东', 45 => '广西', 46 => '海南', 50 => '重庆', 51 => '四川', 52 => '贵州', 53 => '云南', 54 => '西藏', 61 => '陕西', 62 => '甘肃', 63 => '青海', 64 => '宁夏', 65 => '新疆', 71 => '台湾', 81 => '香港', 82 => '澳门', 91 => '国外');

		foreach ($name as $key => $value) {
			$idcard = $this->req[$this->prefix . $key];

		    $iSum = 0;
		    $cardLength = strlen($idcard);

		    //长度验证
		    if(!preg_match('/^\d{17}(\d|x)$/i', $idcard) && !preg_match('/^\d{15}$/i', $idcard)){
		        $passed = false;
		    }

		    //地区验证
		    if(!array_key_exists(intval(substr($idcard, 0, 2)), $city)){
		       $passed = false;
		    }

		    // 15位身份证验证生日，转换为18位
		    if($cardLength == 15){
		        $sBirthday = '19' . substr($idcard, 6, 2) . '-' . substr($idcard, 8, 2) . '-' . substr($idcard, 10, 2);
		        try {
			        $d = new DateTime($sBirthday);
			        $dd = $d->format('Y-m-d');
			    } catch (Exception $e) {
			    	$passed = false;
			    }

		        $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);	//15to18
		        $Bit18 = $this->getVerifyBit($idcard);	//算出第18位校验码
		        $idcard = $idcard . $Bit18;
		    }

		    // 判断是否大于2078年，小于1900年
		    $year = substr($idcard, 6, 4);
		    if ($year < 1900 || $year > 2078){
		        $passed = false;
		    }

		    //18位身份证处理
		    $sBirthday = substr($idcard, 6, 4) . '-' . substr($idcard, 10, 2) . '-' . substr($idcard, 12, 2);
		    try {
		        $d = new DateTime($sBirthday);
		        $dd = $d->format('Y-m-d');
		    } catch (Exception $e) {
		    	$passed = false;
		    }

		    if($sBirthday != $dd){
		        $passed = false;
		    }

		    //身份证编码规范验证
		    $idcard_base = substr($idcard, 0, 17);
		    if(strtoupper(substr($idcard, 17, 1)) != $this->getVerifyBit($idcard_base)){
				$passed = false;
		    }
		}

	    if(!$passed) {
	    	$this->warning($value . '格式有误');
	    	return false;
	    }
	    else return true;
	}

	/* 计算身份证校验码，根据国家标准GB 11643-1999 */
	protected function getVerifyBit($idcard_base){
	    if(strlen($idcard_base) != 17){
	        return false;
	    }

	    //加权因子
	    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	    //校验码对应值
	    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

	    $checksum = 0;
	    for($i = 0; $i < strlen($idcard_base); $i++){
	        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
	    }

	    $mod = $checksum % 11;
	    $verify_number = $verify_number_list[$mod];

	    return $verify_number;
	}

}

?>