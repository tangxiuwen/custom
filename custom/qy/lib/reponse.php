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

		if(!$this->verify($data_str, $sign)){
			$this->msg->set_msg('签名错误', false);
			goto END;
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

		$msg = '';
		$instance->$method($data, $msg);

		END:
		$this->msg->send();

    }


	public function request($method, $params, $writelog, $async = false, $addon = '', $time_out = 5)
    {

		/** @var apiactionlog_mdl_apilog $oAction_log */
        $oAction_log = app::get('apiactionlog')->model('apilog');
        $log_title = $writelog['log_title'];
        $original_bn = $writelog['original_bn'];
        $log_type = $writelog['log_type'];


        $time_out = 60;
        $log_sdf = $oAction_log->write_log($log_title, $method['method'], $params, $log_type, 'fail', '', $original_bn, $addon, 'request');
        $rpc_callback = array('', '', array('log_id' => $log_sdf['log_id']));


        return $this->rpc_request($method, $params, $rpc_callback, $async, $time_out, $writelog, $addon);
    }

}
