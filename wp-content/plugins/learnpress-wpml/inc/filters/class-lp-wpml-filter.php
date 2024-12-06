<?php
/**
 * Class LP_WPML_Filter
 *
 * @author  ThimPress
 * @package LearnPress/Classes/Filters
 * @since  4.0.0
 * @version 1.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( class_exists( 'LP_WPML_Filter' ) ) {
	return;
}

class LP_WPML_Filter extends LP_Filter {
	/**
	 * @var string
	 */
	public $element_type = '';
	/**
	 * @var string[]
	 */
	public $element_types = [];
	/**
	 * @var array
	 */
	public $translation_id = [];
	/**
	 * @var array
	 */
	public $element_id = [];
	/**
	 * @var array
	 */
	public $trid = [];
	/**
	 * @var string
	 */
	public $language_code = '';
	/**
	 * @var string
	 */
	public $source_language_code = '';
	/**
	 * @var string
	 */
	public $field_count = 'translation_id';
}
