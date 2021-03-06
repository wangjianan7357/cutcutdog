<?php

function callback($json) {
	$message = array(
		1 => '註冊失敗',
		2 => '賬號已存在',
		3 => '賬號或密碼有誤',
		4 => '提交失敗',
		5 => '查詢失敗',
		6 => '賬號不存在',
	);

	if ($json['error']) {
		$json['message'] = $message[$json['error']];
	}

	echo json_encode($json);
	exit;
}

function checkMember($member) {
	global $my_db;

	$condition = array(
        'name' => $member['name'],
        'id' => intval($member['id']),
    );

    if ($my_db->fetchOne('member', $condition)) {
    	return true;
    } else {
    	callback(array('error' => 6));
    }
}