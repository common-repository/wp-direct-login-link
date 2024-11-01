<?php
/**
 *
 * Backend view
 *
 * @package wpmll
 * @subpackage _views
 * @since WP Direct Login Link 1.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<style type="text/css">
	.pxl-label {font-weight: bold;padding-top: 15px;}
	#wpmll_link_validity {display: block;}
	.pxl-row {margin-top: 15px;}
	#wpbody-content{margin-bottom: 20px;}
	#wp-wpmll_email_content-wrap {max-width: 98.5%;}
</style>
<div class="wpmll-backend-wrapper" id="wpmll-be-main">
	<div class="wpmll-hero">
		<div class="wpmll-hero-intro">
			<h1><?php esc_html_e( 'WP Direct Login Link', 'wp-direct-login-link' ); ?></h1>
			<p>
				<?php esc_html_e( 'Allow users to login and register without password. Send them login link via email. Secure and simple.', 'wp-direct-login-link' ); ?>
			</p>
		</div>
	</div>

	<div class="wpmll-content">
		<form action="" method="post">
			<div class="pxl-row">
				<label class="pxl-label"><?php esc_html_e( 'Direct Login Link functionality is:', 'wp-direct-login-link' ); ?></label>
				<div class="pxl-radio-group">
					<label>
						<input type="radio" name="wpmll_enabled" value="1" <?php echo esc_attr( 1 == $wpmll['wpmll_enabled'] ? 'checked' : '' ); ?> />
						<span><?php esc_html_e( 'Enabled', 'wp-direct-login-link' ); ?></span>
					</label>
					<label>
						<input type="radio" name="wpmll_enabled" value="0" <?php echo esc_attr( 1 != $wpmll['wpmll_enabled'] ? 'checked' : '' ); ?> />
						<span><?php esc_html_e( 'Disabled', 'wp-direct-login-link' ); ?></span>
					</label>
				</div>
			</div>
			<div class="wpmll-enabled-form <?php echo 1 == $wpmll['wpmll_enabled'] ? 'is-enabled' : ''; ?>">
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Direct link form should:', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-radio-group">
						<label>
							<input type="radio" name="wpmll_form_type" value="1" <?php echo esc_attr( 1 == $wpmll['wpmll_form_type'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Replace the login form', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_form_type" value="0" <?php echo esc_attr( 1 != $wpmll['wpmll_form_type'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Diplayed below login form', 'wp-direct-login-link' ); ?></span>
						</label>
					</div>
				</div>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Link validity (minutes)', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-slider-group">
						<input type="range" min="1" max="60" value="<?php echo esc_attr( $wpmll['wpmll_link_validity'] ); ?>" step="1" oninput="outputUpdate(value)" id="wpmll_link_validity_slider">
						<input type="number" name="wpmll_link_validity" value="<?php echo esc_attr( $wpmll['wpmll_link_validity'] ); ?>" id="wpmll_link_validity" />
					</div>
				</div>
				<?php if ( WPMLL_HAS_WOO ) : ?>
					<div class="pxl-row">
						<label class="pxl-label"><?php esc_html_e( 'Display Direct Link Form for WooCommerce pages?', 'wp-direct-login-link' ); ?></label>
						<div class="pxl-radio-group">
							<label>
								<input type="radio" name="wpmll_enabled_woo" value="1" <?php echo esc_attr( 1 == $wpmll['wpmll_enabled_woo'] ? 'checked' : '' ); ?> />
								<span><?php esc_html_e( 'Display', 'wp-direct-login-link' ); ?></span>
							</label>
							<label>
								<input type="radio" name="wpmll_enabled_woo" value="0" <?php echo esc_attr( 1 != $wpmll['wpmll_enabled_woo'] ? 'checked' : '' ); ?> />
								<span><?php esc_html_e( 'Do not display', 'wp-direct-login-link' ); ?></span>
							</label>
						</div>
					</div>
				<?php endif; ?>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'User should login from the same IP that requested Direct Link?', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-radio-group">
						<label>
							<input type="radio" name="wpmll_same_ip" value="1" <?php echo esc_attr( 1 == $wpmll['wpmll_same_ip'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Yes', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_same_ip" value="0" <?php echo esc_attr( 1 != $wpmll['wpmll_same_ip'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'No', 'wp-direct-login-link' ); ?></span>
						</label>
					</div>
				</div>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Alloweded users to login', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-radio-group">
						<label>
							<input type="radio" name="wpmll_allow_guests" value="0" <?php echo esc_attr( 1 != $wpmll['wpmll_allow_guests'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Only registered users', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_allow_guests" value="1" <?php echo esc_attr( 1 == $wpmll['wpmll_allow_guests'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'All users', 'wp-direct-login-link' ); ?></span>
						</label>
					</div>
				</div>

				<!-- Roles -->
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Assign role for new users', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-radio-group">
						<select name="wpmll_default_role">
							<?php wp_dropdown_roles( $wpmll['wpmll_default_role'] ); ?>
						</select>
					</div>
				</div>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'On successful login, redirect user to:', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-radio-group">
						<label>
							<input type="radio" name="wpmll_redirect_to" value="profile" <?php echo esc_attr( 'profile' == $wpmll['wpmll_redirect_to'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Profile page', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_redirect_to" value="url" <?php echo esc_attr( 'url' == $wpmll['wpmll_redirect_to'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Custom url', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_redirect_to" value="page" <?php echo esc_attr( 'page' == $wpmll['wpmll_redirect_to'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Page', 'wp-direct-login-link' ); ?></span>
						</label>
					</div>
				</div>
				<div class="pxl-row" id="pxl-redirect-url">
					<label class="pxl-label"><?php esc_html_e( 'Redirect to this url:', 'wp-direct-login-link' ); ?></label>
					<input type="url" name="wpmll_redirect_url" value="<?php echo esc_attr( $wpmll['wpmll_redirect_url'] ); ?>" />
				</div>
				<div class="pxl-row" id="pxl-redirect-page">
					<label class="pxl-label"><?php esc_html_e( 'Redirect to this page:', 'wp-direct-login-link' ); ?></label>
					<?php
					$args = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => ( isset( $wpmll['wpmll_redirect_page_id'] ) && 0 < $wpmll['wpmll_redirect_page_id'] ) ? $wpmll['wpmll_redirect_page_id'] : 0,
						'echo'                  => 1,
						'name'                  => 'wpmll_redirect_page_id',
						'id'                    => null,
						'class'                 => null,
						'show_option_none'      => null,
						'show_option_no_change' => null,
						'option_none_value'     => null,
					);
					wp_dropdown_pages( $args );
					?>
				</div>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Allowed domains to login', 'wp-direct-login-link' ); ?></label>
					<div class="pxl-radio-group">
						<label>
							<input type="radio" name="wpmll_allowed_domains" value="all" <?php echo esc_attr( 'all' == $wpmll['wpmll_allowed_domains'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'All', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_allowed_domains" value="custom" <?php echo esc_attr( 'custom' == $wpmll['wpmll_allowed_domains'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Only specific domains', 'wp-direct-login-link' ); ?></span>
						</label>
						<label>
							<input type="radio" name="wpmll_allowed_domains" value="emails" <?php echo esc_attr( 'emails' == $wpmll['wpmll_allowed_domains'] ? 'checked' : '' ); ?> />
							<span><?php esc_html_e( 'Only specific emails', 'wp-direct-login-link' ); ?></span>
						</label>
					</div>
				</div>

				<div class="pxl-row" id="wpmll-allowed-domains">
					<label class="pxl-label"><?php esc_html_e( 'Allowed domains', 'wp-direct-login-link' ); ?></label>
					<div class="wpmll-allowed-domains-list">
						<?php if ( ! empty( $wpmll['wpmll_allowed_domains_list'] ) ) : ?>
							<?php foreach ( $wpmll['wpmll_allowed_domains_list'] as $key => $value ) : ?>
								<div class="wpmll-allowed-domains-list-item">
									@<input type="text" name="wpmll_allowed_domains_list[]" value="<?php echo esc_attr( $value ); ?>" />
									<a href="#" class="wpmll-delete-allowed-domain"><?php esc_html_e( 'Remove', 'wp-direct-login-link' ); ?></a>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						<div class="wpmll-allowed-domains-list-item">
							@<input type="text" name="wpmll_allowed_domains_list[]" />
							<a href="#" class="wpmll-delete-allowed-domain"><?php esc_html_e( 'Remove', 'wp-direct-login-link' ); ?></a>
						</div>
					</div>
					<a href="#" class="wpmll-add-allowed-domain button-secondary"><?php esc_html_e( 'Add New Entry', 'wp-direct-login-link' ); ?></a>
				</div>

				<div class="pxl-row" id="wpmll-allowed-emails">
					<label class="pxl-label"><?php esc_html_e( 'Allowed emails', 'wp-direct-login-link' ); ?></label>
					<div class="wpmll-allowed-emails-list">
						<?php if ( ! empty( $wpmll['wpmll_allowed_emails_list'] ) ) : ?>
							<?php foreach ( $wpmll['wpmll_allowed_emails_list'] as $key => $value ) : ?>
								<div class="wpmll-allowed-emails-list-item">
									<input type="email" name="wpmll_allowed_emails_list[]" value="<?php echo esc_attr( $value ); ?>" />
									<a href="#" class="wpmll-delete-allowed-email"><?php esc_html_e( 'Remove', 'wp-direct-login-link' ); ?></a>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						<div class="wpmll-allowed-emails-list-item">
							<input type="email" name="wpmll_allowed_emails_list[]" />
							<a href="#" class="wpmll-delete-allowed-email"><?php esc_html_e( 'Remove', 'wp-direct-login-link' ); ?></a>
						</div>
					</div>
					<a href="#" class="wpmll-add-allowed-email button-secondary"><?php esc_html_e( 'Add New Entry', 'wp-direct-login-link' ); ?></a>
				</div>

				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Email subject:', 'wp-direct-login-link' ); ?></label>
					<input type="text" name="wpmll_email_subject" value="<?php echo esc_attr( $wpmll['wpmll_email_subject'] ); ?>" />
				</div>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Email sender (FROM):', 'wp-direct-login-link' ); ?></label>
					<input type="text" name="wpmll_email_sender" value="<?php echo esc_attr( $wpmll['wpmll_email_sender'] ); ?>" />
				</div>
				<div class="pxl-row">
					<label class="pxl-label"><?php esc_html_e( 'Email Content:', 'wp-direct-login-link' ); ?></label>
					<p><?php esc_html_e( "Use the shortcode {{WPMLL_LINK}} to display the direct link. If it's not used in email content, it will be appended automatically at the end of the email. For example, you can use it like this: ", 'wp-direct-login-link' ); ?></p>
					<code>&lt;a href="{{WPMLL_LINK}}"&gt;<?php esc_html_e( 'Click here to login', 'wp-direct-login-link' ); ?>&lt;/a&gt;</code>
					<?php wp_editor( $wpmll['wpmll_email_content'], 'wpmll_email_content' ); ?>
				</div>
			</div>

			<div class="pxl-row">
				<input type="hidden" name="wpmll-action" value="save" />
				<?php wp_nonce_field( 'wpmll_nonce', 'wpmll_nonce' ); ?>
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save', 'wp-direct-login-link' ); ?>" />
			</div>
		</form>
	</div>
</div>
