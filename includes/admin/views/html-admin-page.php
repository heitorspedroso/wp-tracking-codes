<?php
/**
 * Admin help message.
 *
 * @package WooCommerce_PagSeguro/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style type="text/css">
.form-table{
	clear:none !important;
}
</style>
<div class="wrap">
	<form method="POST" action="options.php">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
					<div id="sm_pnres" class="postbox">
						<h3 class="hndle"><span>About this Plugin:</span></h3>
						<div class="inside">
							<a class="sm_button sm_pluginHome" target="_blank" href="https://github.com/heitorspedroso/wp-tracking-codes">Contribute in repository GitHub</a><br>
							<a class="sm_button sm_pluginHome" target="_blank" href="https://br.wordpress.org/plugins/wp-tracking-codes">Wordpress Plugin</a><br><br>
							<a class="button activate-now button-primary" target="_blank" href="https://wordpress.org/support/plugin/wp-tracking-codes/reviews/" style="font-size:18px;">
								Send Your Review
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="has-sidebar sm-padded">
				<div id="post-body-content" class="has-sidebar-content">
					<div class="meta-box-sortabless">
					<!-- Rebuild Area -->
						<div id="sm_rebuild" class="postbox">
							<h3 class="hndle"><span>Your Tracking Codes</span></h3>
							<div class="inside">
								<?php //echo $options = get_option( 'tracking_analytics' ); ?>
								<?php settings_fields( 'wp-tracking-codes' );	//pass slug name of page, also referred
																												//to in Settings API as option group name
								do_settings_sections( 'wp-tracking-codes' ); 	//pass slug name of page
								submit_button();
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
