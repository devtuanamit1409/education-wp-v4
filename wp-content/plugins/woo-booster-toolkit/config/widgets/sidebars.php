<?php

return apply_filters(
	'wcbt/filter/config/widgets/sidebars',
	array(
		'wcbt-product-archive-sidebar'     => array(
			'name'          => esc_html__( 'Product Archive Sidebar', 'wcbt' ),
			'description'   => esc_html__( 'Display widget items in the product sidebar area.', 'wcbt' ),
			'id'            => 'wcbt-product-archive-sidebar',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		),
	)
);
