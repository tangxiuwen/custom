<?php

class qy_sign
{

	public $private_file = 'src/drug.pfx';
	public $public_file = 'src/drug.cer';
	public $private_password = '123456';


	public function get_sign($str){
		$private_str = file_get_contents($this->private_file);
		openssl_pkcs12_read($private_str, $certs, $this->private_password);

		openssl_sign($str, $sign, $certs['pkey'], OPENSSL_ALGO_SHA1);
		return $sign = base64_encode($sign);
	}


	public function verify($str, $sign)
	{
		$pubkeyid = openssl_pkey_get_public('file://' . $this->public_file);
		//$pubkeyid = file_get_contents($this->public_file);
		if (openssl_verify($str, base64_decode($sign), $pubkeyid) == 1) {
			return true;
		} else {
			return false;
		}
	}


}
