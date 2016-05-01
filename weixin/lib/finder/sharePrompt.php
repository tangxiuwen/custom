<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class weixin_finder_sharePrompt{

    var $column_edit = '操作';

    function column_edit($row){
        return '<a href="index.php?app=weixin&ctl=admin_sharePrompt&act=add&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&prompt_id='.$row['prompt_id'].'" target="dialog::{ title:\'' . app::get('weixin')->_('活动金额提示规则') . '\', width:700, height:400}">'.app::get('weixin')->_('编辑').'</a>';
    }

}