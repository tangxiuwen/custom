<?php
/**
 * Created by Test, 2016/04/30 22:16.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_rpc_reponse_product extends qy_reponse{

	/**
	 * @var dbeav_model
	 */
	public $model;

	public function __construct(){
		$this->model = app::get('qy')->model('product');
		parent::__construct();
	}

	public function update($data, &$msg){

		if($data['operate'] == 1){
			$method = 'do_insert';
		}else if($data['operate'] == 2){
			$method = 'do_delete';
		}else if($data['operate'] == 3){
			$method = 'do_update';
		}

		$update_data = array(
			'productionId' => $data['productionId'],		//理赔产品id
			'productionName' => $data['productionName'],	//理赔产品名称
			'drugHouseId' => $data['drugHouseId'],			//自定义药品库id
			'last_modify' => $this->run_time,  		//更新时间
		);
		$msg = '成功';
		return $this->$method($update_data, $msg);

	}

	public function do_update($data, &$msg){
		if($this->model->update($data, array('productionId' => $data['productionId']))){
			return true;
		}else{
			$msg = '更新失败';
			return false;
		}
	}


	public function do_insert($data, &$msg){
		$data['createtime'] = $this->run_time;
		if($this->model->insert($data)){
			return true;
		}else{
			$msg = '更新失败';
			return false;
		}
	}


	public function do_delete($data, &$msg){
		if($this->model->delete(array('productionId' => $data['productionId']))){
			return true;
		}else{
			$msg = '更新失败';
			return false;
		}
	}





}