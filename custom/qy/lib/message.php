<?php

class qy_message
{

	var $msg;

	var $suc = true;

	public function set_msg($str, $suc = true){
		$this->msg = $str;
		$this->suc = $suc;
	}

	public function send(){
		$data = array(
			'rspCode' => $this->suc == true ? "0" : "1",
			'rspMsg' => "$this->msg"
		);

		echo json_encode($data);
	}

}
