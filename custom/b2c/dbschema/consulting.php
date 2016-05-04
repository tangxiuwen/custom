<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['consulting'] = array(
    'columns' =>
        array(
            'id' =>
                array(
                    'type' => 'bigint unsigned',
                    'required' => true,
                    'pkey' => true,
                    'extra' => 'auto_increment',
                    'label' => app::get('b2c')->_('ID'),
                    'width' => 110,
                    'hidden' => true,
                    'editable' => false,
                    'in_list' => false,
                ),
            'goods_id' => array(
                'type' => 'table:goods',
                'editable' => false,
                'in_list' => true,
                'default_in_list' => true,
                'width' => 180,
                'label' => app::get('b2c')->_('商品名称'),
                'comment' => app::get('b2c')->_('商品ID '),
            ),
            'product_id' =>
                array(
                    'type' => 'varchar(50)',
                    'label' => app::get('b2c')->_('货品编号'),
                    'width' => 150,
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => app::get('b2c')->_('货品ID '),
                ),
            'member_id' =>
                array(
                    'type' => 'number',
                    'default' => '0',
                    'label' => app::get('b2c')->_('会员用户名'),
                    'width' => 75,
                    'in_list' => false,
                    'default_in_list' => false,
                    'comment' => app::get('b2c')->_('会员ID '),
                ),
            'create_time' =>
                array(
                    'width' => 110,
                    'type' => 'time',
                    'editable' => false,
                    'filtertype' => 'time',
                    'filterdefault' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => app::get('b2c')->_('创建时间'),
                    'label' => app::get('b2c')->_('创建时间'),
                ),
            'contact_time' =>
                array(
                    'width' => 110,
                    'type' => 'time',
                    'editable' => false,
                    'filtertype' => 'time',
                    'filterdefault' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => app::get('b2c')->_('电话时间'),
                    'label' => app::get('b2c')->_('联系时间'),
                ),
            'status' =>
                array(
                    'type' =>
                        array(
                            0 => app::get('b2c')->_('未联系'),
                            1 => app::get('b2c')->_('已联系'),
                        ),
                    'default' => '0',
                    'required' => false,
                    'label' => app::get('b2c')->_('状态'),
                    'width' => 75,
                    'editable' => true,
                    'filtertype' => 'bool',
                    'filterdefault' => 'true',
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'mobile' =>
                array(
                    'type' => 'varchar(20)',
                    'label' => app::get('b2c')->_('手机号'),
                    'width' => 110,
                    'searchtype' => 'has',
                    'in_list' => true,
                    'default_in_list' => true,
                ),

        ),
    'comment' => app::get('b2c')->_('用户商品咨询记录表'),
);
