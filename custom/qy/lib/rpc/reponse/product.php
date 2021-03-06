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


	public $update_data,$insert_data,$delete_data;


	public function __construct(){
		$this->model = app::get('qy')->model('product');
		parent::__construct();
	}

	public function update($data, &$msg){
		$this->getUpdateDate($data['detailList']);

		$succ = false;
		$status = $this->model->db->beginTransaction();

		$msg = '';
		if(!$this->do_update($msg)){
			goto END;
		}

		if(!$this->do_insert($msg)){
			goto END;
		}

		if(!$this->do_delete($msg)){
			goto END;
		}

		if($this->model->db->commit($status)){
			$msg = '成功';
			$succ = true;
		}

		END:
		return $this->msg->set_msg($msg, $succ);
	}



	public function getUpdateDate($data){

		foreach($data as $value){

			if(empty($value['productionId'])){
				continue;
			}

			switch($value['operate']){
				case 1:
					$this->insert_data[] = $value;
					break;
				case 2:
					$this->delete_data[] = $value;
					break;
				case 3:
					$this->update_data[] = $value;
					break;
			}
		}

	}



	public function do_update(&$msg = ''){
		if(empty($this->update_data)){
			return true;
		}

		foreach($this->update_data as $value){
			$update_data = array(
				'productionName' => $value['productionName'],	//理赔产品名称
				'drugHouseId' => '"'.$value['drugHouseId'].'"',			//自定义药品库id
				'last_modify' => $this->run_time,  		//更新时间
			);

			if(!$this->model->update($update_data, array('productionId' => $value['productionId']))){
				$msg = '更新失败！';
				return false;
			}
		}

		return true;
	}

	public function do_insert(&$msg = ''){
		if(empty($this->insert_data)){
			return true;
		}

		$table_name = $this->model->table_name(1);
		$sql = 'INSERT INTO '.$table_name.' VALUES ';
		foreach($this->insert_data as $value){
			$update_data = array(
				'id' => 0,
				'productionId' => $value['productionId'],	//理赔产品id
				'productionName' => '"'.$value['productionName'].'"',	//理赔产品名称
				'drugHouseId' => '"'.$value['drugHouseId'].'"',			//自定义药品库id
				'createtime' => $this->run_time,  		//更新时间
				'last_modify' => $this->run_time,  		//更新时间
			);
			$sql .= '('.implode(',', $update_data).'),';
		}

		$sql = substr($sql, 0, -1);

		if($this->model->db->exec($sql)){
			return true;
		}else{
			$msg = '添加失败';
			return false;
		}

	}


	public function do_delete(&$msg = ''){
		if(empty($this->delete_data)){
			return true;
		}

		$ids = array();
		foreach($this->delete_data as $value){
			$ids[] = $value['productionId'];
		}

		if($this->model->delete(array('productionId' => $ids))){
			return true;
		}else{
			$msg = '添加失败';
			return false;
		}
	}





}