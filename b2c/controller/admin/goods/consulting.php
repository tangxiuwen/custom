<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class b2c_ctl_admin_goods_consulting extends desktop_controller
{

    var $workground = 'b2c_ctl_admin_goods_consulting';

    /**
     * 构造方法
     * @params object app object
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index()
    {
        /*$data = array(
    'method' => 'service.store_good_add',
    'sign' => 'YZLMOTVIZWU3YZM4NJY4MWFLYZUZMZE3ZDKYZDQ1OGI=',
    'timestamp' => '2014-07-02 17:34:00',
    'data' => array(
        'goods_bn' => 'bn001',
        'goods_name' => 'goods_name111',
        'goods_type' => 'goods_type222',
        'goods_brand' => 'goods_brand3333',
        'unit' => 'goods_unit444',
        'lastmodify' => '2015-03-26 11:11:12',
        'products' => array(
            'product_bn' => 'product_bn1111',
            'spec_info' => 'spec_info2222',
            'store' => 'store3333',
            'store_freeze' => '44',
            'weight' => '55',
            'cost' => '12.30',
            'price' => '22.80',
            'mktprice' => '39.90',
            'barcode' => '111111111'
        )
    ),
);

        echo json_encode($data);
        exit;*/

        $this->finder('b2c_mdl_consulting', array(
            'title' => app::get('b2c')->_('商品咨询列表'),
            'use_buildin_recycle' => false,
            'use_buildin_filter' => true,
        ));
    }

    public function update_status(){
        $this->begin();
        $data['id'] = $_GET['id'];
        $data['status'] = '1';
        $data['contact_time'] = time();
        if($this->app->model('consulting')->save($data)){
            $this->end(true, app::get('b2c')->_('保存成功'));
        }
        $this->end(false, app::get('b2c')->_('保存失败'));
    }

    /*public function _views()
    {
        $mdl = $this->app->model('consulting');
        $sub_menu = array(
            0 => array('label' => app::get('b2c')->_('未联系'), 'optional' => false, 'filter' => array('status' => array('0'))),
            1 => array('label' => app::get('b2c')->_('已联系'), 'optional' => false, 'filter' => array('status' => array('1'))),
            2 => array('label' => app::get('b2c')->_('全部'), 'optional' => false),
        );

        if (isset($_GET['optional_view'])) $sub_menu[$_GET['optional_view']]['optional'] = false;

        foreach ($sub_menu as $k => $v) {
            if ($v['optional'] == false) {
                $show_menu[$k] = $v;
                if (is_array($v['filter'])) {
                    $v['filter'] = array_merge(array(), $v['filter']);
                } else {
                    $v['filter'] = array();
                }
                $show_menu[$k]['filter'] = $v['filter'] ? $v['filter'] : null;
                if ($k == $_GET['view']) {
                    $show_menu[$k]['newcount'] = true;
                    $show_menu[$k]['addon'] = $mdl->count($v['filter']);
                }
                $show_menu[$k]['href'] = 'index.php?app=b2c&ctl=admin_goods_consulting&act=index&view=' . ($k) . (isset($_GET['optional_view']) ? '&optional_view=' . $_GET['optional_view'] . '&view_from=dashboard' : '');
            } elseif (($_GET['view_from'] == 'dashboard') && $k == $_GET['view']) {
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;
    }*/
}
