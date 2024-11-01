<?php
/**
 * DB controller
 *
 * @package wpmll
 * @subpackage controllers
 * @since WP Direct Login Link 1.0
 */

namespace Wpmll\Controllers;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Class DB Controller
 *
 * @category Controller
 * @package wpmll
 * @author  
 * @license  
 * @link     
 */
class WpmllDb {

	public $wpmll_db_version;
	protected $table_name;
	protected $charset_collate;

	/**
	 * Function to be called on class init
	 */
	function __construct() {
		global $wpdb;

		$this->wpmll_db_version = '1.0';
		$this->charset_collate  = $wpdb->get_charset_collate();
		// table to store data
		$this->table_name = $wpdb->base_prefix . 'wpmll_magic_links';
	}

	/**
	 * Function to create tables on plugin install
	 */
	function create_table_on_install() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// action types:
		// 0: allow non registered user to login
		// 1: allow only registered users to login
		$sql_templates = "CREATE TABLE $this->table_name (
			id int NOT NULL AUTO_INCREMENT,
			site_id int NULL,
			timestamp int NOT NULL,
			expire_timestamp int NOT NULL,
			user_ip text NOT NULL,
			user_key text NOT NULL,
			user_email text NOT NULL,
			action_type int DEFAULT 0 NULL,
			redirect_to text NULL,
			status int DEFAULT 0 NULL,
			activated_timestamp int NULL,
			PRIMARY KEY (id)
		) $this->charset_collate;";

		dbDelta( $sql_templates );

		if ( get_option( 'wpmll_db_version' ) ) {
			update_option( 'wpmll_db_version', $this->wpmll_db_version );
		} else {
			add_option( 'wpmll_db_version', $this->wpmll_db_version );
		}
	}

	/**
	 * Function to create entry
	 *
	 * @param mixed $data .
	 * @param mixed $format .
	 */
	function create_entry( $data ) {
		global $wpdb;
		$res = $wpdb->insert( $this->table_name, $data );

		return $wpdb->insert_id;
	}

	/**
	 * Function to update entry
	 *
	 * @param mixed $data .
	 * @param int $id .
	 * @param mixed $format .
	 */
	function update_entry( $data, $id ) {
		global $wpdb;
		$updated = $wpdb->update( $this->table_name, $data, array( 'id' => (int) $id ) );
		return $updated;
	}

	/**
	 * Function to delete entry
	 *
	 * @param mixed $data .
	 * @param int $id .
	 * @param mixed $format .
	 */
	function delete_entry( $id ) {
		global $wpdb;
		$deleted = $wpdb->delete( $this->table_name, array( 'id' => (int) $id ) );
		return $deleted;
	}

	/**
	 * Function to get row entry
	 *
	 * @param int $id .
	 */
	function get_entry_by_id( $id ) {
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM $this->table_name WHERE id = %d", intval( $id ) );
		$entry = $wpdb->get_row( $sql, ARRAY_A );

		return $entry;
	}

	/**
	 * Function to get row entry
	 *
	 * @param int $id .
	 */
	function get_entry_by_hash( $hash ) {
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM $this->table_name WHERE user_key = %s AND status = 1 AND activated_timestamp IS NULL", $hash );
		$entry = $wpdb->get_row( $sql, ARRAY_A );

		return $entry;
	}

	/**
	 * Function to get all entries
	 *
	 * @param int $page_nr .
	 * @param int $per_page .
	 */
	function get_entries( $page_nr = 1, $per_page = 10 ) {
		global $wpdb;
		$site_id = get_current_blog_id();
		$offset  = ( $page_nr - 1 ) * $per_page;
		$sql     = $wpdb->prepare( "SELECT * FROM $this->table_name WHERE site_id = %d ORDER BY id DESC LIMIT %d, %d", $site_id, $offset, $per_page );
		$entries = $wpdb->get_results( $sql, ARRAY_A );

		return $entries;
	}

	/**
	 * Function to get all entries
	 *
	 * @param int $page_nr .
	 * @param int $per_page .
	 */
	function get_entries_for_reports( $page_nr = 1, $per_page = 10 ) {
		global $wpdb;
		$site_id = get_current_blog_id();
		$offset  = ( $page_nr - 1 ) * $per_page;
		$sql     = $wpdb->prepare( "SELECT timestamp, expire_timestamp, user_ip, user_email, status, activated_timestamp FROM $this->table_name WHERE site_id = %d ORDER BY id DESC LIMIT %d, %d", $site_id, $offset, $per_page );
		$entries = $wpdb->get_results( $sql, ARRAY_A );

		return $entries;
	}

	/**
	 * Function to get all entries
	 *
	 * @param int $page_nr .
	 * @param int $per_page .
	 */
	function get_total_entries_for_reports() {
		global $wpdb;
		$site_id = get_current_blog_id();
		$sql     = $wpdb->prepare( "SELECT count(id) as total_entries FROM $this->table_name WHERE status = %d AND site_id = %d", 1, $site_id );
		$entries = $wpdb->get_row( $sql, ARRAY_A );

		return $entries;
	}

}
