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

require_once WPMLL_PLUGIN_PATH . 'controllers/class-db.php';
require_once WPMLL_PLUGIN_PATH . 'controllers/class-main.php';
use Wpmll\Controllers\WpmllDb;
use Wpmll\Controllers\WpmllMain as MainController;

/**
 * Class Magic Controller
 *
 * @category Controller
 * @package wpmll
 * @author  
 * @license  
 * @link     
 */
class WpmllMagic {

	function create_magic_entry( $email, $ip ) {
		$mc = new MainController();
		$magic_settings = $mc->get_settings();

		// guests are allowed to login?
		if ( 1 != $magic_settings['wpmll_allow_guests'] ) {
			$user = get_user_by( 'email', $email );
			if ( ! $user || is_wp_error( $user ) ) {
				return array(
					'success' => false,
					'message' => esc_html__( 'Email addres is not allowed.', 'wp-direct-login-link' ),
				);
				exit;
			}
		}

		// check the email in allowed list
		if ( 'custom' == $magic_settings['wpmll_allowed_domains'] && ! empty( $magic_settings['wpmll_allowed_domains_list'] ) ) {
			$matches = array_filter( $magic_settings['wpmll_allowed_domains_list'], function( $var ) use ( $email ) {
				$length = strlen( $var );
				return (substr($email, -$length) === $var);
			} );

			if ( empty( $matches ) ) {
				return array(
					'success' => false,
					'message' => esc_html__( 'Email addres is not allowed.', 'wp-direct-login-link' ),
				);
				exit;
			}
		}

		// check the email in allowed list
		if ( 'emails' == $magic_settings['wpmll_allowed_domains'] && ! empty( $magic_settings['wpmll_allowed_emails_list'] ) ) {
			$matches = array_filter( $magic_settings['wpmll_allowed_emails_list'], function( $var ) use ( $email ) {
				$length = strlen( $var );
				return (substr($email, -$length) === $var);
			} );

			if ( empty( $matches ) ) {
				return array(
					'success' => false,
					'message' => esc_html__( 'Email addres is not allowed.', 'wp-direct-login-link' ),
				);
				exit;
			}
		}

		$data = array(
			'site_id' => get_current_blog_id(),
			'timestamp' => time(),
			'expire_timestamp' => time() + 60 * $magic_settings['wpmll_link_validity'],
			'user_ip' => $ip,
			'user_key' => $this->generate_magic_hash(),
			'user_email' => $email,
			'status' => 1,
		);
		$db = new WpmllDb();
		$inserted_id = $db->create_entry( $data );
		if ( $inserted_id > 0 ) {
			$full_url = add_query_arg( 'wpmll_magic', $data['user_key'], wp_login_url() );
			$status = $this->email_magic_link( $email, $full_url, $magic_settings );

			return array(
				'success' => $status,
				'message' => esc_html__( 'The login link is on it\'s way. Please check your inbox.', 'wp-direct-login-link' ),
			);
		}
	}

	function email_magic_link( $email, $link, $magic_settings ) {
		if ( trim( $magic_settings['wpmll_email_sender'] ) ) {
			$headers[] = 'From: ' . $magic_settings['wpmll_email_sender'];
		}
		if ( trim( $magic_settings['wpmll_email_content'] ) ) {
			$content = $this->process_template_content( $magic_settings['wpmll_email_content'] );
			if ( strpos($content, '{{WPMLL_LINK}}') === false ) {
				$content .= ' {{WPMLL_LINK}}';
			}
			$content = str_replace( 'https://{{WPMLL_LINK}}', $link , $content );
			$content = str_replace( 'http://{{WPMLL_LINK}}', $link , $content );
			$content = str_replace( '{{WPMLL_LINK}}', $link , $content );
		} else {
			$content = esc_html__( 'Hello! You asked for a magic link for an easy login on our website. Here it is: ', 'wp-direct-login-link' );
			$content .= $link;
		}
		if ( trim( $magic_settings['wpmll_email_subject'] ) ) {
			$subject = $magic_settings['wpmll_email_subject'];
		} else {
			$subject = esc_html__( 'Direct Login Link you asked for', 'wp-direct-login-link' );
		}

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$email_sent = wp_mail( $email, $subject, $content, $headers );

		return $email_sent;
	}

	function check_magic_link_entry_and_invalidate( $wpmll, $magic_hash, $ip = false ) {
		$db = new WpmllDb();
		$db_entry = $db->get_entry_by_hash( $magic_hash );
		if ( ! $db_entry ) {
			return array(
				'success' => false,
				'message' => esc_html__( 'Invalid link', 'wp-direct-login-link' ),
			);
		} else {
			if ( $ip && $db_entry['user_ip'] != $ip ) {
				return array(
					'success' => false,
					'message' => esc_html__( 'You must login from the same IP', 'wp-direct-login-link' ),
				);
			}
			if ( time() < $db_entry['expire_timestamp'] ) {
				$updated = $db->update_entry( array( 'status' => -1, 'activated_timestamp' => time() ), $db_entry['id'] );
				if ( $updated ) {
					return array(
						'success' => true,
						'email' => $db_entry['user_email'],
					);
				} else {
					return array(
						'success' => false,
						'message' => esc_html__( 'An error occured. Please refresh the page or request new link.', 'wp-direct-login-link' ),
					);
				}
			} else {
				return array(
					'success' => false,
					'message' => esc_html__( 'Expired link', 'wp-direct-login-link' ),
				);
			}
		}
	}

	function process_template_content( $email_content_raw = '' ) {
		$content = convert_chars( convert_smilies( wptexturize( $email_content_raw ) ) );
		if ( isset( $GLOBALS['wp_embed'] ) ) {
			$content = $GLOBALS['wp_embed']->autoembed( $content );
		}
		$content = wpautop( $content );
		$content = do_shortcode( shortcode_unautop( $content ) );

		return $content;
	}

	function generate_magic_hash( $length = false, $separator = '-' ) {
		if ( ! is_array( $length ) || is_array( $length ) && empty( $length ) ) {
			$length = array( 8, 4, 8, 8, 4, 8 );
		}
		$hash = '';
		foreach ( $length as $key => $string_length ) {
			if ( $key > 0 ) {
				$hash .= $separator;
			}
			$hash .= $this->s4generator( $string_length );
		}

		return $hash;
	}

	function s4generator( $length ) {
		$token = '';
		$codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$max = strlen( $codeAlphabet );
		for ( $i=0; $i < $length; $i++ ) {
			$token .= $codeAlphabet[ $this->crypto_rand_secure( 0, $max-1 ) ];
		}
		return $token;
	}

	function crypto_rand_secure( $min, $max ) {
		$range = $max - $min;
		if ($range < 1) return $min;
		$log = ceil( log( $range, 2 ) );
		$bytes = (int) ( $log / 8 ) + 1;
		$bits = (int) $log + 1;
		$filter = (int) ( 1 << $bits ) - 1;
		do {
			$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
			$rnd = $rnd & $filter;
		} while ( $rnd > $range );
		return $min + $rnd;
	}

}
