<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 百度钱包支付(百付宝)交易支付网关（国内）
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.payment.baifubao
 */
final class ectools_payment_plugin_bfb extends ectools_payment_app implements ectools_interface_payment_app
{

    /**
     * @var string 支付方式名称
     */
    public $name = '百付宝支付';
    /**
     * @var string 支付方式接口名称
     */
    public $app_name = '百度钱包支付接口';
    /**
     * @var string 支付方式key
     */
    public $app_key = 'bfb';
    /**
     * @var string 中心化统一的key
     */
    public $app_rpc_key = 'bfb';
    /**
     * @var string 统一显示的名称
     */
    public $display_name = '百付宝';
    /**
     * @var string 货币名称
     */
    public $curname = 'CNY';
    /**
     * @var string 当前支付方式的版本号
     */
    public $ver = '1.0';
    /**
     * @var string 当前支付方式所支持的平台
     */
    public $platform = 'ispc';

    /**
     * @var array 扩展参数
     */
    public $supportCurrency = array("CNY" => "01");

    /**
     * 构造方法
     * @param null
     * @return boolean
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->callback_url = kernel::openapi_url('openapi.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_bfb', 'callback');
        if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->callback_url, $matches)) {
            $this->callback_url = str_replace('http://', '', $this->callback_url);
            $this->callback_url = preg_replace("|/+|", "/", $this->callback_url);
            $this->callback_url = "http://" . $this->callback_url;
        } else {
            $this->callback_url = str_replace('https://', '', $this->callback_url);
            $this->callback_url = preg_replace("|/+|", "/", $this->callback_url);
            $this->callback_url = "https://" . $this->callback_url;
        }

        //统一收银台接口地址
        $this->submit_url = 'https://www.baifubao.com/api/0/pay/0/pre_direct';
        $this->submit_method = 'GET';
        $this->submit_charset = 'UTF-8';
    }

    /**
     * 后台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function admin_intro()
    {
        return '百度钱包支付:百付宝';
    }

    /**
     * 后台配置参数设置
     * @param null
     * @return array 配置参数列表
     */
    public function setting()
    {
        return array(
            'pay_name' => array(
                'title' => app::get('ectools')->_('支付方式名称'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'mer_id' => array(
                'title' => app::get('ectools')->_('百度钱包商户号'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'mer_key' => array(
                'title' => app::get('ectools')->_('百度钱包合作密钥(key)'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'order_by' => array(
                'title' => app::get('ectools')->_('排序'),
                'type' => 'string',
                'label' => app::get('ectools')->_('整数值越小,显示越靠前,默认值为1'),
            ),
            'support_cur' => array(
                'title' => app::get('ectools')->_('支持币种'),
                'type' => 'text hidden cur',
                'options' => $this->arrayCurrencyOptions,
            ),
            'real_method' => array(
                'title' => app::get('ectools')->_('选择接口类型'),
                'type' => 'select',
                'options' => array('0' => app::get('ectools')->_('统一收银台接口')),
            ),
            'pay_brief' => array(
                'title' => app::get('ectools')->_('支付方式简介'),
                'type' => 'textarea',
            ),
            'pay_desc' => array(
                'title' => app::get('ectools')->_('描述'),
                'type' => 'html',
                'includeBase' => true,
            ),
            'pay_type' => array(
                'title' => app::get('ectools')->_('支付类型(是否在线支付)'),
                'type' => 'radio',
                'options' => array('false' => app::get('ectools')->_('否'), 'true' => app::get('ectools')->_('是')),
                'name' => 'pay_type',
            ),
            'status' => array(
                'title' => app::get('ectools')->_('是否开启此支付方式'),
                'type' => 'radio',
                'options' => array('false' => app::get('ectools')->_('否'), 'true' => app::get('ectools')->_('是')),
                'name' => 'status',
            ),
        );
    }

    /**
     * 前台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function intro()
    {
        return app::get('ectools')->_('百付宝是百度旗下百度钱包推出的支付方式。');
    }

    /**
     * 提交支付信息的接口
     * @param array 提交信息的数组
     * @return mixed false or null
     */
    public function dopay($payment)
    {

        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);

        $subject = $payment['body'];
        $subject = str_replace("'", '`', trim($subject));
        $subject = str_replace('"', '`', $subject);

        if (empty($subject)) {
            $subject = '网店充值订单';
        }

        $this->add_field('currency', '1');
        $this->add_field('extra', $payment['payment_id']);
        $this->add_field('goods_name', $subject);
        $this->add_field('input_charset', '1');//参数中中文必须使用GBK编码后参与sign运算
        $this->add_field('order_create_time', date('YmdHis',time()));
        $this->add_field('order_no', $payment['order_id']);
        $this->add_field('pay_type', '1');
        $this->add_field('return_url', $this->callback_url);
        $this->add_field('service_code', '1');
        $this->add_field('sign_method', '1');//1代表MD5
        $this->add_field('sp_no', $mer_id);
        $this->add_field('total_amount', sprintf('%.2f', $payment['total_amount'])*100);
        $this->add_field('sign', $this->_get_mac_new($mer_key));

        unset($this->fields['key']);

        if ($this->is_fields_valiad()) {
            echo $this->get_html();
            exit;
        } else {
            return false;
        }
    }

    /**
     * 校验方法
     * @param null
     * @return boolean
     */
    public function is_fields_valiad()
    {
        return true;
    }

    /**
     * 支付后返回后处理的事件的动作
     * @params array - 所有返回的参数，包括POST和GET
     * @return null
     */
    public function callback(&$recv)
    {
        #键名与pay_setting中设置的一致
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);

        if ($this->is_return_vaild($recv, $mer_key)) {
            $ret['payment_id'] = $recv['extra'];
            $ret['account'] = $mer_id;
            $ret['bank'] = app::get('ectools')->_('百度钱包');
            $ret['pay_account'] = app::get('ectools')->_('付款帐号');
            $ret['trade_no'] = $recv['bfb_order_no'];//百度钱包交易号
            $ret['currency'] = 'CNY';
            $ret['money'] = $recv['total_amount'] / 100;
            $ret['cur_money'] = $recv['total_amount'] / 100;
            $ret['t_payed'] = strtotime($recv['pay_time']) ? strtotime($recv['pay_time']) : time();
            $ret['pay_app_id'] = "bfb";
            $ret['pay_type'] = 'online';
            switch ($recv['pay_result']) {
                case '1':
                    $ret['status'] = 'succ';
                    break;
                default:
                    $ret['status'] = 'failed';
                    break;
            }
        } else {
            $message = 'Invalid Sign';
            $ret['status'] = 'invalid';
        }

        return $ret;
    }

    /**
     * 生成支付表单 - 自动提交
     * @params null
     * @return null
     */
    public function gen_form()
    {
        $tmp_form = "<form name='applyForm' method='" . $this->submit_method . "' action='" . $this->submit_url . "' target='_blank'>";

        // 生成提交的hidden属性
        foreach ($this->fields as $key => $val) {
            $tmp_form .= "<input type='hidden' name='" . $key . "' value='" . $val . "'>";
        }

        $tmp_form .= "</form>";

        return $tmp_form;
    }


    /**
     * 生成签名
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key 签名用到的私钥
     * @access private
     * @return string
     */
    public function _get_mac($key)
    {
        ksort($this->fields);
        $mac = "";
        foreach ($this->fields as $k => $v) {
            if(!$mac){
                $mac = "{$k}={$v}";
            }else{
                $mac .= "&{$k}={$v}";
            }
        }
        $mac = md5($mac . $key);  //验证信息
        return $mac;
    }

    public function _get_mac_new($mkey)
    {
        if (is_array($this->fields)) {
            // 对参数数组进行按key升序排列
            if (ksort($this->fields)) {
                $arr_temp = array();
                foreach ($this->fields as $key => $val) {
                    $arr_temp [] = $key . '=' . $val;
                }
                $sign_str = implode('&', $arr_temp);
                $sign_str = $sign_str . '&key=' . $mkey;
                return md5($sign_str);
            }
        }
    }

    /**
     * 检验返回数据合法性
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key 签名用到的私钥
     * @access private
     * @return boolean
     */
    public function is_return_vaild($form, $key)
    {
        ksort($form);
        $signstr = '';
        foreach ($form as $k => $v) {
            if ($k != 'sign' && $k != 'sign_type') {
                $signstr .= "&$k=$v";
            }
        }
        $signstr = ltrim($signstr, "&");
        $signstr = $signstr . '&key='  .$key;
        if ($form['sign'] == md5($signstr)) {
            return true;
        }
        #记录返回失败的情况
        logger::error(app::get('ectools')->_('支付单号：') . $form['out_trade_no'] . app::get('ectools')->_('签名验证不通过，请确认！') . "\n");
        logger::error(app::get('ectools')->_('本地产生的加密串：') . $signstr);
        logger::error(app::get('ectools')->_('百付宝传递打过来的签名串：') . $form['sign']);
        return false;
    }

    /**
     * 支付成功回打支付成功信息给支付网关
     */
    function ret_result($paymentId)
    {
        $rep_str = "<html><head><meta name=\"VIP_BFB_PAYMENT\" content=\"BAIFUBAO\"></head><body><h1>接收成功</h1></body></html>";
        echo "$rep_str";
        exit;
    }

}
