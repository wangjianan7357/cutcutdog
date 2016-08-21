<?php
$con_posi = 'member';

session_start();

require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');


header('Cache-control: private');

if($_POST['register'] == 'true'){
    $_POST['sbt_email'] = trim($_POST['sbt_email']);

    if (!$_POST['sbt_account']) {
        $err = '請輸入賬號';
    }
    
    if (!$_POST['sbt_pass']) {
        $err = '請輸入密碼';
    }

    if ($_POST['sbt_pass'] != $_POST['sbt_confirm']) {
        $err = '密碼兩次輸入不一致';
    }
    
    
    if (!$_POST['sbt_phone']) {
        $err = '請輸入電話';
    }
    
    if (!$_POST['sbt_email']) {
        $err = '請輸入郵箱';
    }
    
    if ($my_db->existRow('member', array('name' => $_POST['sbt_account']))) {
        $err = '賬號已被使用';
    }

    if(!$err){
        $_POST['sbt_id'] = 100000;
        if ($my_db->existRow('member', array('id' => $_POST['sbt_id']))) {
            $_POST['sbt_id'] = $my_db->selectMax('member') + 1;
        }

        $submit = array(
            'id' => $_POST['sbt_id'],
            'name' => trim($_POST['sbt_account']),
            'pass' => md5(trim($_POST['sbt_pass'])),
            'salt' => '',
            'src' => '',
            'sex' => '',
            'realname' => '',
            'email' => trim($_POST['sbt_email']),
            'address' => trim($_POST['sbt_address']),
            'phone' => trim($_POST['sbt_phone']),
            'type' => 1,
            'fields' => '',
            'desp' => ''
        );

        if ($my_db->saveRow('member', $submit)) {
            $_SESSION['memberid'] = $_POST['sbt_id'];
            
            setcookie('cookie[memberid]', $_POST['sbt_id'], time() + systemConfig('cms_login_time') * 60, '/');
            setcookie('cookie[pass]', $_POST['sbt_pass'], time() + systemConfig('cms_login_time') * 60, '/');
            setcookie('cookie[name]', $_POST['sbt_name'], time() + systemConfig('cms_login_time') * 60, '/');

            header('Location: index.php');
            exit;
        }
        else {
            warning('註冊失敗');
        }       
    }
}

require('head.php');
?>

<div class="title1">
    <div class="txt">註冊</div>
</div>

<div class="mgt10">
    <form method="post" action="register.php">
        <input type="hidden" name="register" value="true">

        <div class="form_item">
            <label>賬號 <span style=" color:red ;">*</span></label>
            <input type="text" class="input_txt" name="sbt_account" placeholder="請輸入賬號">
        </div>
        <div class="form_item">
            <label>密碼 <span style=" color:red ;">*</span></label>
            <input type="password" class="input_txt" name="sbt_pass" placeholder="請輸入密碼">
        </div>
        <div class="form_item">
            <label>確認 <span style=" color:red ;">*</span></label>
            <input type="password" class="input_txt" name="sbt_confirm" placeholder="請確認密碼">
        </div>
        <div class="form_item">
            <label>電話 <span style=" color:red ;">*</span></label>
            <input type="text" class="input_txt" name="sbt_phone" placeholder="請輸入電話">
        </div>
        <div class="form_item">
            <label>郵箱 <span style=" color:red ;">*</span></label>
            <input type="text" class="input_txt" name="sbt_email" placeholder="請輸入郵箱">
        </div>
        <div class="form_item">
            <label>地址 <span style=" color:red ;">*</span></label>
            <input type="text" class="input_txt" name="sbt_address" placeholder="請輸入地址">
        </div>

        <div class="form_item xc">
            <input type="submit" class="btn_login" value="註冊">
            <p><a href="login.php">登錄</a></p>
        </div>
    </form>
</div>

<?php require('foot.php'); ?>