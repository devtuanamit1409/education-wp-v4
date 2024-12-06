<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package thim
 */
$theme_options_data = get_theme_mods();
$show_booking     = !empty( $theme_options_data['thim_event_disable_book_event'] ) ? false : true;
if ( !is_active_sidebar( 'sidebar_events' ) && !$show_booking ) {
	return;
}

$sticky_sidebar = ! empty( get_theme_mod( 'thim_sticky_sidebar', true ) ) ? ' sticky-sidebar' : '';
if ( get_theme_mod( 'thim_header_style', 'header_v1' ) == 'header_v4' ) {
	$sticky_sidebar .= ' sidebar_' . get_theme_mod( 'thim_header_style' );
}
?>

<div id="sidebar" class="widget-area col-sm-3 sidebar-events<?php echo esc_attr( $sticky_sidebar ); ?>" role="complementary">
	<?php

	/**
	 * thim_event_booking hook
	 */
	do_action( 'thim_event_booking' );

	if ( !dynamic_sidebar( 'sidebar_events' ) ) :
		dynamic_sidebar( 'sidebar_events' );
	endif; // end sidebar widget area ?>
</div><!-- #secondary -->
