<ul class="nav navbar-nav menu-main-menu thim-ekits-menu__nav">
	<?php
	wp_nav_menu(
		array(
			'theme_location' => has_nav_menu( 'primary' ) ? 'primary' : '',
			'container'      => false,
			'items_wrap'     => '%3$s'
		)
	);
	//sidebar menu_right
	if ( get_theme_mod( 'thim_header_style', 'header_v1' ) != 'header_v4' && is_active_sidebar( 'menu_right' ) ) {
		echo '<li class="menu-right"><ul>';
		dynamic_sidebar( 'menu_right' );
		echo '</ul></li>';
	}
	?>
</ul>
