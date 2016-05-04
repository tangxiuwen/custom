<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class weixin_mdl_draw extends dbeav_model
{
    public function modifier_member_id($row)
    {
        if (!empty($row)) {
            $data = app::get('pam')->model('members')->getRow('login_account', array('member_id' => $row));
            $row = $data['login_account'];
        } else {
            $row = '';
        }
        return $row;
    }

    public function modifier_order_id($row)
    {
        if (empty($row)) {
            $row = '';
        }
        return $row;
    }
}