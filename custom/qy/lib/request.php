<?php

class qy_request extends qy_api
{


	public $method_list = array(
		'qy.order.create' => array('title' => '订单创建', 'worker' => 'qy_rpc_request_user@create'),
	);


	public $call_url = 'http://218.106.92.66:8143/drug-web/online/';

	public $call_list = array(
		'qy.order.create' => array('title' => '订单创建', 'worker' => 'createOrder.do'),

	);


	public function call($method, $data, &$msg = ''){

		$json_str = json_encode($data);

		$query_params = array(
			'jsonStr' => $json_str
		);

		$headers['Content-type'] = 'application/x-www-form-urlencoded; charset=utf-8';
		$this->timeout = 10;

		$method_path = $this->call_list[$method];
		if(!$method_path){
			$msg = '接口不存在!';
			return false;
		}

		$url = $this->call_url.$method_path['worker'];
		$sign = $this->sign->get_sign($json_str);
		$url .= '?signature='.urlencode($sign);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/x-www-form-urlencoded;charset=utf-8'));
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query_params));
		curl_exec($ch);

		return true;
	}



}
