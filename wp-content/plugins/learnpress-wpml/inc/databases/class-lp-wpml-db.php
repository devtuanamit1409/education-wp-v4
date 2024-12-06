<?php
/**
 * Class LP_WPML_DB
 *
 * @author tungnx
 * @since 4.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit();

class LP_WPML_DB extends LP_Database {
	public $tb_icl_translations;

	public static function getInstance() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	protected function __construct() {
		global $wpdb;
		$prefix                    = $wpdb->prefix;
		$this->tb_icl_translations = $prefix . 'icl_translations';

		parent::__construct();
	}

	/**
	 * Get result on table icl_translations.
	 *
	 * @param LP_WPML_Filter $filter
	 *
	 * @return array|int|string|null
	 * @throws Exception
	 */
	public function get_icl_translations( LP_WPML_Filter $filter ) {
		$filter->collection       = $this->tb_icl_translations;
		$filter->collection_alias = 'ic_trans';
		$default_fields           = $this->get_cols_of_table( $this->tb_icl_translations );
		$filter->fields           = array_merge( $default_fields, $filter->fields );

		// Lang code
		if ( ! empty( $filter->language_code ) ) {
			$filter->where[] = $this->wpdb->prepare( "AND {$filter->collection_alias}.language_code = %s", $filter->language_code );
		}

		// Source lang code
		if ( ! empty( $filter->source_language_code ) ) {
			$filter->where[] = $this->wpdb->prepare( "AND {$filter->collection_alias}.source_language_code = %s", $filter->source_language_code );
		}

		// Element type
		if ( ! empty( $filter->element_type ) ) {
			$filter->where[] = $this->wpdb->prepare( "AND {$filter->collection_alias}.element_type = %s", $filter->element_type );
		}

		// Element types
		if ( ! empty( $filter->element_types ) ) {
			$element_types_format = LP_Helper::db_format_array( $filter->element_types, '%s' );
			$filter->where[]      = $this->wpdb->prepare( "AND {$filter->collection_alias}.element_type IN (" . $element_types_format . ')', $filter->element_types );
		}

		// Element id
		if ( ! empty( $filter->element_id ) ) {
			$element_ids_format = LP_Helper::db_format_array( $filter->element_id, '%d' );
			$filter->where[]    = $this->wpdb->prepare( "AND {$filter->collection_alias}.element_id IN (" . $element_ids_format . ')', $filter->element_id );
		}

		// Translate id
		if ( ! empty( $filter->trid ) ) {
			$element_trids_format = LP_Helper::db_format_array( $filter->trid, '%d' );
			$filter->where[]      = $this->wpdb->prepare( "AND {$filter->collection_alias}.trid IN (" . $element_trids_format . ')', $filter->trid );
		}

		return $this->execute( $filter );
	}

	/**
	 * Get trid with language code = default lang
	 * Need set:
	 * $filter->element_type
	 * $filter->element_id
	 * $filter->language_code = default lang
	 *
	 * @param LP_WPML_Filter $filter
	 *
	 * @return int
	 * @throws Exception
	 */
	public function get_trid( LP_WPML_Filter $filter ): int {
		$filter->only_fields         = array( 'trid' );
		$filter->field_count         = 'trid';
		$filter->return_string_query = true;
		$query                       = $this->get_icl_translations( $filter );

		$trid = (int) $this->wpdb->get_var( $query );

		$this->check_execute_has_error();

		return $trid;
	}

	/**
	 * Get trid max of column icl_translations
	 *
	 * @param LP_WPML_Filter $filter
	 *
	 * @return int
	 * @throws Exception
	 */
	public function get_trid_max( LP_WPML_Filter $filter ): int {
		$filter->only_fields         = [ 'MAX(trid) AS trid' ];
		$filter->return_string_query = true;
		$trid_max_query              = $this->get_icl_translations( $filter );

		$trid_max = (int) $this->wpdb->get_var( $trid_max_query );

		$this->check_execute_has_error();

		return $trid_max;
	}

	/**
	 * Get courses with lang WPML
	 *
	 * @param LP_WPML_Filter $filter
	 * @param int $total_rows
	 *
	 * @return array|null|int|string
	 * @throws Exception
	 */
	public function get_courses_wpml( LP_WPML_Filter $filter, int &$total_rows = 0 ) {

		$filter->where[] = $this->wpdb->prepare( 'AND wpml.element_type = %s', $filter->element_type );

		$filter->collection       = $this->tb_icl_translations;
		$filter->collection_alias = 'wpml';

		$filter = apply_filters( 'lp/course/wpml/query/filter', $filter );

		return $this->execute( $filter, $total_rows );
	}

	/**
	 * @param LP_WPML_Filter $filter
	 *
	 * @return array|int|object|stdClass[]|string|null
	 * @throws Exception
	 */
	public function get_courses_by_lang( LP_WPML_Filter $filter ) {
		$filter->fields[] = 'wpml.element_id';
		$filter->where[]  = $this->wpdb->prepare( 'AND wpml.language_code = %s', $filter->language_code );

		$filter = apply_filters( 'lp/addon-wpml/course/query/filter/get-courses-by-lang', $filter );

		return $this->get_courses_wpml( $filter );
	}

	/**
	 * Get trid by lang
	 *
	 * @param LP_WPML_Filter $filter
	 *
	 * @return array|int|object|stdClass[]|string|null
	 * @throws Exception
	 */
	public function get_trid_by_lang( LP_WPML_Filter $filter ) {
		$filter->fields[] = 'wpml.trid';
		$filter->where[]  = $this->wpdb->prepare( 'AND wpml.language_code = %s', $filter->language_code );

		$filter = apply_filters( 'lp/addon-wpml/course/query/filter/get-trid-by-lang', $filter );

		return $this->get_courses_wpml( $filter );
	}

	/**
	 * Query get courses not translate lang, will get default lang
	 *
	 * @param LP_WPML_Filter $filter
	 * @param string $query_trid_course_translated
	 *
	 * @return array|int|object|stdClass[]|string|null
	 * @throws Exception
	 */
	public function get_courses_by_default_lang_not_translate_by_lang( LP_WPML_Filter $filter, string $query_trid_course_translated = '' ) {
		$filter->fields[] = 'wpml.element_id';
		$filter->where[]  = $this->wpdb->prepare( 'AND wpml.language_code = %s', $filter->language_code );
		$filter->where[]  = 'AND wpml.trid NOT IN (' . $query_trid_course_translated . ')';

		$filter = apply_filters( 'lp/addon-wpml/course/query/filter/get-course-default-lang-if-not-translate', $filter );

		return $this->get_courses_wpml( $filter );
	}

	/**
	 * Get trids by element_ids
	 *
	 * @param LP_WPML_Filter $filter
	 * @param array $element_ids
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_trids_by_element_ids( LP_WPML_Filter $filter, array $element_ids ): array {
		$filter->element_id  = ! empty( $element_ids ) ? $element_ids : array( 0 );
		$filter->only_fields = [ 'DISTINCT(trid)' ];
		$filter->field_count = 'trid';
		$filter->limit       = -1;
		$trids               = $this->get_icl_translations( $filter );
		$trids               = $this->get_values_by_key( $trids, 'trid' );

		return $trids;
	}

	/**
	 * Get element_ids by trids
	 *
	 * @param LP_WPML_Filter $filter
	 * @param array $trids
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_element_ids_by_trids( LP_WPML_Filter $filter, array $trids ): array {
		$trids_format        = LP_Helper::db_format_array( $trids, '%d' );
		$filter              = new LP_WPML_Filter();
		$filter->only_fields = [ 'element_id' ];
		$filter->field_count = 'element_id';
		$filter->where[]     = $this->wpdb->prepare( 'AND trid IN (' . $trids_format . ')', $trids );
		$filter->limit       = -1;
		$element_ids         = $this->get_icl_translations( $filter );
		$element_ids         = LP_Database::get_values_by_key( $element_ids, 'element_id' );

		return $element_ids;
	}

	/**
	 * Get element_ids by trids
	 *
	 * @param LP_WPML_Filter $filter
	 * @param array $trids
	 *
	 * @return bool|int|mysqli_result|resource
	 * @throws Exception
	 */
	public function delete_by_trids( LP_WPML_Filter $filter, array $trids ) {
		$trids_will_delete_format = LP_Helper::db_format_array( $trids, '%d' );
		$filter->collection       = $this->tb_icl_translations;
		$filter->where[]          = $this->wpdb->prepare( 'AND trid IN (' . $trids_will_delete_format . ')', $trids );

		return $this->delete_execute( $filter );
	}
}

LP_WPML_DB::getInstance();

