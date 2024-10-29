<?php
/**
 * Admin help message.
 *
 * @package WpTrackingCodes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style type="text/css">
    .form-table{
        clear:none !important;
    }
    .title-section{
        position: relative;
        display: inline-block;
    }
    .title-section:after{
        content:'NEW';
        background: yellow;
        padding: 5px;
        color:#000;
        font-weight: bold;
        position: absolute;
        right: -48px;
        top: -5px;
    }
    .title-section-{
        position: relative;
        display: inline-block;
        font-size: 16px;
        margin-top: 20px;
    }
    .status{
        display: inline-block;
        position: relative;
        top: -10px;
        left: 0px;
    }
    .status .dashicons{
        font-size: 40px;
        color:green;
    }
    #google_merchant_estimative_delivery_days{
        text-align: right;
    }
</style>
<div class="wrap">
    <form method="POST" action="options.php">
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <div id="poststuff" class="metabox-holder has-right-sidebar">
            <div class="inner-sidebar">
                <div class="meta-box-sortabless ui-sortable" style="position:relative;">
                    <div id="sm_pnres" class="postbox" style="border: 5px dashed #ccd0d4;">
                        <div class="inside">
                            <h3 class="hndle"><span><?php esc_html_e( 'Datalayer for WooCommerce?', 'wp-tracking-codes' ); ?></span></h3>
                            <br>
                            <a class="button activate-now button-primary" target="_blank" href="https://woocommerce.com/products/datalayer-for-woocommerce/" style="font-size:18px;display: block;text-align: center;">
                                <?php esc_html_e( 'READ MORE', 'wp-tracking-codes' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="inner-sidebar">
                <div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
                    <div id="sm_pnres" class="postbox">
                        <h3 class="hndle"><span><?php esc_html_e( 'About this Plugin:', 'wp-tracking-codes' ); ?></span></h3>
                        <div class="inside">
                            <br>
                            <a class="button activate-now button-primary" target="_blank" href="https://wordpress.org/support/plugin/wp-tracking-codes/reviews/" style="font-size:18px;display: block;text-align: center;">
                                <?php esc_html_e( 'Send Your Review', 'wp-tracking-codes' ); ?>
                            </a>
                            <br>
                            <br>
                            <a class="sm_button sm_pluginHome" target="_blank" href="https://wordpress.org/support/plugin/wp-tracking-codes/"><?php esc_html_e( 'Support Plugin', 'wp-tracking-codes' ); ?></a><br><br>
                            <a class="sm_button sm_pluginHome" target="_blank" href="https://wordpress.org/plugins/wp-tracking-codes/"><?php esc_html_e( 'Page Plugin', 'wp-tracking-codes' ); ?></a><br><br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="has-sidebar sm-padded">
                <div id="post-body-content" class="has-sidebar-content">
                    <div class="meta-box-sortabless">
                        <!-- Rebuild Area -->
                        <div id="sm_rebuild" class="postbox">
                            <h3 class="hndle"><span><?php esc_html_e( 'My Tracking Codes', 'wp-tracking-codes' ); ?></span></h3>
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
<script>
    let searchParams = new URLSearchParams(window.location.search);
    let desativate_datalayer = searchParams.get('desativate_datalayer')
    if(desativate_datalayer=='true'){
        jQuery("#data_layer_google_tag_manager").trigger('click');
        jQuery("#submit").trigger('click');
        var href = new URL(window.location.href);
        href.searchParams.set('desativate_datalayer', '');
        location.replace(href.toString());
    }
</script>