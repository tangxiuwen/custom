<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class qy_passport_trust implements  pam_interface_passport{

	public $open_id = 999999;

	/**
	 * @var site_router
	 */
	public $router;

	/**
	 * @var dbeav_model
	 */
	public $db;

	function __construct(){
       	$this->name = '泉依登录';
		$this->router = app::get('site')->router();

		$this->db = app::get('openid')->model('openid');
    }

    function login($auth, &$usrdata){
        $router = kernel::router();
        $url = $this->router->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'index'));
        if($_SESSION['account']['member'])
        {
            kernel::single('base_controller')->splash('success',$url,app::get('b2c')->_('您已经是登录状态，不需要重新登录'));
            exit();
        }

		$user_id = $_GET['user_id'];

		$post_data = array(
			'provider_openid' => $user_id
		);

		/** @var dbeav_model $open_member */
		$open_member = app::get('openid')->model('openid');
		$login_name = $this->getName($post_data);
		$data = $open_member->getRow('*', array(
			'openid' => $login_name
		));

		if(empty($data)){
			$url = $this->router->gen_url(array('app' => 'b2c', 'ctl' => 'site_passport', 'act' => 'signup'), true);
			kernel::single('base_controller')->splash('success', $url, app::get('b2c')->_('会员不存在!'));
			$_SESSION['openid_info'] = $data;
			exit();
		}else{
			$usrdata['login_name'] = $login_name;
			return $login_name;
		}
    }

    /**
     * @param $member_id  注册成功 添加关联信息
     */
    public function  registerActive($member_id){
        if(!empty($_SESSION['openid_info'])){
            $data = $_SESSION['openid_info']['openid_info'];
            $auth_model = app::get('pam')->model('auth');
            $op_model = app::get('openid')->model('openid');
            $op_data = array(
                'member_id' => $member_id
            );
            $op_model->update($op_data, array('openid' => $data['openid']));

            $data = array(
                'account_id' => $member_id,
                'module_uid' => $this->getName($data),
                'module' => 'openid_passport_trust',
            );

            $auth_model->insert($data);
            unset($_SESSION['openid_info']);
        }
    }

    public function getName($info){
        $login_name = $this->open_id.'_'.$info['provider_openid'] ;
        return $login_name;
    }

    function loginout($auth,$backurl="index.php"){
        unset($_SESSION['account'][$this->type]);
        unset($_SESSION['last_error']);
        #header('Location: '.$backurl);
    }

    function save_login_data($login_name){
        $account = app::get('pam')->model('account');
        $auth_model = app::get('pam')->model('auth');
        $data = array(
            'login_name' => $login_name,
            'login_password' => md5(time()),
            'account_type'=>'member',
            'createtime'=>time(),
        );
        $account_id = $account->insert($data);

        $data = array(
            'account_id'=>$account_id,
            'module_uid'=>$login_name,
            'module'=>'openid_passport_trust',
        );
        $auth_model->insert($data);
        return true;
    }

	function get_data(){
    }

    function get_id(){
    }

	function get_expired(){
    }

	function get_login_form($auth, $appid, $view, $ext_pagedata=array()){
        return null;
    }

	function get_name(){
        return null;
    }


	function update($data, &$msg){

		$db = $this->db->db;
		$db_begin = $db->beginTransaction();
		$login_name = $this->getName($data);
		$filter = array(
			'openid' => $login_name
		);

		$open_info = $this->db->getRow('*', $filter);


		$account = app::get('pam')->model('members');


		if(!empty($open_info)) {
			$update_data = array(
				'provider_code' => $data['provider_code'], //泉依卡号
				'realname' => $data['realname'],   //理赔产品id
				'nickname' => $data['nickname'],  //用户姓名
			);


			if(!$this->db->update($update_data ,array('openid' => $login_name))){
				goto END;
			}

			if($db->commit($db_begin)){
				$msg = '更新成功';
				return true;
			}
		}else{
			$msg = '保存失败!';
			$lv_model = app::get('b2c')->model('member_lv');
			$member_lv_id = $lv_model->get_default_lv();

			$insert_data = array(
                'member_lv_id' => $member_lv_id,
                'email' => '',
                'name'=> $data['nickname'],
                'regtime' => time(),
				'mobile' => $data['phone'],
                'trust_name' => $data['nickname']
            );

			$obj_mem = app::get('b2c')->model('members');

			if(!$member_id = $obj_mem->insert($insert_data)){
				goto END;
			}

			$auth_model = app::get('pam')->model('auth');
			$insert_data = array(
				'account_id' => $member_id,
				'module_uid' => $login_name,
				'module' => get_class($this),
			);

			if(!$auth_model->insert($insert_data)){
				goto END;
			}

			$insert_data = array(
				'member_id' => $member_id,
				'login_account' => $login_name,
				'password_account' => $login_name,
				'login_password' => md5(time()),
				'login_type' => 'local',
				'createtime' => time(),
			);

			if(!$account->insert($insert_data)){
				goto END;
			}


			$insert_data = array(
				'member_id' => $member_id,
				'openid' => $login_name,
				'provider_openid' => $data['provider_openid'],
				'provider_code' => $data['provider_code'],
				'realname' => $data['realname'],   //理赔产品id
				'nickname' => $data['nickname'],  //用户姓名
			);

			if(!$this->db->insert($insert_data)){
				goto END;
			}


			$insert_data = array(
				'member_id' => $member_id,
				'open_id' => $login_name,
				'tag_type' => 'qy',
				'tag_name' => $data['nickname'],
				'createtime' => time()
			);

			if(!app::get('pam')->model('bind_tag')->insert($insert_data)){
				goto END;
			}



			if($db->commit($db_begin)){
				$msg = '创建成功';
				return true;
			}

		}

		END:
		$db->rollBack();
		return false;

    }




	/**
	* 得到配置信息
	* @return  array 配置信息数组
	*/
    function get_config(){
        $ret = app::get('pam')->getConf('passport.'.__CLASS__);
        if($ret && isset($ret['shopadmin_passport_status']['value']) && isset($ret['site_passport_status']['value'])){
            return $ret;
        }else{
            $ret = $this->get_setting();
            $ret['passport_id']['value'] = __CLASS__;
            $ret['passport_name']['value'] = $this->name;
            $ret['shopadmin_passport_status']['value'] = 'true';
            $ret['site_passport_status']['value'] = 'true';
            $ret['passport_version']['value'] = '1.0';
            app::get('pam')->setConf('passport.'.__CLASS__,$ret);
            return $ret;
        }
    }
    /**
	* 设置配置信息
	* @param array $config 配置信息数组
	* @return  bool 配置信息设置成功与否
	*/
    function set_config(&$config){
        $save = app::get('pam')->getConf('passport.'.__CLASS__);
        if(count($config))
            foreach($config as $key=>$value){
                if(!in_array($key,array_keys($save))) continue;
                $save[$key]['value'] = $value;
            }
        $save['shopadmin_passport_status']['value'] = 'false';
        return app::get('pam')->setConf('passport.'.__CLASS__,$save);
    }


	  /**
	* 获取finder上编辑时显示的表单信息
	* @return array 配置信息需要填入的项
	*/
    function get_setting(){
        return array(
            'passport_id'=>array('label'=>app::get('pam')->_('通行证id'),'type'=>'text','editable'=>false),
            'passport_name'=>array('label'=>app::get('pam')->_('通行证'),'type'=>'text','editable'=>false),
            'shopadmin_passport_status'=>array('label'=>app::get('pam')->_('后台开启'),'type'=>'bool','editable'=>false),
            'site_passport_status'=>array('label'=>app::get('pam')->_('前台开启'),'type'=>'bool'),
            'passport_version'=>array('label'=>app::get('pam')->_('版本'),'type'=>'text','editable'=>false),
        );
    }


}


