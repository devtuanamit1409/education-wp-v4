<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package thim
 */
if ( ! is_active_sidebar( 'sidebar_shop' ) ) {
	return;
}
$sticky_sidebar = ! empty( get_theme_mod( 'thim_sticky_sidebar', true ) ) ? ' sticky-sidebar' : '';
if ( get_theme_mod( 'thim_header_style', 'header_v1' ) == 'header_v4' ) {
	$sticky_sidebar .= ' sidebar_' . get_theme_mod( 'thim_header_style' );
}
?>

<div id="sidebar" class="widget-area col-sm-3<?php echo esc_attr( $sticky_sidebar ); ?>" role="complementary">
	<?php dynamic_sidebar( 'sidebar_shop' ); ?>
</div><!-- #secondary-2 -->
