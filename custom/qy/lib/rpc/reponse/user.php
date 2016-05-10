<?php
/**
 * Created by Test, 2016/04/30 22:16.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_rpc_reponse_user extends qy_reponse{

	/**
	 * @var qy_passport_trust
	 */
	public $open;



	public function __construct(){
		parent::__construct();
		$this->open = kernel::single('qy_passport_trust');
	}


	public function update($data, &$msg){
		$msg = '';

		$format_data = array(
			'provider_openid'	=> $data['userId'], //用户账号
			'provider_code'	=> $data['cardNo'], //泉依卡号
			'realname'	=> $data['productionId'],   //理赔产品id
			'phone'	=> $data['prePhone'],  //用户预留手机号
			'nickname'	=> $data['userName'], //用户姓名
		);

		if($this->open->update($format_data, $msg)){
			return $this->msg->set_msg($msg);
		}

		return $this->msg->set_msg($msg, false);
	}




	public function login($data){
		/** @var qy_passport_open $passport_open_login */
		$passport_open_login = kernel::single('qy_passport_open');
		$passport_open_login->callback($data);

	}

}