<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class weixin_mdl_share extends dbeav_model
{

    /**
     * 领取红包之后更新活动表
     * @param $share_id 活动id
     * @param $total_money 被领取的金额
     * @param $count 领取的次数
     */
    public function update_share($share_id, $total_money, $count){
        $old_data = $this->getRow('*', array('share_id' => $share_id));
        $data = array(
            'get_money' => $total_money + $old_data['get_money'],
            'get_count' => $count,
        );
        $this->update($data, array('share_id' => $share_id));
    }

    /**
     * 检查活动数据
     * @param $data
     * @param $msg
     * @return mix
     */
    public function check_data($data, &$msg){
        $order_id = $data['share_order_id'];
        $share_id = $data['share_id'];
        $openid = $data['openid'];

        if (empty($openid)) {
            $msg = app::get('weixin')->_('无法获取微信用户openid！');
            return false;
        }

        $objDraw = app::get('weixin')->model('draw');
        $objOrders = app::get('b2c')->model('orders');

        if (!$objOrders->getList('*', array('order_id' => $order_id))) {
            $msg = app::get('weixin')->_('数据异常！');
            return false;
        }

        $filter = array(
            'share_id' => $share_id,
            'from_time|sthan' => time(),
            'to_time|bthan' => time(),
            'status' => 'true',
        );
        $activity = $this->getRow('*', $filter);
        if (empty($activity)) {
            $msg = app::get('weixin')->_('暂无活动！');
            return false;
        }

        if ($objDraw->getRow('*', array('openid' => $openid, 'share_id' => $share_id, 'share_order_id' => $order_id))) {
            $msg = app::get('weixin')->_('您已领取红包！');
            return false;
        }

        $get_count = $activity['get_count']; //红包已领取次数
        $get_total_count = $activity['get_total_count']; //红包总领取次数
        if ($get_count >= $get_total_count) {
            $msg = app::get('weixin')->_('红包已领完，请您期待下一次活动！');
            return false;
        }

        return $activity;
    }

    /**
     * 分享链接得到红包
     * @param $min 最小金额
     * @param $max 最大金额
     * @return int 得到的红包金额
     */
    public function get_redpacket($min, $max){
        $math = kernel::single('ectools_math');
        $rand = mt_rand(1, 1000);
        if ($rand == 1) {
            $money = $max;
        } else if ($rand <= 40) {
            $money = mt_rand($math->number_multiple(array($max, 0.9)), $max - 1);
        } else if ($rand <= 100) {
            $money = mt_rand($math->number_multiple(array($max, 0.7)), $math->number_multiple(array($max, 0.9)));
        } else if ($rand <= 150) {
            $money = mt_rand($math->number_multiple(array($max, 0.4)), $math->number_multiple(array($max, 0.7)));
        } else {
            $money = mt_rand($min, $math->number_multiple(array($max, 0.4)));
        }

        return $money;
    }
}
