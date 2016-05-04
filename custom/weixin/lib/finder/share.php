<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class weixin_finder_share{

    var $column_edit = '操作';

    function column_edit($row){
        return '<a href="index.php?app=weixin&ctl=admin_share&act=edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[share_id]='.$row['share_id'].'" target="_blank">'.app::get('weixin')->_('编辑').'</a>';
    }

}