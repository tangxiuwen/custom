<?php
/**
 * 领取列表功能
 */
class weixin_ctl_admin_draw extends desktop_controller{

    function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index(){
        $this->finder('weixin_mdl_draw', array(
            'title' => app::get('weixin')->_('领取列表'),
        ));
    }
}
