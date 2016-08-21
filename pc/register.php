<?php
$con_posi = 'member';

session_start();

require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');


header('Cache-control: private');

if($_POST['register'] == 'true'){
    $_POST['sbt_email'] = trim($_POST['sbt_email']);

    $chk_post = new ChkRequest('sbt_', $con_lang_current);
    $chk_post->chkExist(array('email' => '電子郵箱', 'phone' => '聯繫電話'), array('name' => 'member'));
    $chk_post->chkFormat(array('email' => '电子邮箱'), 'define:email');
    $chk_post->chkPassword(array('password' => '密码'), array('confirm' => '确认密码'));
    //$chk_post->chkEmpty(array('firstname' => C_FIRST_NAME, 'lastname' => C_LAST_NAME, 'address' => C_ADDRESS));
    
    $_POST['sbt_username'] = $_POST['sbt_email'];

    if(!$err){
        $_POST['sbt_id'] = 100000;
        if ($my_db->existRow('member', array('id' => $_POST['sbt_id']))) {
            $_POST['sbt_id'] = $my_db->selectMax('member') + 1;
        }

        $_POST['sbt_valid'] = 1;
        $_POST['sbt_money'] = 0;
        $_POST['sbt_status'] = 0;
        $_POST['sbt_password'] = md5($_POST['sbt_password']);

        $tmp = array();
        foreach($_POST as $key => $value){
            if(strpos($key, 'sbt_fields_') === 0){
                $tmp[strtr($key, array('sbt_fields_' => ''))] = $value;
            }
        }
        $_POST['sbt_fields'] = json_encode_un($tmp);

        $submit = array();
        $submit_arr = array('id', 'email', 'password', 'username', 'sex', 'mobile', 'country', 'city', 'postcode', 'address', 'money', 'fields', 'status', 'valid');

        for($i = 0; $i < count($submit_arr); $i++){
            if (isset($_POST['sbt_' . $submit_arr[$i]])) {
                $submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
            }
        }

        if ($my_db->saveRow('member', $submit)) {
            $_SESSION['memberid'] = $_POST['sbt_id'];
            
            setcookie('cookie[memberid]', $_POST['sbt_id'], time() + systemConfig('cms_login_time') * 60, '/');
            setcookie('cookie[pass]', $_POST['sbt_pass'], time() + systemConfig('cms_login_time') * 60, '/');
            setcookie('cookie[name]', $_POST['sbt_name'], time() + systemConfig('cms_login_time') * 60, '/');

            header('Location: index.html');
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
    <form method="post" action="login.php">
        <input type="hidden" name="login" value="true">

        <div class="form_item">
            <label>賬號</label>
            <input type="text" class="input_txt" name="sbt_account">
        </div>
        <div class="form_item">
            <label>密碼</label>
            <input type="password" class="input_txt" name="sbt_pass">
        </div>
        <div class="form_item xc">
            <input type="submit" class="btn_login" value="登录">
            <p><a href="login.php">登錄</a></p>
        </div>
    </form>
</div>

<?php require('foot.php'); ?>