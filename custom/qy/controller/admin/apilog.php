<?php

/**
 * omeapi开放接口接收类
 * @copyright shopex.cn
 * @author Mr.dong
 */
class qy_ctl_admin_apilog extends desktop_controller
{


	/**
	 * @var apiactionlog_mdl_apilog
	 */
	public $model;

	/**
	 * @var qy_request
	 */
	public $worker;

	public function __construct(){
		$this->model = app::get('apiactionlog')->model('apilog');
		$this->worker = kernel::single('qy_request');
	}


	function re_request($log_id){

		$this->begin();
		$row = $this->model->getRow('*', array('apilog_id' => $log_id));

		$data = unserialize($row['params']);

		$rs = $this->worker->call($row['worker'], $data, '', $msg, $log_id);

		$this->end($rs, $msg);
	}



}