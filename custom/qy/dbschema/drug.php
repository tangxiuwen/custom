<?php
$db['drug'] = array(
    'columns' =>
        array(
            'id' =>
                array(
                    'type' => 'int(8)',
                    'required' => true,
                    'pkey' => true,
                    'label' => app::get('qy')->_('id'),
                    'editable' => false,
                    'extra' => 'auto_increment',
                    'in_list' => false,
                ),
            'drug_id' =>
                array(
                    'type' => 'table:share',
                    'default' => 0,
                    'required' => true,
                    'label' => app::get('qy')->_('药品库id'),
                    'editable' => false,
                    'in_list' => true,
                ),
            'drug_name' =>
                array(
                    'type' => 'varchar(100)',
                    'default' => '',
                    'required' => true,
                    'editable' => false,
                    'label' => app::get('qy')->_('药品库名称'),
                    'in_list' => true,
                    'default_in_list' => false,
                ),
            'goods_id' =>
                array(
                    'type' => 'bn',
                    'label' => app::get('qy')->_('货品编码'),
                    'default' => 0,
					'type' => 'int(8)',
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'product_id' =>
                array(
                    'type' => 'bn',
                    'label' => app::get('qy')->_('货品编码'),
                    'default' => 0,
					'type' => 'int(8)',
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'product_bn' =>
                array(
                    'type' => 'bn',
                    'label' => app::get('qy')->_('货品编码'),
                    'default' => 0,
					'type' => 'varchar(100)',
                    'editable' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'createtime' =>
                array(
                    'type' => 'time',
                    'label' => app::get('qy')->_('创建时间'),
                    'editable' => false,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
        ),
    'comment' => app::get('qy')->_('药品库信息'),
);
