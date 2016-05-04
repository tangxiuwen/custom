<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class b2c_tasks_order_cancel extends base_task_abstract implements base_interface_task
{

    function exec($params = null)
    {
        $cancel_mdl = app::get('b2c')->model('orders');
        $cancel_time_system = app::get('b2c')->getConf('system.order.cancel');
        $cancel_time = time() - $cancel_time_system * 3600;
        $orders = $cancel_mdl->getList("order_id", array('createtime|sthan' => $cancel_time,'pay_status' => '0', 'status' => 'active','ship_status' => '0','payment|noequal' => '-1'));
        $this->cancel_orders($orders);
    }

    function cancel_orders($orders)
    {
        $this->app = app::get('b2c');
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path' => 'b2c_order_checkorder'));
        $mdl_order_cancel_reason = app::get('b2c')->model('order_cancel_reason');
        $b2c_order_cancel = kernel::single("b2c_order_cancel");
        foreach ($orders as $key => $val) {
            $oid = $val['order_id'];
            $o_reason = '过期自动取消';
            $o_type = $val['promotion_type'];
            if ($obj_checkorder->check_order_cancel($oid)) {
                $sdf['order_id'] = $oid;
                $sdf['op_id'] = 1;
                $sdf['opname'] = 'admin';
                $sdf['account_type'] = 'shopadmin';
                $order_cancel_reason = array(
                    'order_id' => $oid,
                    'reason_type' => 7,
                    'reason_desc' => $o_reason,
                    'cancel_time' => time(),
                );

                if ($b2c_order_cancel->generate($sdf, $this, $message)) {
                    $mdl_order_cancel_reason->save($order_cancel_reason);
                    if ($order_object = kernel::service('b2c_order_rpc_async')) {
                        $order_object->modifyActive($sdf['order_id']);
                    }
                }
            }
        }
    }
}