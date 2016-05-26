<?php

class Paypal {

	public $api;						// Paypal接口
	public $account;					// Paypal账号
	public $exchange_rate;				// 兑换率
	public $data;						// 订单信息
	public $language;					// 语言信息

	function __construct(){
		global $con_lang_current;

		$this->api = 'https://www.paypal.com/cgi-bin/webscr';
		//$this->api = 'https://www.sandbox.paypal.com/cgi-bin/webscr';	// 测试

		$this->account = 'petchat2016@gmail.com'; //systemConfig('paypal_setting');
		$this->exchange_rate = 1; //systemConfig('exchange_rate');
		$this->language = $con_lang_current;
	}

	function getOrderData($id){
		global $my_db;

		$result = $my_db->fetchOne('order', array('id' => $id));

		$this->data['number'] 	= $result['number'];
		$this->data['amount']	= sprintf("%01.2f", $result['amount'] / $this->exchange_rate);
		$this->data['num']		= 1;
		$this->data['name']		= 'Dnaber auto parts';
	}

	//生成数据信封
	function createForm(){
		$comfirm_url = 'http://' . $_SERVER['SERVER_NAME'] . '/member/pay_success.php';
		$notify_url = 'http://' . $_SERVER['SERVER_NAME'] . '/member/pay_notify.php';

		// PAYPAL 接口
		$form_text = '<form method="post" name="pay_form" action="' . $this->api . '">';

		// 立即购买
		$form_text .= '<input type="hidden" name="cmd" value="_xclick" />';
		//$form_text .= '<input type="hidden" name="bn" value="Wintour_Cart_WPS" />';

		// PayPal账户上的电子邮件地址
		$form_text .= '<input type="hidden" name="business" value="' . $this->account . '" />';

		// 物品名称（或购物车名称）
		$form_text .= '<input type="hidden" name="item_name" value="' . $this->data['name'] . '" />';

		// 用于跟踪付款的可选传递变量。必须是字母数字字符，最多为 127 个字符
		$form_text .= '<input type="hidden" name="item_number" value="' . $this->data['number'] . '" />';

		// 决不会向您的客户显示的可选转递变量。可用于跟踪账单号
		$form_text .= '<input type="hidden" name="invoice" value="' . $this->data['number'] . '" />';

		// 物品的价格（购物车中所有物品的总价格,因为是_Xclick模式）
		$form_text .= '<input type="hidden" name="amount" value="' . $this->data['amount'] . '" />';

		// 物品数量。大于 1 时，会与金额相乘
		$form_text .= '<input type="hidden" name="quantity" value="' . $this->data['num'] . '" />';

		// 送货地址。如果设为 "1"，则不会要求您的客户提供送货地址。该变量为可选项；如果省略或设为 "0"，将提示您的客户输入送货地址
		$form_text .= '<input type="hidden" name="no_shipping" value="1" />';

		// 定义币种以标示货币变量 值可以为 "USD"、"EUR"、"GBP"、"CAD"、"JPY"。 
		$form_text .= '<input type="hidden" name="currency_code" value="USD" />';
		
		// 字符编码
		$form_text .= '<input type="hidden" name="charset" value="utf-8" />';

		// 您的客户完成付款后将返回的互联网 URL
		$form_text .= '<input type="hidden" name="return" value="' . $comfirm_url . '" />';

		// 您的客户取消付款后将返回的互联网 URL
		$form_text .= '<input type="hidden" name="cancel_return" value="' . $comfirm_url . '" />';

		// 仅与 IPN 一起使用。发送 IPN Form Post 的互联网 URL
		$form_text .= '<input type="hidden" name="notify_url" value="' . $notify_url . '" />';

		// 语言地区
		if($this->language == 'tw' || $this->language == 'cn'){
			$form_text .= '<input type="hidden" name="lc" value="Chinese" />';
			$form_text .= '<input type="hidden" name="locale.x" value="zh_HK" />';

		} else if ($this->language == 'en') {
			$form_text .= '<input type="hidden" name="lc" value="US" />';
			$form_text .= '<input type="hidden" name="locale.x" value="en_GB" />';
		}
		
		// 表单提交
		$form_text .= '</form><script>document.forms["pay_form"].submit();</script>';

		return $form_text;
	}

	//接收支付返回数据
	function notify(){
		global $my_db;

		//将 POST 变量记录在本地变量中
		$data = $_POST;

		$req = 'cmd=_notify-validate';
		foreach ($data as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= '&' . $key . '=' . $value;
		}

		// PAYPAL 验证
		$ch = curl_init($this->api); 
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close')); 
		$curl_result = curl_exec($ch);
		curl_close($ch); 

		//已经通过认证
		if ($data['payer_status'] == 'verified') {
			$order = $my_db->fetchOne('order', array('number' => $data['invoice']));

			//检查付款状态
			if($data['payment_status'] == 'Completed'){
				// 其他字段验证
				if ($data['mc_gross'] == $order['amount'] && $data['invoice'] == $order['number']) {
					$my_db->saveRow('order', array('status' => 2), array('id' => $order['id'], 'number' => $order['number']));
					return true;
				}
			}
		}

		return false;
	}
}

?>