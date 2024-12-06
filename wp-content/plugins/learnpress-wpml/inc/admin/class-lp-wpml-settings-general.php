<?php

/**
 * Class LP_WPML_Settings_General
 *
 * @since 4.0.0
 * @version  1.0.0
 * @author Minhpd
 */
class LP_WPML_Settings_General {

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * LP_WPML_Settings_General Construct
	 */
	public function __construct() {
		add_filter( 'learn-press/general-settings-fields', array( $this, 'lp_wpml_custom_general_settings' ), 10, 1 );
	}

	/**
	 * Return fields name with language current.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function lp_wpml_custom_general_settings( array $fields ): array {
		$wpml_default_lang = apply_filters( 'wpml_default_language', null );
		$wpml_current_lang = apply_filters( 'wpml_current_language', null );

		$field_arr = [
			'courses_page_id',
			'profile_page_id',
			'checkout_page_id',
			'instructors_page_id',
			'single_instructor_page_id',
			'become_a_teacher_page_id',
			'term_conditions_page_id',
			'logout_redirect_page_id'
		];
		$field_arr = apply_filters( 'lp_wpml/settings/name-fields', $field_arr );

		if ( $wpml_default_lang != $wpml_current_lang ) {
			// Set name field with lang
			foreach ( $fields as $k => $field ) {
				if ( isset( $field['id'] ) && in_array( $field['id'], $field_arr ) ) {
					$fields[ $k ]['id'] = $field['id'] . '_' . $wpml_current_lang;
				}
			}
		}

		return $fields;
	}

}

LP_WPML_Settings_General::instance();
