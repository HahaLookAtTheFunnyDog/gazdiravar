<?php

return array(
	'version'      => '1.6.110',
	'content_type' => 'free',
	'themes'       => array(
		'mesmerize',
	),
	'theme_type'   => 'multipage',
	'data'         => array(
		'bgColorPalette' => array(
			'#ffffff',
			'#F5FAFD',
		),
		'fonts'          => array(),
		'headers'        => array(),
		'footers'        => array(),
	),
	'customizer'   => array(
		'panels'   => array(
			'page_content_panel' => array(
				'class'    => 'Mesmerize\Customizer\Panels\ContentPanel',
				'disabled' => false,
				'wp_data'  => array(
					'title'    => 'Page Content',
					'priority' => 2,
				),
			),
		),
		'sections' => array(),
		'settings' => array(),
	),
);
