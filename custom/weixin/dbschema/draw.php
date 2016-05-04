<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['draw'] = array(
    'columns' =>
        array(
            'draw_id' =>
                array(
                    'type' => 'int(8)',
                    'required' => true,
                    'pkey' => true,
                    'label' => app::get('b2c')->_('领取id'),
                    'editable' => false,
                    'extra' => 'auto_increment',
                    'in_list' => false,
                ),
            'share_id' =>
                array(
                    'type' => 'table:share',
                    'default' => 0,
                    'required' => true,
                    'label' => app::get('b2c')->_('活动名称'),
                    'editable' => false,
                    'in_list' => true,
                ),
            'member_id' =>
                array(
                    'type' => 'table:members@b2c',
                    'default' => 0,
                    'label' => app::get('b2c')->_('领取会员'),
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                    'filterdefault' => true,
                ),
            'openid' =>
                array(
                    'type' => 'varchar(100)',
                    'default' => '',
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('b2c')->_('微信用户标识'),
                    'in_list' => true,
                    'default_in_list' => false,
                ),
            'get_money' =>
                array(
                    'type' => 'money',
                    'label' => app::get('b2c')->_('领取金额'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'validity' =>
                array(
                    'type' => 'int unsigned',
                    'label' => app::get('b2c')->_('有效时间（天）'),
                    'default' => 0,
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'is_use' =>
                array (
                    'type' =>
                        array (
                            0 => app::get('b2c')->_('未使用'),
                            1 => app::get('b2c')->_('已使用'),
                        ),
                    'default' => '0',
                    'required' => true,
                    'label' => app::get('b2c')->_('是否使用'),
                    'width' => 75,
                    'editable' => false,
                    'filtertype' => 'yes',
                    'filterdefault' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'order_id' =>
                array (
                    'type' => 'table:orders@b2c',
                    'default' => 0,
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('b2c')->_('使用订单号'),
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'use_time' =>
                array(
                    'type' => 'time',
                    'label' => app::get('b2c')->_('使用时间'),
                    'editable' => false,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'practical_money' =>
                array(
                    'type' => 'money',
                    'default' => 0,
                    'required' => true,
                    'label' => app::get('b2c')->_('实际使用金额'),
                    'in_list' => true,
                    'editable' => false,
                    'filterdefault' => true,
                    'default_in_list' => true,
                ),
            'share_member_id' =>
                array(
                    'type' => 'table:members@b2c',
                    'required' => true,
                    'default' => 0,
                    'label' => app::get('b2c')->_('分享会员'),
                    'editable' => true,
                    'in_list' => false,
                    'default_in_list' => false,
                ),
            'share_order_id' =>
                array (
                    'type' => 'table:orders@b2c',
                    'default' => 0,
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('b2c')->_('来源订单号'),
                    'in_list' => false,
                    'default_in_list' => false,
                ),
            'createtime' =>
                array(
                    'type' => 'time',
                    'label' => app::get('b2c')->_('领取时间'),
                    'editable' => false,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
        ),
    'comment' => app::get('b2c')->_('领取会员列表'),
);
