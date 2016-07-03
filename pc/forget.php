<?php
$con_posi = 'member';

session_start();

require_once('include/fun_web.php');
require_once('include/common.php');
require_once('include/initial.php');

$err = '';

if ($_POST['reset'] == 'true') {
    $verify = $my_db->fetchOne('verify', array('sign = "' . $_POST['token'] . '" AND type = 1 AND created > ' . (time() - 1800)));
    if (!$verify) {
        $err = '该链接验证已失效，请重新提交！';
    }

    $chk_post = new ChkRequest('sbt_');
    $chk_post->chkPassword(array('password' => '密码'), array('confirm' => '确认密码'));

    if(!$err){
        $my_db->saveRow('member', array('password' => md5($_POST['sbt_password'])), array('email' => $verify['account']));
        $my_db->deleteRow('verify', array('id' => $verify['id']));

        $msg = '密码修改成功！';
    }

} else if ($_GET['token']) {
    $getdata = $my_db->fetchOne('verify', array('sign = "' . $_GET['token'] . '" AND type = 1 AND created > ' . (time() - 1800)));
    if (!$getdata) {
        $err = '该链接验证已失效，请重新提交！';
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN" class="root61">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="images/member.css" type="text/css" />
<?php
$script = '';
if($err) echo '<script language="javascript">alert("' . $err . '"); history.go(-1); </script>';
if($msg) echo '<script language="javascript">alert("' . $msg . '"); </script>';
if($href) echo '<script language="javascript">document.location.href = "' . $href . '"; </script>';
?>
</head>
<body>
<div class="regist_wrap">	        	
    <div class="regist_tab">
        <ul class="clearfix">
            <li><i class="r_mail"></i><span>找回密码</span></li>
        </ul>
        <p class="cur_tab cur"><em></em></p>
    </div>

    <form class="regist_form mobile_register_form" action="forget.php" method="post">
        <input type="hidden" value="true" name="reset">
        <input type="hidden" value="<?=$_GET['token'];?>" name="code">

        <ul class="clearfix">
            <li>
                <div class="form_item">
                    <label>新密码：</label>
                    <input type="text" name="sbt_password" class="ipt ipt_phone" placeholder="">
                </div>
            </li>
            <li>
                <div class="form_item">
                    <label>确认密码：</label>
                    <input type="text" name="sbt_confirm" class="ipt ipt_phone" placeholder="">
                </div>
            </li>
            <li class="regist_btn">
                <br />
                <button type="button" onclick="javascript:this.form.submit();">提交</button>
            </li>
        </ul>
	</form>
</div>

</body>
</html>