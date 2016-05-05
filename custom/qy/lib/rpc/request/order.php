<?php
/**
 * Created by Test, 2016/04/30 22:16.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_rpc_request_order extends qy_request{

	public function __construct(){
		parent::__construct();
	}


	public function create(){
//		$order = array(
//			'merchantId' => '0001', //商户编号
//			'storeNo' => '001', 	//店铺id
//			'orderId' => '',	//商城订单号
//			'cardNo' => '622229991603184006',		//泉依卡卡号
//			'orderAmt' => '',	//订单折后金额
//			'originalAmt' => '',	//订单原金额
//			'orderTime' => '',	//订单时间
//			'orderDate' => '',	//订单日期
//			'remark' => '',	//交易备注
//			'detailList' => array(  		//交易明细
//				array(
//					'outNo' => '',	//商城货品编码
//					'commonName' => '',	//药品名称
//					'spec' => '',	//规格
//					'originalAmt' => '',	//单个药品销售价,元为单位
//					'discountAmt' => '',	//单个药品折后价格，元为单位
//					'num' => '',	//销售数量
//					'detailAmt' => '',	//药品小计金额,元为单位
//				)
//			)
//
//		);


		$order = array(
			'merchantId' => '0001', //商户编号
			'storeNo' => '001', 	//店铺id
			'orderId' => '54898635312',	//商城订单号
			'cardNo' => '622229991603184006',		//泉依卡卡号
			'orderAmt' => '50',	//订单折后金额
			'originalAmt' => '100',	//订单原金额
			'orderTime' => '20160505132850',	//订单时间
			'orderDate' => '20160505',	//订单日期
			'remark' => '测试',	//交易备注
			'detailList' => array(  		//交易明细
				array(
					'outNo' => '67363',	//商城货品编码
					'commonName' => '测试',	//药品名称
					'spec' => '',	//规格
					'originalAmt' => '100',	//单个药品销售价,元为单位
					'discountAmt' => '50',	//单个药品折后价格，元为单位
					'num' => '1',	//销售数量
					'detailAmt' => '50',	//药品小计金额,元为单位
				)
			)

		);

		$rs = $this->call('qy.order.create', $order);

		var_dump($rs);
		die();







	}



}