<?php

final class ectools_payment_plugin_unionpay extends ectools_payment_app implements ectools_interface_payment_app
{
    public $name = '中国银联';
    public $app_name = '中国银联支付接口';
    public $app_key = 'unionpay';
    /** 中心化统一的key **/
    public $app_rpc_key = 'unionpay';
    public $display_name = 'unionpay';
    public $curname = 'CNY';
    public $ver = '1.0';
    /**
     * @var array 扩展参数
     */
    public $supportCurrency = array("CNY" => "1");
    /**
     * @var string 当前支付方式所支持的平台
     */
    public $platform = 'ispc';

    function is_fields_valiad()
    {
        return true;
    }


    function intro()
    {
        return '<b><h3>' . app::get('ectools')->_('中国银联支付（UnionPay）是银联电子支付服务有限公司主要从事以互联网等新兴渠道为基础的网上支付。') . '</h3></b>';
    }

    function admin_intro()
    {
        return app::get('ectools')->_('中国银联支付（UnionPay）是银联电子支付服务有限公司主要从事以互联网等新兴渠道为基础的网上支付。("网付通"支付网关，是广州银联网络支付有限公司于2002年建成开通的统一支付网关系统)');
    }

    public function __construct($app)
    {
        parent::__construct($app);

        $this->callback_url = kernel::openapi_url('openapi.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_unionpay', 'callback');
        if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->callback_url, $matches)) {
            $this->callback_url = str_replace('http://', '', $this->callback_url);
            $this->callback_url = preg_replace("|/+|", "/", $this->callback_url);
            $this->callback_url = "http://" . $this->callback_url;
        } else {
            $this->callback_url = str_replace('https://', '', $this->callback_url);
            $this->callback_url = preg_replace("|/+|", "/", $this->callback_url);
            $this->callback_url = "https://" . $this->callback_url;
        }

        // 按照相应要求请求接口网关改为一下地址
        // $this->submit_url = 'http://58.246.226.99/UpopWeb/api/Pay.action';
        //$this->submit_url = 'https://unionpaysecure.com/api/Pay.action';
        $this->submit_url = 'https://www.gnete.com/bin/scripts/OpenVendor/gnete/V36/GetOvOrder.asp';
        $this->submit_method = 'POST';
        $this->submit_charset = 'GB2312';
    }

    public function setting()
    {
        return array(
            'pay_name' => array(
                'title' => app::get('ectools')->_('支付方式名称'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'mer_id' => array(
                'title' => app::get('ectools')->_('商户ID'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'mer_key' => array(
                'title' => app::get('ectools')->_('私钥'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'order_by' => array(
                'title' => app::get('ectools')->_('排序'),
                'type' => 'string',
                'label' => app::get('ectools')->_('整数值越小,显示越靠前,默认值为1'),
            ),
            'pay_fee' => array(
                'title' => app::get('ectools')->_('交易费率'),
                'type' => 'pecentage',
                'validate_type' => 'number',
            ),
            'support_cur' => array(
                'title' => app::get('ectools')->_('支持币种'),
                'type' => 'text hidden cur',
                'options' => $this->arrayCurrencyOptions,
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


    public function dopay($payment)
    {
        $merId = $this->getConf('mer_id', __CLASS__);//客户号

        $args = array(
            "MerId" => $merId,//商户代码
            "OrderNo" => $payment['payment_id'],//银行订单号(对应本系统支付单号)\
            "OrderAmount" => $payment['cur_money'],//交易金额
            'CurrCode' => 'CNY',//币种
            "OrderType" => "B2C",//订单类型
            "CallBackUrl" => $this->callback_url,//通知的url
            'BankCode' => '',
            'LangType' => 'GB2312',
            'BuzType' => '01',
            'Reserved01' => '',
            'Reserved02' => '',
        );

        //生成签名
        $chkvalue = $this->sign($args);
        //循环给表单赋值
        foreach ($args as $key => $val) {
            $this->add_field($key, $val);
        }
        //再往表单里面添加签名方法，签名
        $this->add_field('SignMsg', $chkvalue);
        if ($this->is_fields_valiad()) {
            echo $this->get_html();
            exit;
        } else {
            return false;
        }

    }

    //回调函数: 接受银联返回来的数据
    public function callback(&$recv)
    {
        error_log(date('Y-m-d H:i:s') . '=' . var_export($recv, 1), 3, DATA_DIR . '/txw-111.txt');
        $sign = $recv['SignMsg'];  //银联返回来的签名
        $arrs = array(
            "OrderNo" => $recv['OrderNo'],
            "PayNo"=> $recv['PayNo'],//支付单号
            "PayAmount" => $recv['PayAmount'],//交易金额 格式：元.角分
            'CurrCode' => $recv['CurrCode'],//币种
            "SystemSSN" => $recv['SystemSSN'],//银联系统参考号
            "RespCode" => $recv['RespCode'],//交易响应码
            'SettDate' => $recv['SettDate'],//清算日期，格式（MMDD）
            'Reserved01' => $recv['Reserved01'],
            'Reserved02' => $recv['Reserved02'],
        );
        error_log(date('Y-m-d H:i:s') . '=' . var_export($arrs, 1), 3, DATA_DIR . '/txw-222.txt');
        //生成签名
        $chkvalue = $this->sign($arrs);

        $ret['payment_id'] = $arrs['OrderNo'];
        $ret['account'] = $merId = $this->getConf('mer_id', __CLASS__);//客户号
        $ret['bank'] = app::get('unionpay')->_('银联');
        $ret['pay_account'] = app::get('unionpay')->_('付款帐号');
        $ret['currency'] = 'CNY';
        $ret['money'] = $arrs['PayAmount'];
        $ret['paycost'] = '0.000';
        $ret['cur_money'] = $arrs['PayAmount'];
        $ret['trade_no'] = $arrs['PayNo'].'-'. $arrs['SystemSSN'];
        $ret['t_payed'] = time();
        $ret['pay_app_id'] = 'unionpay';
        $ret['pay_type'] = 'online';
        $ret['memo'] = 'unionpay';
        //校验签名
        if ($sign == $chkvalue && $arrs['RespCode'] === '00') {
            $ret['status'] = 'succ';
        } else {
            $ret['status'] = 'failed';
        }
        error_log(date('Y-m-d H:i:s') . '=' . var_export($ret, 1), 3, DATA_DIR . '/txw-333.txt');
        return $ret;
    }

    /*
        签名方法 3个参数
        $params:组织给银联发过去的数据
    */
    private function sign($params)
    {

        $mer_key = $this->getConf('mer_key', __CLASS__);//私钥
        $sign_str = "";
        foreach ($params as $key => $val) {
            $sign_str .= $val;
        }
        $sign = $sign_str . MD5($mer_key);
        return MD5($sign);
    }


    public function gen_form()
    {
        $tmp_form = '<a href="javascript:void(0)" onclick="document.applyForm.submit();">' . app::get('unionpay')->_('立即申请') . '</a>';
        $tmp_form .= "<form name='applyForm' method='" . $this->submit_method . "' action='" . $this->submit_url . "' target='_blank'>";
        // 生成提交的hidden属性
        foreach ($this->fields as $key => $val) {
            $tmp_form .= "<input type='hidden' name='" . $key . "' value='" . $val . "'>";
        }

        $tmp_form .= "</form>";

        return $tmp_form;

    }

    function ret_result($paymentId)
    {
        echo "OK";
    }
}
