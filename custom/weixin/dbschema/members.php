<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['members'] = array(
    'columns' =>
        array(
            'openid' =>
                array(
                    'type' => 'varchar(100)',
                    'default' => '',
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('weixin')->_('微信用户标识'),
                    'in_list' => true,
                    'default_in_list' => false,
                ),
            'nickname' =>
                array(
                    'type' => 'varchar(50)',
                    'label' => app::get('weixin')->_('姓名'),
                ),
            'sex' =>
                array(
                    'type' =>
                        array(
                            0 => app::get('weixin')->_('女'),
                            1 => app::get('weixin')->_('男'),
                            2 => '-',
                        ),
                    'default' => 2,
                    'required' => true,
                    'label' => app::get('weixin')->_('性别'),
                ),
            'imgurl' =>
                array(
                    'type' => 'longtext',
                    'label' => app::get('weixin')->_('头像'),
                ),
            'subscribe_time' =>
                array(
                    'type' => 'time',
                    'label' => app::get('weixin')->_('订阅时间'),
                ),
        ),
    'comment' => app::get('weixin')->_('微信会员信息'),
);
