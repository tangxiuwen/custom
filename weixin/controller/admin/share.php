<?php
/**
 * 微信分享活动
 */
class weixin_ctl_admin_share extends desktop_controller{

    function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index(){
        $this->finder('weixin_mdl_share', array(
            'title' => app::get('weixin')->_('微信分享活动'),
            'actions' => array(
                array('label' => app::get('b2c')->_('添加微信分享活动'), 'href' => 'index.php?app=weixin&ctl=admin_share&act=add', 'target' => '_blank')
            ),
        ));
    }

    public function add(){
        $this->_editor();
    }

    public function _editor(){
        $this->singlepage('admin/share/basic.html');
    }

    public function toAdd(){
        $this->begin();
        $aData = $this->_prepareRuleData($_POST);
        $objShare = app::get('weixin')->model("share");
        $result = $objShare->save($aData);

        $this->end($result, app::get('b2c')->_('操作成功'));
    }

    private function _prepareRuleData($aData) {
        $aResult = $aData;

        if( !$aResult['name'] ) $this->end( false,'活动名称不能为空！' );
        if( !$aResult['top_limit'] || !$aResult['down_limit']) $this->end( false,'随机赠送虚拟金额不能为空！' );
        if( !$aResult['deduction_limit'] ) $this->end( false,'抵扣订单金额上限不能为空！' );
        if( !$aResult['validity'] ) $this->end( false,'有效期不能为空！' );
        if( !$aResult['get_total_count'] ) $this->end( false,'领取总次数不能为空！' );

        // 开始时间&结束时间
        foreach ($aData['_DTIME_'] as $val) {
            $temp['from_time'][] = $val['from_time'];
            $temp['to_time'][] = $val['to_time'];
        }
        $aResult['from_time'] = strtotime($aData['from_time'] . ' ' . implode(':', $temp['from_time']));
        $aResult['to_time'] = strtotime($aData['to_time'] . ' ' . implode(':', $temp['to_time']));
        if ($aResult['to_time'] <= $aResult['from_time']) $this->end(false, '结束时间不能小于开始时间！');

        // 创建时间 (修改时不处理)
        if(empty($aResult['rule_id'])) $aResult['createtime'] = time();

        //随机赠送虚拟金额
        if ($aResult['top_limit'] <= $aResult['down_limit']) $this->end(false, '最少金额不能小于最大金额！');

        $aResult['operator_id'] = $this->user->user_id;

        return $aResult;
    }

    public function edit($share_id){
        $objShare = app::get('weixin')->model("share");
        $activity = $objShare->getRow('*', array('share_id' => $share_id));
        $this->pagedata['share'] = $activity;
        $this->_editor();
    }
}
