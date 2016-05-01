<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['rx_order'] = array(
    'columns' =>
        array(
            'id' =>
                array(
                    'type' => 'bigint unsigned',
                    'required' => true,
                    'pkey' => true,
                    'extra' => 'auto_increment',
                    'hidden' => true,
                    'editable' => false,
                ),
            'order_id' => array(
                'type' => 'bigint unsigned',
                'editable' => false,
                'comment' => app::get('b2c')->_('订单ID '),
            ),
            'order_info' =>
                array(
                    'type' => 'longtext',
                    'comment' => app::get('b2c')->_('序列化商品信息 '),
                ),
        ),
    'comment' => app::get('b2c')->_('处方药订单信息'),
);
