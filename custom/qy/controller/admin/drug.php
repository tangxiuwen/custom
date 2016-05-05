<?php

/**
 * omeapi开放接口接收类
 * @copyright shopex.cn
 * @author Mr.dong
 */
class qy_ctl_admin_drug extends desktop_controller
{

	public function index()
	{
		$this->finder('qy_mdl_drug', array(
			'title' => app::get('qy')->_('药品库列表'),
			'actions' => array(
			),
		));
	}


}