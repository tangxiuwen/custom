<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/4/14
 * Time: 21:52
 */
class b2c_util
{

    var $is_fastbuy;

    function __construct()
    {
        kernel::single('base_session')->start();
    }

    function set_cart_type($data)
    {
        $_SESSION['cart_type']=$data;
    }

    function get_cart_type()
    {
        return $_SESSION['cart_type'];
    }

    function get_goods_type()
    {

    }

    function set()
    {

    }

    function get()
    {

    }

    function destory()
    {
        
    }
}