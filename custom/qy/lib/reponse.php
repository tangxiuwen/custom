<?php

class qy_reponse extends qy_api
{

	public $method_list = array(
		'user.update' => array('title' => '会员更新/删除/添加', 'worker' => 'qy_rpc_reponse_user@update'),
		'user.login' => array('title' => '会员信任登录', 'worker' => 'qy_rpc_reponse_user@login'),
		'fiscal.update' => array('title' => '理财产品更新/删除/添加', 'worker' => 'qy_rpc_reponse_fiscal@update'),
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
		$data = $_POST;

		//$data_str = json_encode($_POST, 256);
		foreach($_POST as &$value){
			$value = urlencode($value);
		}
		$data_str = urldecode(json_encode($_POST));

		$sign = $_GET['signature'];

		//$new_sign = $this->get_sign($data_str);

		if(!$this->verify($data_str, $sign)){
			$this->msg->set_msg('签名错误', false);
			//goto END;
		}

		$method = $params['method'];

		if(empty($method) || empty($this->method_list[$method])){
			$this->msg->set_msg('接口不存在', false);
			goto END;
		}

		$method_info = $this->method_list[$method];
        if (empty($method_info)) return false;
        $worker = explode('@', $method_info['worker']);
        $class = $worker[0];
        $method = $worker[1];
        $instance = kernel::single($class);

		$instance->$method($data);

		END:
		$this->msg->send();

    }

}
