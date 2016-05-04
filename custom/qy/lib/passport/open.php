<?php
/**
 * Created by Test, 2016/05/01 07:30.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_passport_open{

	/**
	 * @var site_router
	 */
	public $router;

	/**
	 * @var app
	 */
	public $app;

	/**
	 * @var qy_passport_trust
	 */
	public $passport_trust;

	function __construct(&$app){
 		$this->app = $app;
		$this->router = app::get('site')->router();
		$this->passport_trust = kernel::single('qy_passport_trust');
    }


    function callback($params){
		/** @var pam_callback $callback */
        $callback = kernel::single('pam_callback');
        $params['module'] = 'qy_passport_trust';
        $params['type'] = pam_account::get_account_type('b2c');
        $back_url = $this->router->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'index'));
        $params['redirect'] = base64_encode($back_url);
        $result_m = array();
        $callback->login($params);
		die();
        if($result_m['redirect_url']){
            echo "<script>window.location=decodeURIComponent('".$result_m['redirect_url']."');</script>";
            exit;
        }else{
            echo "<script>top.window.location='".$back_url."'</script>";
            exit;
        }
    }



}