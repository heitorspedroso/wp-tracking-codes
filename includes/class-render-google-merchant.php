<?php
/**
 * Wp Tracking Register Codes class
 *
 * @package WooCommerc/Classes/API
 * @version 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'RenderGoogleMerchant' ) ) :
	class RenderGoogleMerchant{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

    /**
     * Start up
     */
    private function __construct()
    {
        //Load Tracking Google Merchant Customer Reviews
        add_action( 'wp_footer',  array( $this, 'search_page_trigger' ) );


        $this->options = get_option( 'tracking_option' );
        if( isset( $this->options['google_merchant'] ) && !empty( $this->options['google_merchant'] ) && isset( $this->options['google_merchant_estimative_delivery_days'] ) && !empty( $this->options['google_merchant_estimative_delivery_days'] ) )
            if($google_merchant = $this->options['google_merchant']):
                if (!$this->hasValidRequirements()) {
                    add_action('admin_notices', [$this, 'showAdminNotice']);
                    return;
                }
            endif;
    }

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    public function search_page_trigger(){
        $this->options = get_option( 'tracking_option' );
        if( isset( $this->options['google_merchant'] ) && !empty( $this->options['google_merchant'] ) && isset( $this->options['google_merchant_estimative_delivery_days'] ) && !empty( $this->options['google_merchant_estimative_delivery_days'] ) ):
            $google_merchant = $this->options['google_merchant'];
            $google_merchant_estimative_delivery_days = $this->options['google_merchant_estimative_delivery_days'];
            if(is_order_received_page()) {
                $this->push_google_merchant_code($google_merchant,$google_merchant_estimative_delivery_days);
            }
        endif;
    }

    private function push_google_merchant_code($google_merchant,$google_merchant_estimative_delivery_days){
        $transactionId = empty($_GET[ 'order' ]) ? ($GLOBALS[ 'wp' ]->query_vars[ 'order-received' ] ? $GLOBALS[ 'wp' ]->query_vars[ 'order-received' ] : 0) : absint($_GET[ 'order' ]);

        if(!empty($transactionId)){
            $order = wc_get_order( $transactionId );
            $user = $order->get_user();
            $deliveryCountry = $order->get_shipping_country();
            $emailUser = $user->user_email;
            $date=Date('Y-m-d', strtotime('+'.$google_merchant_estimative_delivery_days.' days'));
            $locale = str_replace('_','-',get_locale());

            $product_data = [];
            foreach ($order->get_items() as $item) {
                $product_id  = $item->get_product_id();
                $product_data[]  = [
                    'gtin'      => strval($product_id),
                ];
            }

            printf('<!-- BEGIN GCR Opt-in Module Code -->
    <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
    <script>
        window.renderOptIn = function() {
            window.gapi.load(\'surveyoptin\', function() {
                window.gapi.surveyoptin.render(
                    %s
                );
            });
        }
    </script>
    <!-- END GCR Opt-in Module Code -->
    <!-- BEGIN GCR Language Code -->
    <script>
        window.___gcfg = {                
            lang: %s
        };
    </script>
    <!-- END GCR Language Code -->',
                wp_json_encode(
                    array(
                        "merchant_id" => $google_merchant,
                        "order_id" => $transactionId,
                        "email" => $emailUser,
                        "delivery_country" => $deliveryCountry,
                        "estimated_delivery_date" => $date,
                        "opt_in_style" => "BOTTOM_LEFT_DIALOG",
                        "products" => $product_data,
                    ),
                    JSON_UNESCAPED_UNICODE
                ),
                wp_json_encode($locale, JSON_UNESCAPED_UNICODE)
            );

        }

    }

    private function hasValidRequirements()
    {
//        return class_exists('WooCommerce') && floatval(WC()->version) >= 5.0;
        if ( ! function_exists( 'is_woocommerce_activated' ) ) {
            if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
        }
    }

    public function showAdminNotice()
    {
        printf("<div class='notice notice-warning is-dismissible'>
                <p><b>Google Merchant Customer Reviews for Woocommerce</b> requires Woocommerce activated. <a href='/wp-admin/options-general.php?page=wp-tracking-codes&desativate_datalayer=true'>Disable function</a>.</p>
                <button type='button' class='notice-dismiss'>
                    <span class='screen-reader-text'>Dismiss this notice.</span>
                </button>
            </div>");
    }

}
	RenderGoogleMerchant::get_instance();
endif;
