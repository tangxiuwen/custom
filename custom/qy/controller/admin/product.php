<?php

/**
 * omeapi开放接口接收类
 * @copyright shopex.cn
 * @author Mr.dong
 */
class qy_ctl_admin_product extends desktop_controller
{

	public function index()
	{
		$this->finder('qy_mdl_product', array(
			'title' => app::get('qy')->_('理赔产品列表'),

		));
	}


}