<?php
require('../include/common.php');
require('../include/fun_saas.php');
require('../include/cls_emailer.php');

if ($_REQUEST['action'] == 'send') {

    $member = $my_db->fetchOne('member', array('email' => trim($_POST['email'])));

    if ($member) {
        $submit = array(
            'email' => $_POST['email'],
            'sign' => md5(uniqid()),
            'time' => time(),
            'valid' => 1,
            'content' => '',
            'type' => 1,
        );

        if ($my_db->saveRow('verify', $submit)) {

            $mail = new Emailer($con_mail_set);
            $content = '您好，請打開以下鏈接進行密碼重置：<br>';
            $content .= '<a href="http://' . $_SERVER['SERVER_NAME'] . '/forget.php?token=' . $submit['sign'] . '&m=' . md5($submit['email']) . '">';
            $content .= 'http://' . $_SERVER['SERVER_NAME'] . '/forget.php?token=' . $submit['sign'] . '&m=' . md5($submit['email']) . '</a><br>';
            $content .= '郵件來自：愛寵潮流';

            $mail->resetContent($content, '重置密碼');
            $mail->send($_POST['email']);

            callback(array('error' => 0));
        } else {
            callback(array('error' => 4));
        }

    } else {
        callback(array('error' => 6));
    }

}