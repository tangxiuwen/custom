<?php
$db['product'] = array(
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
			'productionId' =>
				array(
					'type' => 'table:share',
					'default' => 0,
					'required' => true,
					'label' => app::get('qy')->_('理赔产品id'),
					'editable' => false,
					'in_list' => true,
				),
			'productionName' =>
				array(
					'type' => 'varchar(100)',
					'default' => '',
					'required' => true,
					'editable' => false,
					'label' => app::get('qy')->_('理赔产品名称'),
					'in_list' => true,
					'default_in_list' => false,
				),
			'drugHouseId' =>
				array(
					'type' => 'bn',
					'label' => app::get('qy')->_('自定义药品库id'),
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
			),
		),
	'comment' => app::get('qy')->_('药品库信息'),
);
