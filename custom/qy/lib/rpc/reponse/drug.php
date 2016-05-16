<?php
/**
 * Created by Test, 2016/04/30 22:16.
 * @author serpent.
 *
 * Copyright (c) 2016 serpent All rights reserved.
 */

class qy_rpc_reponse_drug extends qy_reponse{


	/**
	 * @var dbeav_model
	 */
	public $model;


	public $update_data,$insert_data,$delete_data;


	public function __construct(){
		$this->model = app::get('qy')->model('drug');
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
				'drug_name' => $value['drugHouseName'],
				'product_bn' => $value['goodNo'],
				'price' => $value['price'],
				'last_modify' => $this->run_time,  		//更新时间
			);

			if(!$this->model->update($update_data, array('drug_id' => $value['drugHouseId']))){
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
				'drug_id' => '"'.$value['drugHouseId'].'"',
				'drug_name' => '"'.$value['drugHouseName'].'"',
				'product_bn' => $value['goodNo'],
				'price' => empty($value['price']) ? 0 : $value['price'],
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
			$ids[] = $value['drugHouseId'];
		}

		if($this->model->delete(array('drug_id' => $ids))){
			return true;
		}else{
			$msg = '添加失败';
			return false;
		}
	}


}