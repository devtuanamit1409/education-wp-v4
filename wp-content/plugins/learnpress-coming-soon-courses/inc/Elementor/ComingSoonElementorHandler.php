<?php
/**
 * Class ComingSoonElementorHandler
 *
 * Hook to register widgets, dynamic tags, ... for LearnPress Elementor handler.
 *
 * @since 4.0.6
 * @version 1.0.0
 */
namespace LP_Addon_Coming_Soon\Elementor;

use LearnPress\Helpers\Singleton;
use LP_Addon_Coming_Soon\Elementor\Widgets\CourseComingSoonCountdown;
use LP_Addon_Coming_Soon\Elementor\Widgets\CourseComingSoonMessage;
use LP_Addon_Coming_Soon_Courses_Preload;

class ComingSoonElementorHandler {
	use Singleton;

	/**
	 * Hooks to register widgets, dynamic tags, ...
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'lp/elementor/widgets', [ $this, 'register_widgets' ] );
		add_filter( 'elementor/frontend/widget/should_render', array( $this, 'thim_ekits_should_render' ), 10, 2 );
	}

	/**
	 * @param $lp_widgets array
	 * @return mixed
	 */
	public function register_widgets( array $lp_widgets ): array {
		include_once LP_ADDON_COMING_SOON_COURSES_PATH . '/inc/Elementor/Widgets/CourseComingSoonCountdown.php';
		include_once LP_ADDON_COMING_SOON_COURSES_PATH . '/inc/Elementor/Widgets/CourseComingSoonMessage.php';

		$lp_widgets['coming-soon-countdown'] = CourseComingSoonCountdown::class;
		$lp_widgets['coming-soon-message']   = CourseComingSoonMessage::class;

		return $lp_widgets;
	}

	/**
	 * @param $should_render
	 * @param $element
	 * @return false|mixed
	 * @author tuanta
	 */
	public function thim_ekits_should_render( $should_render, $element ) {
		$widgets_no_load = [
			'thim-loop-item-read-more',
			'thim-loop-item-excerpt',
		];

		if ( LP_Addon_Coming_Soon_Courses_Preload::$addon->is_coming_soon( get_the_ID() ) ) {
			if ( in_array( $element->get_name(), $widgets_no_load ) ) {
				return false;
			}

			$show_course_meta_data = get_post_meta( get_the_ID(), '_lp_coming_soon_metadata', true ) === 'yes';
			$widgets_no_load 	 = [
				'thim-loop-item-info',
				'thim-ekits-course-meta',
			];
			if ( in_array( $element->get_name(), $widgets_no_load ) && ! $show_course_meta_data ) {
				return false;
			}
		}

		return $should_render;
	}
}
