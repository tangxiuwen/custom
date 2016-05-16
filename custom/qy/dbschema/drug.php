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
					'type' => 'int(8)',
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
			'price' =>
				array(
					'type' => 'money',
					'label' => app::get('qy')->_('价格'),
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
			'last_modify' => array(
				'type' => 'last_modify',
				'label' => app::get('qy')->_('更新时间'),
				'width' => 110,
				'editable' => false,
				'in_list' => true,
				'orderby' => true,
				'default_in_list' => true,
			),
		),
	'comment' => app::get('qy')->_('药品库信息'),
);
