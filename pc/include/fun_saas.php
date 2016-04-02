<?php

function callback($json) {
	$message = array(
		1 => '註冊失敗',
		2 => '賬號已存在',
		3 => '賬號或密碼有誤',
	);

	if ($json['error']) {
		$json['message'] = $message[$json['error']];
	}

	echo json_encode($json);
	exit;
}