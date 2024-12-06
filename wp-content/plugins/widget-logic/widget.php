<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Widget_Logic_Live_Match_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'live_match_widget',
			'description' => esc_html__('Current or next live match widget', 'widget-logic'),
		);
		parent::__construct('live_match_widget', esc_html__('Live Match Widget', 'widget-logic'), $widget_ops);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$defaults = array(
			'title'       => esc_html__('Live Match', 'widget-logic')
        );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title    = apply_filters( 'widget_title', $instance['title'] );
		?>

		<?php echo $args['before_widget']; // @codingStandardsIgnoreLine here I can't escape output because other widgets will be damaged (example from https://developer.wordpress.org/reference/classes/wp_widget_search/widget/) ?>
		<div class="live_match_widget">
            <?php if ( $title ) { ?>
            <div class="live_match_widget_header"><?php echo $args['before_title'] . esc_html( $title ) . $args['after_title'];  // @codingStandardsIgnoreLine here I can't escape output $args['before_title'] and $args['after_title'] because other functionality will be damaged (example from https://developer.wordpress.org/reference/classes/wp_widget_search/widget/)?></div>
            <?php } ?>
            <div class="live_match_widget_div">
                <div data-place="widget-live-match">Live Match will be here</div>
            </div>
		</div>
		<?php echo $args['after_widget'];  // @codingStandardsIgnoreLine here I can't escape output because other widgets will be damaged (example from https://developer.wordpress.org/reference/classes/wp_widget_search/widget/)
		wp_enqueue_script( 'widget-logic_live_match_widget');
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$defaults = array(
			'title' => esc_html__( 'Live Match', 'widget-logic' )
        );
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title    = esc_attr( $instance['title'] );
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php esc_html_e( 'Title:', 'widget-logic' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
        <?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = wp_strip_all_tags($new_instance['title']);

		return $instance;
	}
}

if (version_compare(get_bloginfo('version'), '5.0', '>=')) {
    include_once 'block_widget/index.php';
} else {
	add_action('widgets_init', function() {
		register_widget( 'Widget_Logic_Live_Match_Widget' );
	});
}

add_action('wp_enqueue_scripts', function() {
	$cfg = require('widget_cfg.php');
	$url = $cfg['base'];
	$ver = $cfg['ver'];
	$t = time();
	$t = $t - $t%(12*60*60);

	wp_register_script('widget-logic_live_match_widget', "{$url}{$ver}/js/data.js?t={$t}", array(),  '6.0.4', true);
});
