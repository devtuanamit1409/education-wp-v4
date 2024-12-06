<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package thim
 */
if ( ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
$sticky_sidebar = ! empty( get_theme_mod( 'thim_sticky_sidebar', true ) ) ? ' sticky-sidebar' : '';
if ( get_theme_mod( 'thim_header_style', 'header_v1' ) == 'header_v4' ) {
	$sticky_sidebar .= ' sidebar_' . get_theme_mod( 'thim_header_style' );
}
?>

<div id="sidebar" class="widget-area col-sm-3<?php echo esc_attr( $sticky_sidebar ); ?>" role="complementary">
	<?php dynamic_sidebar( 'sidebar' ); ?>
</div><!-- #secondary -->
