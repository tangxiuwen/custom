<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_draw_mem {


    public function __construct() {
        $this->weixin = app::get('weixin');
    }

    public function get_list_m($m_id=0,$nPage=null) {
        if( empty($m_id) ) return false;
        if($nPage){
            $pageLimit = 10;
            $offset = $pageLimit*($nPage-1);
        }else{
            $offset = 0;
            $pageLimit = -1;
        }
        $arr = $this->weixin->model('draw')->getList('*', array('member_id'=>$m_id),$offset,$pageLimit);
        return $arr;
    }
//    public function insertinto(){
//        $data = array('share_id'=>'1','member_id'=>'2','openid'=>'adsa','get_money'=>10,'validity'=>10,'is_use'=>1,'order_id'=>10009009000,'use_time'=>1252547126,'practical_money'=>111,'share_member_id'=>556777);
//        $this->weixin->model('draw')->save($data);
//    }
    public function getRows(){
        $rows = $this->weixin->model('draw')->getList();
        return $rows;
    }

}

