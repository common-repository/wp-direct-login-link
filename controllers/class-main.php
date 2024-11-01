<?php
/**
 * Main controller
 *
 * @package wpmll
 * @subpackage controllers
 * @since WP Direct Login Link 1.0
 */

namespace Wpmll\Controllers;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once WPMLL_PLUGIN_PATH . 'controllers/class-magic.php';
require_once WPMLL_PLUGIN_PATH . 'controllers/class-db.php';
use Wpmll\Controllers\WpmllMagic;
use Wpmll\Controllers\WpmllDb;

/**
 * Class Main Controller
 *
 * @category Controller
 * @package wpmll
 * @author  
 * @license  
 * @link     
 */
class WpmllMain {

	protected $site_option_slug = 'wpmll_site_options_value';
	protected $wpmll = array(
		'wpmll_enabled' => 0,
		'wpmll_form_type' => 0,
		'wpmll_enabled_woo' => 0,
		'wpmll_link_validity' => 3,
		'wpmll_same_ip' => 1,
		'wpmll_allow_guests' => 0,
		'wpmll_default_role' => 'subscriber',
		'wpmll_display_login_form' => 0,
		'wpmll_redirect_to' => 'profile',
		'wpmll_redirect_url' => false,
		'wpmll_redirect_page_id' => false,
		'wpmll_allowed_domains' => 'all',
		'wpmll_allowed_domains_list' => array(),
		'wpmll_allowed_emails_list' => array(),
		'wpmll_email_sender' => false,
		'wpmll_email_subject' => false,
		'wpmll_email_content' => false,
	);

	function admin_enqueue( $hook ) {
		if ( in_array( $hook, array( 'toplevel_page_wpmll', 'magic-link_page_wpmll-reports' ) ) && is_admin() ) {
			wp_enqueue_style( 'wpmll-be-style', WPMLL_PLUGIN_URL . 'assets/css/wpmll-be-style.css', array(), '1.0' );
			//wp_enqueue_script('jquery');
			wp_enqueue_script( 'wpmll-wpadmin-scripts', WPMLL_PLUGIN_URL . 'assets/js/be-scripts.js', array( 'jquery' ), '1.0', true );
		}
	}

	function wpmll_enqueue_magic_form_scripts( $hook ) {
		wp_enqueue_style( 'wpmll-magic-form', WPMLL_PLUGIN_URL . 'assets/css/wpmll-form.css', array(), '1.0' );
	}

	function login_page_init() {
		$wpmll = $this->get_settings();
		if ( isset( $_GET['wpmll_magic'] ) ) {
			$magic = new WpmllMagic();
			$current_ip = false;
			if ( 1 == $wpmll['wpmll_same_ip'] ) {
				$current_ip = $_SERVER['REMOTE_ADDR'];
			}
			$update_status = $magic->check_magic_link_entry_and_invalidate( $wpmll, $_GET['wpmll_magic'], $current_ip );

			if ( $wpmll && $update_status['success'] ) {
				// if only registered users allowed
				if ( 1 != $wpmll['wpmll_allow_guests'] ) {
					$user = get_user_by( 'email', $update_status['email'] );
					if ( ! is_wp_error( $user ) && $user ) {

						switch ( $wpmll['wpmll_redirect_to'] ) {
							case 'url':
								$redirect = $wpmll['wpmll_redirect_url'];
								break;
							case 'page':
								$redirect = get_permalink( $wpmll['wpmll_redirect_page_id'] );
								break;
							default:
								$redirect = admin_url( 'profile.php' );
								break;
						}

						wp_clear_auth_cookie();
						wp_set_current_user ( $user->ID );
						wp_set_auth_cookie  ( $user->ID );

						wp_redirect( $redirect );
						echo json_encode( array( 'error_code' => 0 ) );
						exit();
					} else {
						echo json_encode( array( 'error_code' => 1 ) );
						exit();
					}
				} else {
					$user = get_user_by( 'email', $update_status['email'] );
					if ( ! $user ) {
						$user_data = array(
							'user_login' => $update_status['email'],
							'user_email' => $update_status['email'],
							'user_pass'  => wp_generate_password(),
							'first_name' => $update_status['email'],
							'nickname'   => $update_status['email'],
							'role'       => $wpmll['wpmll_default_role'],
						);
						$user_id = wp_insert_user( $user_data );
					} else {
						$user_id = $user->ID;
					}

					if ( ! is_wp_error( $user_id ) ) {
						switch ( $wpmll['wpmll_redirect_to'] ) {
							case 'url':
								$redirect = $wpmll['wpmll_redirect_url'];
								break;
							case 'page':
								$redirect = get_permalink( $wpmll['wpmll_redirect_page_id'] );
								break;
							default:
								$redirect = admin_url( 'profile.php' );
								break;
						}

						wp_clear_auth_cookie();
						wp_set_current_user ( $user_id );
						wp_set_auth_cookie  ( $user_id );

						wp_redirect( $redirect );
						echo json_encode( array( 'error_code' => 0 ) );
						exit();
					} else {
						echo json_encode( array( 'error_code' => 3 ) );
						exit();
					}
				}
			} else {
				wp_redirect( add_query_arg( 'wpmll_error', urlencode( $update_status['message'] ), remove_query_arg( 'wpmll_magic' ) ) );
			}
		}
	}

	function wpmll_display_magic_form_header() {
		$wpmll = $this->get_settings();
		if ( isset( $wpmll['wpmll_enabled'] ) && 1 == $wpmll['wpmll_enabled'] ) {
			if ( isset( $wpmll['wpmll_form_type'] ) && 1 == $wpmll['wpmll_form_type'] ) {
				$this->wpmll_display_magic_form_action();
				include WPMLL_PLUGIN_PATH . 'views/wpmll-form.php';
				exit();
			}
		}
	}

	function wpmll_display_magic_form_footer() {
		$wpmll = $this->get_settings();
		if ( isset( $wpmll['wpmll_enabled'] ) && 1 == $wpmll['wpmll_enabled'] ) {
			if ( isset( $wpmll['wpmll_form_type'] ) && 1 != $wpmll['wpmll_form_type'] ) {
				$this->wpmll_display_magic_form_action();
				include WPMLL_PLUGIN_PATH . 'views/wpmll-form.php';
			}
		}
	}

	function wpmll_display_magic_form_footer_woo() {
		$wpmll = $this->get_settings();
		if ( isset( $wpmll['wpmll_enabled'] ) && 1 == $wpmll['wpmll_enabled'] && isset( $wpmll['wpmll_enabled_woo'] ) && 1 == $wpmll['wpmll_enabled_woo'] ) {
			wp_enqueue_style( 'wpmll-magic-form', WPMLL_PLUGIN_URL . 'assets/css/wpmll-form.css', array(), '1.0' );
			$this->wpmll_display_magic_form_action();
			include WPMLL_PLUGIN_PATH . 'views/wpmll-form.php';
		}
	}

	function wpmll_display_magic_form_checkout_woo() {
		if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
			return;
		}
		$wpmll = $this->get_settings();
		if ( isset( $wpmll['wpmll_enabled'] ) && 1 == $wpmll['wpmll_enabled'] && isset( $wpmll['wpmll_enabled_woo'] ) && 1 == $wpmll['wpmll_enabled_woo'] ) {
			wp_enqueue_style( 'wpmll-magic-form', WPMLL_PLUGIN_URL . 'assets/css/wpmll-form.css', array(), '1.0' );
			$this->wpmll_display_magic_form_action();
			include WPMLL_PLUGIN_PATH . 'views/wpmll-form.php';
		}
	}

	function wpmll_display_magic_form_action() {
		if ( isset( $_POST['wpmll_action'] ) && 'send_magic' == sanitize_text_field($_POST['wpmll_action']) ) {
			if ( isset( $_POST['wpmll_email'] ) && trim( sanitize_email($_POST['wpmll_email'] )) ) {
				$email = sanitize_text_field( wp_unslash( $_POST['wpmll_email'] ) );
				if ( ! $email ) {
					wp_redirect( add_query_arg( 'wpmll_error', urlencode( esc_html__( 'Please submit a valid email address', 'wp-direct-login-link' ) ), remove_query_arg( 'wpmll_success' ) ) );
				}
				$magic = new WpmllMagic();
				$response = $magic->create_magic_entry( $email, $_SERVER['REMOTE_ADDR'] );
				if ( $response['success'] ) {
					$url = add_query_arg( 'wpmll_success', urlencode( $response['message'] ), remove_query_arg( 'wpmll_error' ) );
					wp_safe_redirect( $url );
				} else {
					wp_redirect( add_query_arg( 'wpmll_error', urlencode( $response['message'] ), remove_query_arg( 'wpmll_success' ) ) );
					exit;
				}
			} else {
				wp_redirect( add_query_arg( 'wpmll_error', urlencode( esc_html__( 'Please submit a valid email address', 'wp-direct-login-link' ) ), remove_query_arg( 'wpmll_success' ) ) );
				exit;
			}
		}
	}


	/**
	 * Init plugin admin page
	 */
	function admin_page_init() {
		add_menu_page( 'WP Direct Login Link', 'WP Direct Login', 'manage_options', 'wpmll', array( $this, 'init_backend' ), 'dashicons-admin-network' );
		add_submenu_page( 'wpmll', 'WP Direct Login Link Settings', 'Settings', 'manage_options', 'wpmll' );
		add_submenu_page( 'wpmll', 'Reports', 'Reports', 'manage_options', 'wpmll-reports', array( $this, 'init_reports_page' ) );
	}

	function get_settings() {
		$wpmll = get_option( $this->site_option_slug );
		if ( ! $wpmll ) {
			return $this->wpmll;
		} else {
			if ( isset( $wpmll['wpmll_allowed_domains_list'] ) && ! is_array( $wpmll['wpmll_allowed_domains_list'] ) ) {
				$wpmll['wpmll_allowed_domains_list'] = json_decode( $wpmll['wpmll_allowed_domains_list'], true );
			} else {
				$wpmll['wpmll_allowed_domains_list'] = array();
			}
			if ( isset( $wpmll['wpmll_allowed_emails_list'] ) ) {
				$wpmll['wpmll_allowed_emails_list'] = json_decode( $wpmll['wpmll_allowed_emails_list'], true );
			} else {
				$wpmll['wpmll_allowed_emails_list'] = array();
			}
			if ( ! isset( $wpmll['wpmll_default_role'] ) ) {
				$wpmll['wpmll_default_role'] = 'subscriber';
			}
			$this->wpmll = $wpmll;
			return $this->wpmll;
		}

	}

	function update_settings( $wpmll ) {
		update_option( $this->site_option_slug, $wpmll );
		$this->wpmll = $wpmll;
	}

	function init_backend() {

		$magic = new WpmllMagic();
		if ( isset( $_POST['wpmll-action'] ) && 'save' == sanitize_text_field($_POST['wpmll-action']) ) {
			if ( ! isset( $_POST['wpmll-action'] ) || ! wp_verify_nonce( sanitize_text_field($_POST['wpmll_nonce'] ), 'wpmll_nonce' ) ) {
				print 'Invalid nonce.';
				exit;
			} else {
				$errors = array();
				if ( 1 > sanitize_text_field($_POST['wpmll_link_validity']) || 1440 < sanitize_text_field($_POST['wpmll_link_validity'] )) {
					$errors['wpmll_link_validity'] = esc_html__( 'Please set a value between 1 and 1440' );
				} else {
					$wpmll['wpmll_link_validity'] = sanitize_text_field( wp_unslash( $_POST['wpmll_link_validity'] ) );
				}
				$wpmll['wpmll_enabled'] = sanitize_text_field( wp_unslash( $_POST['wpmll_enabled'] ) );
				if ( WPMLL_HAS_WOO ) {
					$wpmll['wpmll_enabled_woo'] = sanitize_text_field( wp_unslash( $_POST['wpmll_enabled_woo'] ) );
				}
				$wpmll['wpmll_form_type'] = sanitize_text_field( wp_unslash( $_POST['wpmll_form_type'] ) );
				$wpmll['wpmll_same_ip'] = sanitize_text_field( wp_unslash( $_POST['wpmll_same_ip'] ) );
				$wpmll['wpmll_allow_guests'] = sanitize_text_field( wp_unslash( $_POST['wpmll_allow_guests'] ) );
				$wpmll['wpmll_default_role'] = sanitize_text_field( wp_unslash( $_POST['wpmll_default_role'] ) );
				$wpmll['wpmll_redirect_to'] = sanitize_text_field( wp_unslash( $_POST['wpmll_redirect_to'] ) );

				switch ( sanitize_text_field($_POST['wpmll_redirect_to'] )) {
					case 'url':
						if ( ! trim( sanitize_text_field($_POST['wpmll_redirect_url'] )) ) {
							$errors['wpmll_redirect_url'] = esc_html__( 'You must submit a redirect url' );
						}
						break;
					case 'page':
						if ( ! ($_POST['wpmll_redirect_page_id'] )) {
							$errors['wpmll_redirect_page_id'] = esc_html__( 'You must submit a redirect url' );
						}
						break;
				}
				$wpmll['wpmll_redirect_url'] = sanitize_text_field( wp_unslash( $_POST['wpmll_redirect_url'] ) );
				$wpmll['wpmll_redirect_page_id'] = sanitize_text_field( wp_unslash( $_POST['wpmll_redirect_page_id'] ) );

				$wpmll['wpmll_email_sender'] = sanitize_email($_POST['wpmll_email_sender']);
				$wpmll['wpmll_email_subject'] = sanitize_text_field( wp_unslash( $_POST['wpmll_email_subject'] ) );
				$wpmll['wpmll_email_content'] = wp_kses_post( stripslashes( $_POST['wpmll_email_content'] ) );

				$wpmll['wpmll_allowed_domains'] = sanitize_text_field( wp_unslash( $_POST['wpmll_allowed_domains'] ) );

				$_POST['wpmll_allowed_domains_list'] = array_filter( $this->custom_sanitize_text($_POST['wpmll_allowed_domains_list']), function( $value ) { return trim( $value ) !== ''; } );

				$wpmll['wpmll_allowed_domains_list'] = wp_json_encode( $this->custom_sanitize_text($_POST['wpmll_allowed_domains_list'] ));
				if ( empty( $errors ) ) {
					$this->update_settings( $wpmll );

					$wpmll['wpmll_allowed_domains_list'] = json_decode( $wpmll['wpmll_allowed_domains_list'], true );
				}

				$_POST['wpmll_allowed_emails_list'] = array_filter( $this->custom_sanitize_email($_POST['wpmll_allowed_emails_list']), function( $value ) { return trim( $value ) !== ''; } );

				$wpmll['wpmll_allowed_emails_list'] = wp_json_encode( $this->custom_sanitize_email($_POST['wpmll_allowed_emails_list'] ));
				if ( empty( $errors ) ) {
					$this->update_settings( $wpmll );

					$wpmll['wpmll_allowed_emails_list'] = json_decode( $wpmll['wpmll_allowed_emails_list'], true );
				}
			}
		} else {
			$wpmll = $this->get_settings();
			if ( ! isset( $wpmll['wpmll_enabled_woo'] ) ) {
				$wpmll['wpmll_enabled_woo'] = 0;
			}
		}

		include WPMLL_PLUGIN_PATH . 'views/be-main.php';
	}

	function custom_sanitize_email( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();
		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			$new_input[ $key ] = sanitize_email( $val );
		}
		return $new_input;
	}

	function custom_sanitize_text( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();
		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			$new_input[ $key ] = sanitize_text_field( $val );
		}
		return $new_input;
	}

	function init_reports_page() {
		$wpmll = $this->get_settings();
		$db = new WpmllDb();
		$current_page = 1;
		if ( isset( $_GET['page_nr'] ) ) {
			$current_page = (int) $_GET['page_nr'];
		}
		$per_page = 25;
		$links_data = $db->get_entries_for_reports( $current_page, $per_page );
		$links_data_count = $db->get_total_entries_for_reports();
		$total_pages = 1;
		if ( $links_data_count && isset( $links_data_count['total_entries'] ) ) {
			$total_pages = ceil( $links_data_count['total_entries'] / $per_page );
		}

		include WPMLL_PLUGIN_PATH . 'views/be-reports.php';
	}

	function wpmll_get_status_writing( $status ) {
		switch ( $status ) {
			case -1:
				return esc_html__( 'Activated', 'wp-direct-login-link' );
				break;
			case 1:
				return esc_html__( 'Valid', 'wp-direct-login-link' );
				break;
			default:
				return esc_html__( 'Invalid', 'wp-direct-login-link' );
				break;
		}
	}

	function wpmll_gmt_to_local_timestamp( $gmt_timestamp ) {
		return get_date_from_gmt( date( 'Y-m-d H:i:s', $gmt_timestamp ), 'd M Y, H:i' );
	}
}
