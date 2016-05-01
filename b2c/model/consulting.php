<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * mdl_coupon
 *
 * @uses base_db_model
 * @package
 * @version $Id: mdl.coupon.php 2057 2010-04-02 08:38:32Z
 * @copyright
 * @author
 * @license Commercial
 */

class b2c_mdl_consulting extends dbeav_model{

    var $defaultOrder = array('create_time','desc');

    public function __construct($app)
    {
        parent::__construct($app);
        $this->product = $this->app->model('products');
    }

    public function searchOptions()
    {
        $arr = parent::searchOptions();
        $arr = array_merge($arr, array(
            'goods_name' => app::get('b2c')->_('商品名称'),
            'product_bn' => app::get('b2c')->_('货号'),
        ));
        return $arr;
    }

    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null)
    {
        if (!$cols) {
            $cols = $this->defaultCols;
        }
        if (!empty($this->appendCols)) {
            $cols .= ',' . $this->appendCols;
        }

        $orderType = $orderType ? $orderType : $this->defaultOrder;
        $sql = 'SELECT ' . $cols . ' FROM `' . $this->table_name(true) . '` WHERE ' . $this->_filter($filter);
        if ($orderType) $sql .= ' ORDER BY ' . (is_array($orderType) ? implode($orderType, ' ') : $orderType);
        $data = $this->db->selectLimit($sql, $limit, $offset);

        return $data;
    }

    public function _filter($filter, $tableAlias = null, $baseWhere = null){
        if ($filter['goods_name'] || $filter['product_bn']) {
            if ($filter['goods_name']) {
                $gids = app::get('b2c')->model('goods')->getList('goods_id', array('name' => $filter['goods_name']));
                foreach($gids as $v){
                    $goods[] = $v['goods_id'];
                }
                if(empty($goods)){
                    $filter['goods_id'] = '0';
                }else{
                    $goods_filter = array(
                        'goods_id|in' => $goods
                    );
                }
                unset($filter['goods_name']);
                $filter = array_merge($filter,$goods_filter);
            }
            if ($filter['product_bn']) {
                $aData = app::get('b2c')->model('products')->getRow('product_id', array('bn' => $filter['product_bn']));
                unset($filter['product_bn']);
                if(empty($aData)){
                    $filter['product_id'] = '0';
                }else{
                    $filter['product_id'] = $aData['product_id'];
                }
            }
        }

        $filter = parent::_filter($filter);

        return $filter;
    }

    public function modifier_product_id($row){
        $data = $this->product->getRow('bn',array('product_id'=>$row));
        return $data['bn'];
    }
}
