<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
/*
 * @package site
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */
class site_ctl_admin_sitemaps extends site_admin_controller
{
    /*
     * workground
     * @var string
     */
    var $workground = 'seo_ctl_admin_sitemaps';

    /*
     * ï¿½Ð±ï¿½
     * @public
     */
    public function index(){
    	$shop_base = app::get('site')->router()->gen_url(array('app'=>'site', 'ctl'=>'sitemaps', 'act'=>'catalog', 'full'=>1));
    	$this->pagedata['url'] = $shop_base;
		$this->page('admin/sitemaps/index.html');
    }

    public function baidu(){
        $this->page('admin/sitemaps/baidu.html');
    }

    public function postToBaidu(){
        $this->begin('index.php?app=site&ctl=admin_sitemaps&act=baidu');
        //提交过来的URL
        $url = $url_auto = array();
        if($_POST['url']){
            $url = explode("\n", $_POST['url']);
        }
        //自动生成的链接

        /*PC端导航*/
        $pc_navigation = app::get('site')->model('menus')->getList('*',array('hidden'=>'false'));
        if(!empty($pc_navigation)){
            foreach($pc_navigation as $k => $v){
                if($v['custom_url']){
                    $url_auto[] = $v['custom_url'];
                }else{
                    $params = array('app' => $v['app'], 'ctl' => $v['ctl'], 'act' => $v['act']);
                    if($v['params']){
                        $params['args'] = $v['params'];
                        $url_auto[] = kernel::single('site_router')->gen_url($params);
                    }else{
                        $url_auto[] = kernel::base_url(1).kernel::single('site_router')->gen_url($params);
                    }
                }
            }
        }

        /*PC端商品详情*/
        $pc_pids = app::get('b2c')->model('products')->getList('product_id');
        foreach ($pc_pids as $k => $v) {
            $url_auto[] = kernel::base_url(1) . kernel::single('site_router')->gen_url(array('app' => 'b2c', 'ctl' => 'site_product', 'act' => 'index', 'arg0' => $v['product_id']));
        }

        /*PC端商品列表*/
        $pc_catids = app::get('b2c')->model('goods_cat')->getList('cat_id');
        foreach ($pc_catids as $k => $v) {
            $url_auto[] = kernel::base_url(1) . kernel::single('site_router')->gen_url(array('app' => 'b2c', 'ctl' => 'site_gallery', 'act' => 'index', 'arg0' => $v['cat_id']));
        }

        /*PC端品牌页*/
        $pc_bids = app::get('b2c')->model('brand')->getList('brand_id');
        foreach ($pc_bids as $k => $v) {
            $url_auto[] = kernel::base_url(1) . kernel::single('site_router')->gen_url(array('app' => 'b2c', 'ctl' => 'site_brand', 'act' => 'index', 'arg0' => $v['brand_id']));
        }

        /*PC端文章列表页*/
        $pc_content = app::get('content')->model('article_nodes')->getList('*',array('ifpub' => 'true'));
        foreach($pc_content as $k => $v){
            if ($v['homepage'] != 'true') {
                $url_auto[] = kernel::single('site_router')->gen_url(array('app' => 'content', 'ctl' => 'site_article', 'act' => 'l', 'arg0' => $v['node_id']));
            } else {
                $url_auto[] = kernel::single('site_router')->gen_url(array('app' => 'content', 'ctl' => 'site_article', 'act' => 'i', 'arg0' => $v['node_id']));
            }
        }

        /*PC端文章详情页*/
        $pc_article = app::get('content')->model('article_indexs')->getList('*', array('ifpub' => 'true'));
        foreach ($pc_article as $k => $v) {
            if ($v['platform'] == 'wap') {
                $url_auto[] = app::get('wap')->router()->gen_url(array('app' => 'content', 'ctl' => 'wap_article', 'act' => 'index', 'arg0' => $v['article_id']));
            } else {
                $url_auto[] = app::get('site')->router()->gen_url(array('app' => 'content', 'ctl' => 'site_article', 'act' => 'index', 'arg0' => $v['article_id']));
            }
        }

        $urls = array_merge($url_auto,$url);
        $api = 'http://data.zz.baidu.com/urls?site=www.qyt1902.com&token=cVq7Cc0vJLsJRMPk';
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $this->end(true, app::get('site')->_('提交成功!'));

    }

}//End Class
