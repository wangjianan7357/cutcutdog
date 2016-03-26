<?php
/**
 * 邮件处理
 */

class Emailer {
	protected $error;
	protected $setting;
	protected $status;
	protected $fields;

	public $mail;

	function __construct($setting = array()){
		$this->status = 1;

		if (empty($setting)) {
			$mail_setting = explode(',', systemConfig('mail_setting'));
			$this->setting['host'] = $mail_setting[0];
			$this->setting['name'] = $mail_setting[1];
			$this->setting['pass'] = $mail_setting[2];
		}
		else {
			$this->setting = $setting;
		}

		require_once('mail/phpmailer.php');
		$this->mail = new PHPMailer(true);

		$this->mail->IsSMTP();
		$this->mail->SMTPDebug  = 0;
		$this->mail->CharSet 	= 'UTF-8';
		$this->mail->Encoding 	= 'base64';

		//Ask for HTML-friendly debug output
		$this->mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$this->mail->Host       = $this->setting['host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$this->mail->Port       = $this->setting['port'] ? $this->setting['port'] : 25;
		//Whether to use SMTP authentication
		$this->mail->SMTPAuth   = true;
		//Username to use for SMTP authentication
		$temp = explode('@', $this->setting['name']);
		//$this->mail->Username   = $temp[0];
		$this->mail->Username   = $this->setting['name'];
		//Password to use for SMTP authentication
		$this->mail->Password   = $this->setting['pass'];
		//Set who the message is to be sent from
		$this->mail->SetFrom($this->setting['name']);
		//Set an alternative reply-to address
		//$this->mail->AddReplyTo('replyto@example.com','First Last');

		$this->mail->From = $this->setting['name'];

		$this->mail->IsHTML(true);

		$this->setFields();
	}

	public function setFields($fields = array()){
		$this->fields = array(
			'subject' => '主题',
			'msg' => '内容',
			'name' => '联系人',
			'company' => '公司',
			'address' => '地址',
			'email' => '邮箱',
			'telephone' => '电话',
		);

		$this->fields = array_merge($this->fields, $fields);
	}

	public function content($data = array()){
		$this->mail->Subject = $data['subject'];
		$this->mail->FromName = $data['name'];
		
		$html = '
			<table width="95%" border="0" cellspacing="2" cellpadding="4">
				<tbody align="left">
					<tr>
						<td colspan="2" style="height:30px; padding:0 0 0 20px; background:#288fd7; color:white;"><strong>客户留言：</strong></td>
					</tr>';

		$i = 0;
		foreach ($this->fields as $key => $value) {
			$html .= '
					<tr bgcolor="#' . ($i % 2 ? 'ebeff1' : 'e8f2f8') . '">
						<td width="20%" align="right">' . $value . '</td>
						<td width="80%">' . $data[$key] . '</td>
					</tr>';
			$i++;
		}

		$html .= '
				</tbody>
			</table><br />
			<strong>邮件来自: <a href="http://' . $_SERVER['SERVER_NAME'] . '/">' . $_SERVER['SERVER_NAME'] . '</a></strong>';
		
		$this->mail->WordWrap = 80;
		$this->mail->MsgHTML($html);
	}

	public function clear(){
		$this->mail->ClearAddresses();
	}

	/**
	 * 发送邮件
	 */
	public function send($email = null){
		$email = $email !== null ? $email : systemConfig('receive_email');
		$mail_arr = explode(';', $email);

		foreach ($mail_arr as $key => $value) {
			$this->mail->AddAddress($value);
		}

		if ($this->status == 1) {
			try {
				$this->mail->Send();
				return 1;
			}
			catch (phpmailerException $e){
				$this->error = $e;
				return 0;
			}
		} else {
			return $this->status;
		}
	}

	public function showError() {
		return $this->error;
	}

	/*
	 * 检测内容是否垃圾信息
	 */
	static public function filter($fields = array()){
		$point = 0;
		foreach ($fields as $key => $value) {
			// 如果有一个链接，记一分
			preg_match_all('/http:\/\//', $value, $match);
			if(!empty($match[0])) {
				$point ++;
			}

			// 如果单词中间有大写，记一分
			preg_match_all('/\b[A-Za-z]+[A-Z]\b/', $value, $match);
			if(!empty($match[0])) {
				$point ++;
			}
		}

		if ($point > 4) {
			return false;
		}
		else {
			return true;
		}
	}

}

?>