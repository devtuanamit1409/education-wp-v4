<?php
/**
 * Field Logo and Sticky Logo
 *
 */
thim_customizer()->add_section(
	array(
		'id'       => 'general_logo',
		'panel'    => 'general',
		'title'    => esc_html__( 'Logo', 'eduma' ),
		'priority' => 10,
 	)
);
thim_customizer()->add_field(
	array(
		'id'            => 'thim_desc_logo_tpl',
		'type'          => 'tp_notice',
		'description'   => sprintf( __( 'This header is built by Thim Elementor Kit, you can edit and configure it in %s.', 'eduma' ), '<a href="' . admin_url( 'edit.php?post_type=thim_elementor_kit&thim_elementor_type=header' ) . '" target="_blank">' . __( 'Thim Elementor Kit', 'eduma' ) . '</a>' ),
		'section'       => 'general_logo',
		'priority'      => 11,
		'wrapper_attrs' => array(
			'class' => '{default_class} hide' . thim_customizer_extral_class( 'header' )
		)
	)
);
thim_customizer()->add_field(
	array(
		'id'       		=> 'thim_logo_retina',
		'type'          => 'image',
		'section'  		=> 'general_logo',
		'label'    		=> esc_html__( 'Logo Retina', 'eduma' ),
		'tooltip'     	=> esc_html__( 'Allows you to add, remove, change logo on your site. ', 'eduma' ),
		'priority' 		=> 10,
		'default'       => '',
		'wrapper_attrs' => array(
			'class' => '{default_class} hide' . thim_customizer_extral_class( 'header' )
		)

	)
);
// Header Logo
thim_customizer()->add_field(
	array(
		'id'       		=> 'thim_logo',
		'type'          => 'image',
		'section'  		=> 'general_logo',
		'label'    		=> esc_html__( 'Logo', 'eduma' ),
		'tooltip'     	=> esc_html__( 'Allows you to add, remove, change logo on your site. ', 'eduma' ),
		'priority' 		=> 10,
		'default'       => THIM_URI . "images/logo.png",
//		'wrapper_attrs' => array(
//			'class' => '{default_class} hide' . thim_customizer_extral_class( 'header' )
//		)
	)
);

// Header Sticky Logo
thim_customizer()->add_field(
	array(
		'id'       		=> 'thim_sticky_logo',
		'type'          => 'image',
		'section'  		=> 'general_logo',
		'label'    		=> esc_html__( 'Sticky Logo', 'eduma' ),
		'tooltip'     	=> esc_html__( 'Allows you to add, remove, change sticky logo on your site. ', 'eduma' ),
		'priority' 		=> 20,
		'default'       => THIM_URI . "images/logo-sticky.png",
		'wrapper_attrs' => array(
			'class' => '{default_class} hide' . thim_customizer_extral_class( 'header' )
		)
 	)
);

// Logo width
thim_customizer()->add_field(
	array(
		'id'          => 'thim_width_logo',
		'type'        => 'dimension',
		'label'       => esc_html__( 'Logo width', 'eduma' ),
		'tooltip'     => esc_html__( 'Allows you to assign a value for logo width. Example: 10px, 3em, 48%, 90vh etc.', 'eduma' ),
		'section'     => 'general_logo',
		'default'     => '155px',
		'priority'    => 40,
		'choices'     => array(
			'min'  => 100,
			'max'  => 500,
			'step' => 1,
		),
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'element'  => 'header#masthead .width-logo > a',
				'property' => 'width',
			)
		),
		'wrapper_attrs' => array(
			'class' => '{default_class} hide' . thim_customizer_extral_class( 'header' )
		)
	)
);
