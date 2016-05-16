<?php

class qy_reponse extends qy_api
{

	public $method_list = array(
		'user.update' => array('title' => '会员更新/删除/添加', 'worker' => 'qy_rpc_reponse_user@update'),
		'user.login' => array('title' => '会员信任登录', 'worker' => 'qy_rpc_reponse_user@login'),
		'product.update' => array('title' => '理财产品更新/删除/添加', 'worker' => 'qy_rpc_reponse_product@update'),
		'store.drug.update' => array('title' => '药品库更新/删除/添加', 'worker' => 'qy_rpc_reponse_drug@update'),
	);


    /**
     * 数据分发
     * @access public
     * @param String $reponse_name 业务请求标识
     * @param Array  $params 业务数据
     * @param bool   $queue 加入队列,默认flase否,true是
     * @return Array 同步结果
     */
    public function call($params)
    {

		jrl_relog(print_r($_POST, true).$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

		$data_str = $_POST['jsonStr'];
		$data = json_decode($data_str, true);

		$sign = $_GET['signature'];
		$method = $params['method'];
		$method_info = $this->method_list[$method];
		$is_re = false;

		if(empty($data['reqId'])){
			$apilog_id = $this->add_log($method, $data, $method_info['title']);
		}else{
			$filter = array(
				'log_type' => 'qy',
				'api_type' => 'response',
				'msg_id' => $data['reqId']
			);
			$log_info = $this->log->getRow('apilog_id,status', $filter);

			if(empty($log_info)){
				$apilog_id = $this->add_log($method, $data, $method_info['title']);
			}else{
				$is_re = true;
				$apilog_id = $log_info['apilog_id'];
				if($log_info['status'] == 'success'){
					$this->msg->set_msg('成功', true);
					goto END;
				}
			}
		}


		if(!$this->verify($data_str, $sign)){
			$this->msg->set_msg('签名错误', false);
			goto END;
		}


		if(empty($method) || empty($this->method_list[$method])){
			$this->msg->set_msg('接口不存在', false);
			goto END;
		}

        if (empty($method_info)) return false;


        $worker = explode('@', $method_info['worker']);
        $class = $worker[0];
        $method = $worker[1];
        $instance = kernel::single($class);

		$msg = '';
		$rs = $instance->$method($data, $msg);

		END:
		if($apilog_id){
			$this->update_log_status($apilog_id, $this->msg->suc, $this->msg->msg, $is_re);
		}
		$this->msg->send();

    }

	public function add_log($method, $params, $log_name){
		$time = time();
		$data = array(
			'task_name' => $log_name,
			'status' => 'running',
			'api_type' => 'response',
			'worker' => $method,
			'msg_id' => $params['reqId'],
			'params' => serialize($params),
			'log_type' => 'qy',
			'createtime' => $time,
			'last_modified' => $time,
			'calltime' => $time,
		);

		return $this->log->insert($data);
	}


	public function update_log_status($id, $succ, $msg, $is_re = false){

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
