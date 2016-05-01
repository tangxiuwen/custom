<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['share'] = array(
    'columns' =>
        array(
            'share_id' =>
                array(
                    'type' => 'int(8)',
                    'required' => true,
                    'pkey' => true,
                    'label' => app::get('weixin')->_('活动id'),
                    'editable' => false,
                    'extra' => 'auto_increment',
                    'in_list' => false,
                ),
            'name' =>
                array(
                    'type' => 'varchar(255)',
                    'required' => true,
                    'default' => '',
                    'label' => app::get('weixin')->_('活动名称'),
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                    'filterdefault' => true,
                    'is_title' => true,
                ),
            'from_time' =>
                array(
                    'type' => 'time',
                    'label' => app::get('weixin')->_('起始时间'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                    'filterdefault' => true,
                ),
            'to_time' =>
                array(
                    'type' => 'time',
                    'label' => app::get('weixin')->_('截止时间'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => false,
                    'filterdefault' => true,
                ),
            'top_limit' =>
                array(
                    'type' => 'money',
                    'label' => app::get('weixin')->_('赠送金额上限'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => false,
                    'default_in_list' => false,
                ),
            'down_limit' =>
                array(
                    'type' => 'money',
                    'label' => app::get('weixin')->_('赠送金额下限'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => false,
                    'default_in_list' => false,
                ),
            'deduction_limit' =>
                array(
                    'type' => 'money',
                    'label' => app::get('weixin')->_('抵扣订单金额上限'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'validity' =>
                array (
                    'type' => 'int unsigned',
                    'default' => 0,
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('weixin')->_('有效期（天）'),
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'get_money' =>
                array(
                    'type' => 'money',
                    'label' => app::get('weixin')->_('被领取总金额'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'get_count' =>
                array (
                    'type' => 'int unsigned',
                    'default' => 0,
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('weixin')->_('被领取次数'),
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'get_total_count' =>
                array (
                    'type' => 'int unsigned',
                    'default' => 0,
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('weixin')->_('领取总次数'),
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'operator_id' =>
                array(
                    'type' => 'table:account@pam',
                    'label' => app::get('weixin')->_('设置活动管理员'),
                    'default' => 0,
                    'editable' => false,
                    'in_list' => true,
                    'in_list' => true,
                    'default_in_list' => false,
                ),
            'status' =>
                array(
                    'type' => 'bool',
                    'default' => 'false',
                    'required' => true,
                    'label' => app::get('weixin')->_('开启状态'),
                    'in_list' => true,
                    'editable' => false,
                    'filterdefault' => true,
                    'default_in_list' => true,
                ),
            'conditions' =>
                array(
                    'type' => 'money',
                    'default' => 0,
                    'required' => true,
                    'label' => app::get('weixin')->_('使用条件'),
                    'editable' => false,
                ),
            'createtime' =>
                array (
                    'type' => 'time',
                    'label' => app::get('weixin')->_('创建时间'),
                    'editable' => false,
                    'filtertype' => 'time',
                    'filterdefault' => true,
                    'in_list' => true,
                    'default_in_list' => false,
                    'orderby' => true,
                ),
            'image' =>
                array(
                    'type' => 'char(32)',
                    'label' => app::get('weixin')->_('分享图片'),
                    'in_list' => false,
                    'default_in_list' => false,
                ),
            'description' =>
                array(
                    'type' => 'longtext',
                    'label' => app::get('weixin')->_('分享描述'),
                    'editable' => false,
                    'in_list' => false,
                ),
            'm_color' =>
                array(
                    'type' => 'varchar(50)',
                    'default' => '',
                    'label' => app::get('weixin')->_('活动页用户名称颜色'),
                ),
            'l_color' =>
                array(
                    'type' => 'varchar(50)',
                    'default' => '',
                    'label' => app::get('weixin')->_('活动页领取列表背景色'),
                ),
        ),
    'comment' => app::get('weixin')->_('微信分享活动'),
);
