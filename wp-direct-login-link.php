<?php
/**
 * Plugin Name: WP Direct Login Link
 * Plugin URI: 
 * Description: Add the direct login functionality on your website
 * Author: Amit Kumar
 * Version: 2.0
 * Author URI: 
 * Text Domain: wp-direct-login-link
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'WPMLL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPMLL_PLUGIN_URL', plugin_dir_url(__FILE__));

if ( class_exists( 'WooCommerce' ) ) {
	define( 'WPMLL_HAS_WOO', true );
} else {
	define( 'WPMLL_HAS_WOO', false );
}

require_once WPMLL_PLUGIN_PATH . 'controllers/class-main.php';
require_once WPMLL_PLUGIN_PATH . 'controllers/class-db.php';
use Wpmll\Controllers\WpmllMain as MainController;
use Wpmll\Controllers\WpmllDb;

register_activation_hook( __FILE__, array( new WpmllDb(), 'create_table_on_install' ) );

function wpmll_update_db_check() {
	$db = new WpmllDb();
	if ( get_option( 'wpmll_db_version' ) != $db->wpmll_db_version ) {
		$db->create_table_on_install();
	}
}
add_action( 'plugins_loaded', 'wpmll_update_db_check' );

// if is login page, init our function
add_action( 'login_init', array( new MainController(), 'login_page_init' ) );

// backend functionality
add_action( 'admin_menu', array( new MainController(), 'admin_page_init' ) );
add_action( 'admin_enqueue_scripts', array( new MainController(), 'admin_enqueue' ) );

add_action( 'login_header', array( new MainController(), 'wpmll_display_magic_form_header' ) );
add_action( 'login_footer', array( new MainController(), 'wpmll_display_magic_form_footer' ) );
add_action( 'woocommerce_after_customer_login_form', array( new MainController(), 'wpmll_display_magic_form_footer_woo' ) );
add_action( 'woocommerce_before_checkout_form', array( new MainController(), 'wpmll_display_magic_form_checkout_woo' ) );
add_action( 'login_enqueue_scripts', array( new MainController(), 'wpmll_enqueue_magic_form_scripts' ) );

add_action( 'plugins_loaded', 'wpmll_load_plugin_textdomain' );
function wpmll_load_plugin_textdomain() {
	load_plugin_textdomain( 'wp-direct-login-link', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function wpmll_app_output_buffer() {
	ob_start();
}
add_action( 'init', 'wpmll_app_output_buffer' );
