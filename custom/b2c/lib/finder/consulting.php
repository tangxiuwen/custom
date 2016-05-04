<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_finder_consulting{

    var $column_editbutton = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_editbutton($row){
        $str_operation = '';
        if ($row['status'] != '1'){
            if (!$row['default_lv']) {
                $target = '{onComplete:function(){if (finderGroup&&finderGroup[\'' . $_GET['_finder']['finder_id'] . '\']) finderGroup[\'' . $_GET['_finder']['finder_id'] . '\'].refresh();}}';
                $str_operation = '<a target="' . $target . '" href="index.php?app=b2c&ctl=admin_goods_consulting&act=update_status&_finder[finder_id]=' . $_GET['_finder']['finder_id'] . '&id=' . $row['id'] . '">' . app::get('b2c')->_('确认联系') . '</a>';
            }
        }
        return $str_operation;
    }

}
