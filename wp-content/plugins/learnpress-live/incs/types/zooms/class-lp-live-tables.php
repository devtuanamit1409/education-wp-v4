<?php
/**
 * Class LP_Database
 *
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit();

class LP_Live_Database {
	private static $_instance;
	public $wpdb;
	public $tb_lp_live;

	protected function __construct() {
		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;
		$prefix = $wpdb->prefix;

		$this->wpdb       = $wpdb;
		$this->tb_lp_live = $prefix . 'learnpress_addon_live_data';
	}

	/**
	 * It creates a table in the database.
	 */
	public function create_tables() {

		$charset_collate = $this->wpdb->get_charset_collate();
		$sql             = "CREATE TABLE IF NOT EXISTS {$this->tb_lp_live} (
			id bigint(10) NOT NULL AUTO_INCREMENT,
			live_id varchar(100) NOT NULL UNIQUE DEFAULT '',
			live_type varchar(100) NOT NULL DEFAULT '',
			live_value longtext NOT NULL DEFAULT '',
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}



	/**
	 * It updates a row in the database if it exists, or inserts a new row if it doesn't
	 *
	 * @param string live_id The unique ID of the live stream.
	 * @param array data an array of data to be inserted into the database.
	 */
	public function update( string $live_id, array $data ) {

		if ( empty( $live_id ) || empty( $data ) ) {
			return false;
		}

		$checkIfExists = $this->wpdb->get_var(
			"
			SELECT live_id FROM $this->tb_lp_live
			WHERE live_id = '$live_id'"
		);
		if ( $checkIfExists == null ) {
			$data['live_id'] = $live_id;
			$this->wpdb->insert( $this->tb_lp_live, $data );
		} else {
			$this->wpdb->update( $this->tb_lp_live, $data, array( 'live_id' => $live_id ) );
		}

		$this->check_execute_has_error();
	}

	/**
	 * Check execute current has any errors.
	 *
	 * @throws Exception
	 */
	private function check_execute_has_error() {
		if ( $this->wpdb->last_error ) {
			throw new Exception( $this->wpdb->last_error );
		}
	}

	/**
	 * It gets a row from the database table `tb_lp_live` where the `live_id` column matches the value of
	 * the `` parameter
	 *
	 * @param string id The ID of the live post.
	 *
	 * @return An array of data.
	 */
	public function get_data_by_id( string $id ) {
		$sql  = $this->wpdb->prepare( "SELECT * FROM {$this->tb_lp_live} WHERE live_id = %s", $id );
		$data = $this->wpdb->get_row( $sql, ARRAY_A );
		return $data;
	}

	/**
	 * Get Instance
	 *
	 * @return LP_Live_Database
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

}
