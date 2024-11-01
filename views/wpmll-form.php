<?php
/**
 *
 * Login
 *
 * @package wpmll
 * @subpackage _views
 * @since WP Direct Login Link 1.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div id="wpmll-login-wrapper">
	<?php if ( isset( $_GET['wpmll_error'] ) ) : ?>
		<div class="wpmll-login-message error-message">
			<?php echo esc_html( $_GET['wpmll_error'] ); ?>
		</div>
	<?php endif; ?>
	<?php if ( isset( $_GET['wpmll_success'] ) ) : ?>
		<div class="wpmll-login-message">
			<?php echo esc_html( $_GET['wpmll_success'] ); ?>
		</div>
	<?php endif; ?>
	<div id="wpmll-login">
		<div class="wpmll-login-heading">
			<h3><?php esc_html_e( 'Direct Link', 'wp-direct-login-link' ); ?></h3>
			<p>
				<?php esc_html_e( 'Login without password: send the one time unique login link to your email and login with 1 click', 'wp-direct-login-link' ); ?>
			</p>
		</div>
		<form method="post" action="">
			<div class="pxl-row">
				<label><?php esc_html_e( 'Email address', 'wp-direct-login-link' ); ?></label>
				<input type="email" name="wpmll_email" value="" />
			</div>
			<div class="pxl-row-full">
				<input type="hidden" name="wpmll_action" value="send_magic" />
				<input type="submit" class="button-primary" name="wpmll_submit" value="<?php esc_attr_e( 'Send me the link', 'wp-direct-login-link' ); ?>" />
			</div>
		</form>
	</div>
</div>
