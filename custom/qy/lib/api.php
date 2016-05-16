<?php

class qy_api
{

	/**
	 * @var qy_message
	 */
	public $msg;


	/**
	 * @var qy_sign
	 */
	public $sign;

	public $merchantId = '0001';
	public $storeNo = '001';

	/**
	 * @var int
	 */
	public $run_time;

	/**
	 * @var apiactionlog_mdl_apilog
	 */
	public $log;


	public function __construct(){
		$this->msg = kernel::single('qy_message');
		$this->sign = kernel::single('qy_sign');
		$this->log = kernel::single('apiactionlog_mdl_apilog');

		$this->run_time = time();
	}


	public function verify($str, $sign)
	{
		return $this->sign->verify($str, $sign);
	}

	public function get_sign($str){
		return $this->sign->get_sign($str);
	}


}
