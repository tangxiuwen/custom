<?php
/**
 * Created by Test, 2016/04/30 22:16.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_rpc_request_pay extends qy_request{

	public function __construct(){
		parent::__construct();
	}


	/**订单支付
	 * @param $order_id 	订单号
	 * @param $card_id  	泉依卡号
	 * @param $password		密码
	 * @param $money		金额
	 * @param $msg			返回信息
	 * @return bool			支付是否成功
	 */
	public function pay($order_id, $card_id, $password, $money, &$msg = ''){
		$data = array(
			'merchantId' => $this->merchantId,  //商户编号
			'orderId' => $order_id,  //商品订单号
			'cardNo' => $card_id,	//泉依卡号
			'password' => $password,  //支付密码
			'qyCardAmt' => $money, //泉依卡支付金额
			'orderPayDate' => date('Ymd'),  //支付日期
			'orderPayTime' => date('YmdHis')  //支付时间
		);

		$rs = $this->call('qy.order.pay', $data, '订单'.$order_id.'支付'.$money.'元卡号('.$card_id.')', $msg);
		return $rs;

	}

	public function test(){
		$rs = $this->pay('11111', '2222', '2222', '20');
		var_dump($rs);
	}


}