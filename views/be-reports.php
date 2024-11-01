<?php
/**
 *
 * Backend view
 *
 * @package wpmll
 * @subpackage views
 * @since WP Direct Login Link 1.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="wpmll-backend-wrapper" id="wpmll-be-main">
	<div class="wpmll-hero">
		<div class="wpmll-hero-intro">
			<h1><?php esc_html_e( 'Reports - WP Direct Link Login', 'wp-direct-login-link' ); ?></h1>
			<p>
				<?php esc_html_e( 'Find below the login data - who asked for Direct Link, how many users accessed the link etc.', 'wp-direct-login-link' ); ?>
			</p>
		</div>
	</div>

	<div class="wpmll-content">
		<?php if ( ! empty( $links_data ) ) : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th class="manage-column column-cb check-column num index-col">#</th>
						<th class="manage-column column-primary">
							<?php esc_html_e( 'Email', 'wp-direct-login-link' ); ?>
						</th>
						<th class="manage-column column-primary">
							<?php esc_html_e( 'IP', 'wp-direct-login-link' ); ?>
						</th>
						<th class="manage-column column-primary">
							<?php esc_html_e( 'Link Requested Time', 'wp-direct-login-link' ); ?>
						</th>
						<th class="manage-column column-primary">
							<?php esc_html_e( 'Status', 'wp-direct-login-link' ); ?>
						</th>
						<th class="manage-column column-primary">
							<?php esc_html_e( 'Login Time', 'wp-direct-login-link' ); ?>
						</th>
					</tr>
				</thead>
				<?php foreach ( $links_data as $key => $link_data ) : ?>
					<tr>
						<td class="manage-column column-cb check-column num index-col">
							<?php echo (($current_page - 1) * $per_page) + $key + 1; ?>
						</td>
						<td>
							<?php echo esc_html( $link_data['user_email'] ); ?>
						</td>
						<td>
							<?php echo esc_html( $link_data['user_ip'] ); ?>
						</td>
						<td>
							<?php echo esc_html( $this->wpmll_gmt_to_local_timestamp( $link_data['timestamp'] ) ); ?>
						</td>
						<td>
							<?php if ( 1 == $link_data['status'] ) : ?>
								<?php if ( time() < ( $wpmll['wpmll_link_validity'] * 60 + $link_data['timestamp'] ) ) : ?>
									<?php echo esc_html( $this->wpmll_get_status_writing( $link_data['status'] ) ); ?>
								<?php else : ?>
									<?php esc_html_e( 'Expired', 'wp-direct-login-link' ); ?>
								<?php endif; ?>
							<?php else : ?>
								<?php echo esc_html( $this->wpmll_get_status_writing( $link_data['status'] ) ); ?>
							<?php endif; ?>
						</td>
						<td>
							<?php echo $link_data['activated_timestamp'] ? esc_html( $this->wpmll_gmt_to_local_timestamp( $link_data['activated_timestamp'] ) ) : ''; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php if ( 1 < $total_pages ) : ?>
				<ul class="wpmll-pagination">
					<?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
						<li>
							<a href="<?php echo esc_url( add_query_arg( 'page_nr', $i ) ); ?>" class="<?php echo esc_attr( $i == $current_page ? 'wpmll-pagination-active' : '' ); ?>"><?php echo esc_html( $i ); ?></a>
						</li>
					<?php endfor; ?>
				</ul>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
