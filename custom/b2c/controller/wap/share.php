<?php
/**
 * Created by Test, 2015/09/21 11:09.
 * @author Y.C.G.
 *
 * Copyright (c) 2013 ycg. All rights reserved.
 */

class b2c_ctl_wap_share extends wap_frontpage{

    function __construct(&$app){
        parent::__construct($app);
    }

    public function index($share_id, $share_member_id, $order_id = ''){
        if (!empty($_GET)) {
            $res = explode('_', $_GET['m']);
            $share_member_id = $res['0']; //分享人
            $order_id = $res['1'];
        }

        $this->pagedata['share_id'] = $share_id;
        $this->pagedata['share_member_id'] = $share_member_id;
        $this->pagedata['order_id'] = $order_id;

        $bind = app::get('weixin')->model('bind')->getRow('*', array('status' => 'active'));
        if (!$_SESSION['weixin_u_openid']) {
            header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $bind['appid'] . "&redirect_uri=" . urlencode(kernel::base_url(1) . '/index.php/wap/share-' . $share_id . '.html?m=' . $share_member_id . '_' . $order_id) . "&response_type=code&scope=snsapi_base&state=" . $bind['eid'] . "#wechat_redirect");
        } else {
            $openid = $_SESSION['weixin_u_openid'];
        }

        $objMembers = app::get('weixin')->model('members');
        $objDraw = app::get('weixin')->model('draw');

        if (!$member_info = $objMembers->getRow('*', array('openid' => $openid))) {
            $info = kernel::single('weixin_wechat')->get_basic_userinfo($bind['id'], $openid);
            $this->pagedata['info'] = $info;

            $weixin_member_info = array(
                'openid' => $openid,
                'nickname' => $info['nickname'],
                'sex' => $info['sex'],
                'imgurl' => $info['headimgurl'],
                'subscribe_time' => $info['subscribe_time'],
            );
            $objMembers->save($weixin_member_info);
        } else {
            $this->pagedata['info'] = $member_info;
        }

        $list = $objDraw->getList('*', array('share_id' => $share_id, 'share_member_id' => $share_member_id, 'share_order_id' => $order_id), 0, -1, 'createtime DESC');
        foreach ($list as $k => $v) {
            $list[$k]['get_money'] = substr($v['get_money'], 0, strlen($v['get_money']) - 1);
            $list[$k]['info'] = $objMembers->getRow('*', array('openid' => $v['openid']));
            if ($v['openid'] == $openid) {
                $this->pagedata['member'] = $list[$k];
            }
        }

        $objPrompt = app::get('weixin')->model('share_prompt');
        $share_prompt = $objPrompt->getRow('*', array('default' => '1'));
        if (empty($share_prompt)) {
            $share_prompt = $objPrompt->getRow('*', array(), 0, -1, 'order_by ASC');
        }
        $prompt = unserialize($share_prompt['values']);
        foreach ($prompt as $k => $v) {
            foreach ($list as $k1 => $v1) {
                if ($v1['get_money'] >= $v['min'] && $v1['get_money'] < $v['max']) {
                    $list[$k1]['title'] = $v['title'];
                }
            }
        }

        $count = count($list);
        if ($count >= 5) {
            $height = 300;
            $this->pagedata['height'] = $height;
        }

        $this->pagedata['list'] = $list;
        $this->pagedata['openid'] = $openid;
        $this->pagedata['share'] = app::get('weixin')->model('share')->getRow('*', array('share_id' => $share_id));
        $this->set_tmpl('share');
        $this->page('wap/share/index.html');
    }

    /**
     * 点击链接获得红包
     */
    public function get_redpacket(){
        $share_member_id = $_POST['share_member_id'];
        $data['share_order_id'] = $order_id = $_POST['order_id'];
        $data['share_id'] = $share_id = $_POST['share_id'];
        $data['openid'] = $openid = $_POST['openid'];
        $msg = '';

        $objShare = app::get('weixin')->model('share');
        $objDraw = app::get('weixin')->model('draw');
        $this->begin(array('app' => 'b2c', 'ctl' => 'wap_share', 'act' => 'index', 'arg0' => $share_id, 'arg1' => $share_member_id, 'arg2' => $order_id));
        if (!$activity = $objShare->check_data($data, $msg)) {
            $this->end(false, $msg);
        }

        if ($member_id = kernel::single('b2c_user_object')->get_member_id()) {
            $data['member_id'] = $member_id;
        }
        $data['get_money'] = $objShare->get_redpacket($activity['down_limit'], $activity['top_limit']);
        $data['validity'] = $activity['validity'];
        $data['is_use'] = '0';
        $data['share_member_id'] = $share_member_id;
        $data['createtime'] = time();
        if ($objDraw->save($data)) {
            $objShare->update_share($share_id, $data['get_money'], $activity['get_count'] + 1);
            $this->end(true, app::get('weixin')->_('领取红包成功！'));
        } else {
            $this->end(false, app::get('weixin')->_('领取红包失败！'));
        }
    }

}