<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class b2c_ctl_admin_member_discuss extends desktop_controller
{

    var $workground = 'b2c_ctl_admin_member';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index()
    {
        $member_comments = $this->app->model('member_comments');
        $member_comments->set_type('discuss');
        $this->finder('b2c_mdl_member_comments', array(
            'title' => app::get('b2c')->_('评论列表'),
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'base_filter' => array('for_comment_id' => 0),
            'actions' => array(
                array(
                    'label' => app::get('b2c')->_('导入'),
                    'href' => 'index.php?app=b2c&ctl=admin_member_discuss&act=import',
                    'target' => 'dialog::{width:680,height:250,title:\'' . app::get('b2c')->_('批量导入评论') . '\'}',
                )
            ),
        ));

    }

    function setting()
    {
        $member_comments = kernel::single('b2c_message_disask');
        $aOut = $member_comments->get_setting('discuss');
        if (!$aOut['verifyCode']['discuss']) {
            $aOut['verifyCode']['discuss'] = 'off';
        }
        $aOut['aSwitch']['discuss'] = array('on' => app::get('b2c')->_('开启'), 'off' => app::get('b2c')->_('关闭'));
        $aOut['verifyLCode']['discuss'] = array('on' => app::get('b2c')->_('开启'), 'off' => app::get('b2c')->_('关闭'));
        foreach (kernel::servicelist('comment_list') as $service) {
            $this->pagedata['html'][] = $service->get_Html();
        }
        $this->pagedata['setting'] = $aOut;
        echo $this->fetch('admin/member/discuss_setting.html');
    }

    function to_setting()
    {
        $this->begin();
        $member_comments = kernel::single('b2c_message_disask');
        $aOut = $member_comments->to_setting('discuss', $_POST);
        foreach (kernel::servicelist('comment_list') as $service) {
            $service->save_setting($_POST);
        }
        $this->end('success', app::get('b2c')->_('设置成功'));
    }

#回复评论
    function to_reply()
    {
        $this->begin("javascript:finderGroup[" . "'" . $_GET["finder_id"] . "'" . "].refresh()");
        $comment_id = $_POST['comment_id'];
        $comment = $_POST['reply_content'];
        if ($comment_id && $comment) {
            $member_comments = kernel::single('b2c_message_disask');
            $row = $member_comments->dump($comment_id);
            $sdf['goods_name'] = $row['goods_name'];
            $sdf['goods_id'] = $row['goods_id'];
            $sdf['uname'] = $row['author'];
            $author_id = $row['author_id'];
            unset($row['goods_point']);
            if ($this->app->getConf('comment.display.discuss') == 'reply') {
                $aData = $row;
                $aData['display'] = 'true';
                $goods_point = $this->app->model('comment_goods_point');
                $goods_point->set_status($comment_id, 'true');
                $_is_add_point = app::get('b2c')->getConf('member_point');
                if ($_is_add_point && $author_id) {
                    $obj_member_point = $this->app->model('member_point');
                    $obj_member_point->change_point($author_id, $_is_add_point, $_msg, 'comment_discuss', 2, $row['type_id'], $author_id, 'comment');
                }
                $member_comments->save($aData);
            }
            $sdf['comment_id'] = '';
            $sdf['for_comment_id'] = $comment_id;
            $sdf['object_type'] = "discuss";
            $sdf['to_id'] = $author_id;
            $sdf['author_id'] = null;
            $sdf['author'] = app::get('b2c')->_('管理员');
            $sdf['title'] = '';
            $sdf['contact'] = '';
            $sdf['display'] = 'true';
            $sdf['time'] = time();
            $sdf['comment'] = $comment;
            if ($member_comments->send($sdf, 'discuss')) {
                $comments = $this->app->model('member_comments');
                $sdf['member_id'] = $author_id;
                $comments->fireEvent('discussreply', $sdf, $author_id);
                #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
                if ($obj_operatorlogs = kernel::service('operatorlog.members')) {
                    if (method_exists($obj_operatorlogs, 'reply_comment')) {
                        $obj_operatorlogs->reply_comment($sdf);
                    }
                }
                #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
                $this->end(true, app::get('b2c')->_('操作成功'));
            } else {
                $this->end(false, app::get('b2c')->_('操作失败'));
            }
        } else {
            $this->end(false, app::get('b2c')->_('内容不能为空'));
        }
    }

    public function import()
    {
        $this->page('admin/member/discusss.html');
    }

    public function add_import()
    {
        $this->begin();
        $file = $_FILES['file'];

        if ($file['size'] == 0) {
            $this->end(false, app::get('b2c')->_('请选择上传文件！'));
            exit;
        }
        $file_type = substr(strstr($file['name'], '.'), 1);
        if ($file_type != 'csv') {
            $this->end(false, app::get('b2c')->_('文件格式不对！'));
            exit;
        }

        $discuss_obj = $this->app->model('member_comments');
        $member_obj = app::get('pam')->model('members');
        $pro_obj = $this->app->model('products');
        $this->db = kernel::database();

        $handle = fopen($file['tmp_name'], "r");
        $row = 0;
        while ($data = fgetcsv($handle, ',')) {
            $row++;
            if ($row == 1) {
                continue;
            }
            for ($i = 0; $i < count($data); $i++) {
                $data[$i] = iconv("GBK", "UTF-8", $data[$i]);
            }
            if (strstr($data[0], "'") || strstr($data[0], '"')) {
                $data[0] = substr($data[0], 1, strlen($data[0]));
                $data[0] = substr($data[0], 0, strlen($data[0]) - 1);
            }
            if (strstr($data[3], "'") || strstr($data[3], '"')) {
                $data[3] = substr($data[3], 1, strlen($data[3]));
                $data[3] = substr($data[3], 0, strlen($data[3]) - 1);
            }
            $goods_name = $pro_obj->getRow('goods_id,product_id', array('bn' => $data[0]));
            $author_id = $member_obj->getRow('member_id', array('login_account' => $data[3]));
            $disucss = array();
            $disucss['for_comment_id'] = 0;//trim($data[0]);
            $disucss['type_id'] = trim($goods_name['goods_id']);
            $disucss['product_id'] = trim($goods_name['product_id']);
            $disucss['contact'] = '';//trim($data[2]);
            $disucss['time'] = trim(strtotime($data[1]));
            //$disucss['title'] = trim($data[4]);
            $disucss['comment'] = trim($data[2]);
            $disucss['gask_type'] = '';//trim($data[6]);
            $disucss['author'] = trim($data[3]);
            $disucss['author_id'] = $author_id['member_id'];
            //$disucss['order_id'] = trim($data[8]);
            $disucss['display'] = strtolower(trim($data[4]));
            $disucss['object_type'] = 'discuss';
            $disucss['comment_type'] = 'normal';
            if ($data[5]) {
                $disucss['addon'] = serialize(array('hidden_name' => 'YES')); //匿名
            } else {
                $disucss['addon'] = '';
            }
            $disucss['is_export'] = 'true';
            if ($discuss_obj->insert($disucss)) {
                $flag = true;
                $goods_point = $this->app->model('comment_goods_point');
                $_pointsdf['comment_id'] = $disucss['comment_id'];
                if ($data[6]) { //评分导入
                    $pingfeng = explode('*', trim($data[6]));
                    if ($disucss['display'] == 'true') {
                        $_pointsdf['display'] = 'true';
                    } else {
                        $_pointsdf['display'] = 'false';
                    }
                    $_pointsdf['goods_id'] = $disucss['type_id'];
                    $_pointsdf['member_id'] = $disucss['author_id'];
                    //更新goods表评论次数
                    $sql = 'UPDATE sdb_b2c_goods SET comments_count=comments_count+1 WHERE goods_id=' . $_pointsdf['goods_id'];

                    $this->db->exec($sql);

                    foreach ($pingfeng as $key => $val) {
                        //if($key>2) break;
                        $point = (float)$val;
                        if ($point > 0 && $point <= 5) {
                            $_pointsdf['goods_point'] = $point;
                        } else {
                            $_pointsdf['goods_point'] = 5;
                        }
                        $_pointsdf['type_id'] = $key + 1;
                        unset($_pointsdf['point_id']);
                        $goods_point->save($_pointsdf);
                    }
                }
            } else {
                $flag = false;
            }
        }
        fclose($handle);
        if ($flag) {
            $this->end(true, app::get('b2c')->_('提交成功！'));
        } else {
            $this->end(false, app::get('b2c')->_('数据格式不正确,提交失败'));
        }
    }

}
