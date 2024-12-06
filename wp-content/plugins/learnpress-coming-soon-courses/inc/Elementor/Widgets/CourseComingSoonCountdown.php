<?php
/**
 * Class CourseComingSoonCountdown
 *
 * @sicne 4.0.6
 * @version 1.0.0
 */
namespace LP_Addon_Coming_Soon\Elementor\Widgets;

use LearnPress\ExternalPlugin\Elementor\LPElementorWidgetBase;
use LearnPress\ExternalPlugin\Elementor\Widgets\Course\SingleCourseBaseElementor;
use LP_Addon_Coming_Soon_Courses_Preload;

class CourseComingSoonCountdown extends LPElementorWidgetBase {
	use SingleCourseBaseElementor;

	public function __construct( $data = [], $args = null ) {
		$this->title    = esc_html__( 'Coming Soon Countdown', 'learnpress-coming-soon-courses' );
		$this->name     = 'coming_soon_countdown';
		$this->keywords = [ 'coming soon countdown' ];
		parent::__construct( $data, $args );
	}

	protected function register_controls() {
		$this->controls = require_once LP_ADDON_COMING_SOON_COURSES_PATH . '/config/elementor/countdown.php';
		parent::register_controls();
	}

	/**
	 * Show content of widget
	 *
	 * @return void
	 */
	protected function render() {
		try {
			$course = $this->get_course();
			if ( ! $course ) {
				return;
			}

			LP_Addon_Coming_Soon_Courses_Preload::$addon->coming_soon_countdown();
		} catch ( \Throwable $e ) {
			error_log( $e->getMessage() );
		}
	}
}
