<?php

class qy_request extends qy_api
{

	public $call_url = 'http://218.106.92.66:8138/drug-web/online/';

	public $call_list = array(
		'qy.order.create' => array('title' => '订单创建', 'worker' => 'createOrder.do'),
		'qy.order.pay' => array('title' => '订单支付', 'worker' => 'payOrder.do'),
	);


	public function call($method, $data, $log_name, &$rs_msg = '', $apilog_id = false){

		$db_status = $this->log->db->exec('start transaction');

		$json_str = json_encode($data);

		$query_params = array(
			'jsonStr' => $json_str
		);

		$headers['Content-type'] = 'application/x-www-form-urlencoded; charset=utf-8';
		$this->timeout = 10;

		$method_path = $this->call_list[$method];
		if(!$method_path){
			$rs_msg = '接口不存在!';
			return false;
		}

		$url = $this->call_url.$method_path['worker'];
		$sign = $this->sign->get_sign($json_str);
		$url .= '?signature='.urlencode($sign);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/x-www-form-urlencoded;charset=utf-8'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query_params));
		$rs = curl_exec($ch);


		$is_re = false;
		if(!$apilog_id){
			$apilog_id = $this->add_log($method, $data, $log_name);
		}else{
			$is_re = true;
		}


		if($rs === false){
			$rs_msg = '接口请求失败';
			$succ = false;
		}else{
			$rs_data = json_decode($rs);
			$succ = $rs_data->rspCode == 1 ? true : false;
			$rs_msg = $rs_data->rspMsg;
		}

		if($apilog_id){
			$this->update_log_status($apilog_id, $succ, $rs_msg, $is_re);
		}

		$this->log->db->commit($db_status);
		return $rs ? true : false;
	}


	public function add_log($method, $params, $log_name){
		$time = time();
		$data = array(
			//'task_name' => $this->call_list[$method]['title'],
			'task_name' => $log_name,
			'status' => 'running',
			'api_type' => 'request',
			'worker' => $method,
			'params' => serialize($params),
			'log_type' => 'qy',
			'createtime' => $time,
			'last_modified' => $time,
			'calltime' => $time,
		);
		return $this->log->insert($data);
	}



	public function update_log_status($id, $succ, $msg, $is_re){

		$table_name = $this->log->table_name(1);

		$sql = ' update '.$table_name.' set ';
		$data = array(
			'status' => $succ ? 'success' : 'fail',
			'last_modified' => time(),
			'msg' => $msg
		);

		foreach($data as $key => $value){
			$sql .= ' '.$key.'="'.$value.'",';
		}

		$sql = substr($sql, 0, -1);

		if($is_re){
			$sql .= ',retry=retry+1 ';
		}

		$sql .= ' where apilog_id = '.$id;
		return $this->log->db->exec($sql);
	}

}
