<?php
/**
 * Created by Test, 2016/04/30 22:16.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_rpc_reponse_drug extends qy_reponse{


	public function __construct(){
		parent::__construct();
	}

	public function update($data, &$msg){
		$sdf = array(
			'is_simple' => true,
			'name' => '测试',
			'brief' => '简介',
			'num' => 0,

		);
		$rs = $this->add($sdf, $msg);

		return $this->msg->set_msg($msg, $rs);
	}


	 /**
     * 添加一个商品
     * @param mixed sdf结构
     * @param object handle object
     * @return mixed 返回增加的结果
     */
    public function add(&$sdf, &$msg = '')
    {
        //请求数据验证合法有效性
        if(!$this->_checkInsertData($sdf,$msg)){
            return false;
        }

        $db = kernel::database();
        $transaction_status = $db->beginTransaction();

        //判断简单商品还是多货品商品数据处理
		$goods_id = $this->simple_goods_update($sdf,$msg);

        if (!$goods_id)
        {
            $db->rollback();
            return false;
        }

        $db->commit($transaction_status);

        /** 得到所有的sku_id **/
        $obj_product = app::get('b2c')->model('products');
        $tmp_products = $obj_product->getList('product_id,bn',array('goods_id'=>$goods_id));
        $str_sku_bns = "";
        $str_sku_ids = "";
        foreach ((array)$tmp_products as $arr_product)
        {
            $str_sku_ids .= $arr_product['product_id'] . ",";
            $str_bns .= $arr_product['bn'] . ",";
        }
        if ($str_sku_ids)
            $str_sku_ids = substr($str_sku_ids, 0, strlen($str_sku_ids)-1);
        if ($str_bns)
            $str_bns = substr($str_bns, 0, strlen($str_bns)-1);

        /** 获取商品修改时间 **/
        $obj_goods = app::get('b2c')->model('goods');
        $tmp_goods = $obj_goods->getList('last_modify',array('goods_id'=>$goods_id));

        return array('iid'=>$goods_id, 'sku_id'=>$str_sku_ids, 'sku_bn'=>$str_bns, 'created'=>date('Y-m-d H:i:s',$tmp_goods[0]['last_modify']));
    }


	private function _checkInsertData(&$sdf, &$msg=''){

		if (!$sdf['name']){
            $msg = app::get('b2c')->_('商品名称不能为空，必要参数！');
            return false;
        }

        if(!isset($sdf['is_simple'])){
            $msg = app::get('b2c')->_('是否简单商品不能为空，必要参数！');
            return false;
        }

        if(isset($sdf['brief']) && $sdf['brief'] && strlen($sdf['brief'])>210){
            $msg = app::get('b2c')->_('简短的商品介绍,请不要超过70个字！');
            return false;
        }

        if(isset($sdf['brief']) && $sdf['num'] < 0){
            $msg = app::get('b2c')->_('商品库存数量必须是大于等于零！');
            return false;
        }
        return true;
    }


    private function _checkUpdateData(&$sdf, &$msg=''){
        if (!$sdf['iid']){
            $msg = app::get('b2c')->_('商品ID不能为空，必要参数！');
            return false;
        }

        if (!$sdf['name']){
            $msg = app::get('b2c')->_('商品名称不能为空，必要参数！');
            return false;
        }

        if(!isset($sdf['is_simple'])){
            $msg = app::get('b2c')->_('参数是否简单商品不能为空，必要参数！');
            return false;
        }

        if(isset($sdf['brief']) && $sdf['brief'] && strlen($sdf['brief'])>210){
            $msg = app::get('b2c')->_('简短的商品介绍,请不要超过70个字！');
            return false;
        }

        if(isset($sdf['brief']) && $sdf['num']>=0){
            $msg = app::get('b2c')->_('商品库存数量必须是大于等于零！');
            return false;
        }
        return true;
    }


	private function simple_goods_update(&$sdf, &$msg='')
    {

        //格式化传入参数
        $time = time();
        $tmp['bn'] = $sdf['bn'] ? $sdf['bn'] : '';
        $tmp['type_id'] = $sdf['type_id'] ? $sdf['type_id'] : '1';
        $tmp['cat_id'] = $sdf['cat_id'] ? $sdf['cat_id'] : '0';
        $tmp['brand_id'] = $sdf['brand_id'] ? $sdf['brand_id'] : '';
        $tmp['score'] = $sdf['score'] ? $sdf['score'] : '';
        $tmp['unit'] = $sdf['unit'] ? $sdf['unit'] : '';
        $tmp['brief'] = $sdf['brief'] ? $sdf['brief'] : '';
        $tmp['marketable'] = ($sdf['approve_status'] == 'onsale')? 'true' : 'false';
        $tmp['description'] = $sdf['description'] ? $sdf['description'] : '';
        $tmp['store'] = $sdf['num'] ? $sdf['num'] : '';
        $tmp['price'] = $sdf['price'] ? $sdf['price'] : '0.000';
        $tmp['cost'] = $sdf['cost'] ? $sdf['cost'] : '0.000';
        $tmp['mktprice'] = $sdf['mktprice'] ? $sdf['mktprice'] : '0.000';
        $tmp['weight'] = $sdf['weight'] ? $sdf['weight'] : '0.000';

        //组织商品基础数据
        $sdf_goods = array(
            'bn'=>$tmp['bn'],
            'name'=>$sdf['name'],
            'type'=>array(
                'type_id'=>$tmp['type_id'],
            ),
            'category'=>array(
                'cat_id'=>$tmp['cat_id'],
            ),
            'brand'=>array(
                'brand_id'=>$tmp['brand_id'],
            ),
            'uptime'=>$time,
            'last_modify'=>$time,
            'gain_score'=>$tmp['score'],
            'unit'=>$tmp['unit'],
            'brief'=>$tmp['brief'],
            'status'=>$tmp['marketable'],
            'thumbnail_pic'=>'',
            'description'=>$tmp['description'],
            //'store'=>$tmp['store'],
        );
        /** 简单商品生成一个货品信息 **/
            $sdf_goods['product'] = array(
                array(
                    'bn' => $tmp['bn'],
                    'price'=>array(
                            'price'=>array(
                                'price'=>$tmp['price'],
                            ),
                            'cost'=>array(
                                'price'=>$tmp['cost'],
                            ),
                            'mktprice'=>array(
                                'price'=>$tmp['mktprice'],
                            ),
                        ),
                    'name' => $sdf['name'],
                    'weight'=>$tmp['weight'],
                    'unit' => $tmp['unit'],
                    'store'=>$tmp['store'],
                    'freez' => '0',
                    'status'=>$tmp['marketable'],
                    'default'=>'1',
                    'goods_type' => 'normal',
                    'uptime' => $time,
                    'last_modify' => $time,
                ),
            );

        //判断是否是已有商品
        if ($sdf['iid']){
            $sdf_goods['goods_id'] = $sdf['iid'];
            $sdf_goods['product'][0]['goods_id'] = $sdf['iid'];
        }

        /** 图片处理 接收远程img地址进行处理**/
        if ($sdf['image_default_id'])
        {
            $mdl_img = app::get('image')->model('image');
            $image_name = substr($sdf['image_default_id'], strrpos($sdf['image_default_id'],'/')+1);
            $image_id = $mdl_img->store($sdf['image_default_id'],null,null,$image_name);
            $sdf_goods['image_default_id'] = $image_id;
        }

        /** 商品属性列表 根据商品类型获取属性props_id以及属性值props_value_id来设置商品属性**/
        if ($sdf['props'] && $tmp['type_id']>1)
        {
            $arr_props = explode(';',$sdf['props']);
            if (count($arr_props)>0)
            {
                $obj_goods_type_props = app::get('b2c')->model('goods_type_props');
                $obj_goods_type_props_value = app::get('b2c')->model('goods_type_props_value');
                $p_index = 1;
                foreach ((array)$arr_props as $key=>$value)
                {
                    $tmp_arr_props = explode(':',$value);
                    $tmp_props = $obj_goods_type_props->getList('props_id', array('type_id'=>$tmp['type_id'],'props_id'=>$tmp_arr_props['0']));
                    if (!$tmp_props)
                    {
                        $msg = app::get('b2c')->_('当前添加的商品类型下不存在该商品属性！');
                        return false;
                    }
                    $tmp_props_value = $obj_goods_type_props_value->getList('props_value_id', array('props_id'=>$tmp_arr_props['0'],'props_value_id'=>$tmp_arr_props['1']));
                    if (!$tmp_props_value)
                    {
                        $msg = app::get('b2c')->_('当前添加的商品属性下不存在该商品属性值！');
                        return false;
                    }

                    $sdf_goods['props']['p_'.$p_index]['value'] = $tmp_props_value[0]['props_value_id'];
                    $p_index++;
                }
            }
        }

        /** 用户自行输入的类目属性 **/
        if ($sdf['input_pids'] && $sdf['input_str'] && $tmp['type_id']>1)
        {
            $arr_input_pids = explode(';',$sdf['input_pids']);
            $arr_input_strs = explode(';',$sdf['input_str']);

            if(count($arr_input_pids)>0){
                $obj_goods_type_props = app::get('b2c')->model('goods_type_props');

                foreach ((array)$arr_input_pids as $key=>$pid){
                    $tmp_input_props = $obj_goods_type_props->getList('props_id', array('type_id'=>$tmp['type_id'],'props_id'=>$pid));
                    if (!$tmp_input_props)
                    {
                        $msg = app::get('b2c')->_('需要添加的商品类型下不存在该自行输入属性！');
                        return false;
                    }
                }

                if (count($arr_input_pids) == count($arr_input_strs) )
                {
                    $p_input_id = 21;
                    foreach ((array)$arr_input_strs as $key=>$input_value)
                    {
                        $sdf_goods['props']['p_'.$p_input_id]['value'] = $input_value;
                        $p_input_id++;
                    }
                }
            }
        }

        $obj_goods = app::get('b2c')->model('goods');
        $goods_id = $obj_goods->save($sdf_goods);
        if (!$goods_id)
        {
            $msg = app::get('b2c')->_('商品添加失败！');
            return false;
        }

        if(!$this->goods_related_items($sdf,$goods_id)){
            $msg = app::get('b2c')->_('商品关联商品信息添加失败！');
            return false;
        }

        if(!$this->goods_keywords($sdf,$goods_id)){
            $msg = app::get('b2c')->_('商品关键字添加失败！');
            return false;
        }


        return $sdf_goods['goods_id'];
    }


	private function goods_related_items(&$sdf, $goods_id){
        // 生成推荐商品
        if ($sdf['related_items'])
        {
            $arr_related_items = json_decode($sdf['related_items'], 1);
            if ($arr_related_items)
            {
                $obj_goods_rate = app::get('b2c')->model('goods_rate');
                foreach ((array)$arr_related_items as $related_item)
                {
                    $filter = array(
                        'filter_sql'=>'(`goods_1`="'.$goods_id.'" AND `goods_2`="'.$related_item.'") OR (`goods_1`="'.$related_item.'" AND `goods_2`="'.$goods_id.'")',
                    );
                    $tmp = $obj_goods_rate->getList('*',$filter);
                    if (count($tmp) == 2)
                    {
                        // 当前商品与关联商品已经存在相互绑定，不做任何处理.
                    }
                    elseif (count($tmp) ==1)
                    {
                        //当查询结果只有一条时，判断是否是当前商品发起的商品关联，有就跳出不做处理，没有就设置双向绑定
                        if ($tmp[0]['goods_1'] == $goods_id)
                            continue;

                        $filter = array(
                            'goods_1'=>$tmp[0]['goods_1'],
                            'goods_2'=>$tmp[0]['goods_2'],
                        );
                        $is_save = $obj_goods_rate->update(array('manual'=>'both'),$filter);
                    }
                    else
                    {
                        //如果之前没有关联数据，则创建单向关联关系
                        $data = array(
                            'goods_1'=>$goods_id,
                            'goods_2'=>$related_item,
                            'manual'=>'left',
                        );
                        $is_save = $obj_goods_rate->insert($data);
                    }
                }
            }
            return $is_save;
        }
        return true;
    }


	private function goods_keywords(&$sdf, $goods_id){
        /** 商品关键字 **/
        if ($sdf['keywords'])
        {
            $arr_keywords = json_decode($sdf['keywords'], 1);
            if ($arr_keywords)
            {
                $obj_goods_keywords = app::get('b2c')->model('goods_keywords');
                foreach ((array)$arr_keywords as $str_keywords)
                {
                    $data = array(
                        'goods_id'=>$goods_id,
                        'keyword'=>$str_keywords,
                        'res_type'=>'goods',
                    );
                    $is_save = $obj_goods_keywords->insert($data);
                }
            }
            return $is_save;
        }
        return true;
    }


}