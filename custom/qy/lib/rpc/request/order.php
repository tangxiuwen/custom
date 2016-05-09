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


	/**
	 * @param $order_id		订单id
	 * @param string $msg	返回消息
	 * @return bool			支付是否成功
	 */
	public function create($order_id, &$msg = ''){

		/** @var b2c_mdl_orders $order_model */
		$order_model = app::get('b2c')->model('orders');
		/** @var b2c_mdl_order_items $order_item_model */
		$order_item_model = app::get('b2c')->model('order_items');

		//discount,pmt_goods,pmt_order,total_amount,cost_item,cost_freight
		$order_data = $order_model->getRow('*', array('order_id' => $order_id));
		$order_item_data = $order_item_model->getList('*', array('order_id' => $order_id));

		$real_order_money = $order_data['total_amount']-$order_data['discount']+$order_data['pmt_order']+$order_data['pmt_goods'];

		$order = array(
			'merchantId' => $this->merchantId, //商户编号
			'storeNo' => $this->storeNo, 	//店铺id
			'orderId' => $order_id,	//商城订单号
			'cardNo' => '622229991603184006',		//泉依卡卡号
			'orderAmt' => $order_data['total_amount'],	//订单折后金额
			'originalAmt' => $real_order_money,	//订单原金额
			'orderTime' => date('YmdHis', $order_data['createtime']),	//订单时间
			'orderDate' => date('Ymd', $order_data['createtime']),	//订单日期
			'remark' => $order_data['memo'],	//交易备注
		);

		foreach($order_item_data as $item){
			$item_data = array(
				'outNo' => $item['bn'],    //商城货品编码
				'commonName' => $item['name'],    //药品名称
				'spec' => '',    //规格
				'originalAmt' => $item['price'],    //单个药品销售价,元为单位
				'discountAmt' => $item['price'],    //单个药品折后价格，元为单位
				'num' => '1',    //销售数量
				'detailAmt' => $item['amount'],    //药品小计金额,元为单位
			);
			$order['detailList'][] = $item_data;
		}

		$rs = $this->call('qy.order.create', $order, '订单'.$order_id.'创建', $msg);

		return $rs;
	}


	public function test(){
		$this->create(160430164938779);
	}


}