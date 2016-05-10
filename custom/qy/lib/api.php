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


	public function __construct(){
		$this->msg = kernel::single('qy_message');
		$this->sign = kernel::single('qy_sign');
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
