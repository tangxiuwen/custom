<?php

/**
 * omeapi开放接口接收类
 * @copyright shopex.cn
 * @author Mr.dong
 */
class qy_ctl_default extends base_controller
{

    /**
     * 默认接收方法
     */
    public function index()
    {
        kernel::single('qy_rpc_service')->response();
    }

}