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

if ( ! class_exists( 'RenderDataLayerGtm' ) ) :
	class RenderDataLayerGtm{
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
			add_action('wp_head', array($this, 'search_page_trigger'), 30);

			$options = get_option('tracking_option');
			$checked = (isset($options['data_layer_google_tag_manager']) && $options['data_layer_google_tag_manager'] == 1) ? 1 : 0;
			if ($checked):
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

			$options = get_option('tracking_option');
			$checked = ( isset($options['data_layer_google_tag_manager']) && $options['data_layer_google_tag_manager'] == 1) ? 1 : 0;
			if($checked):

				if(is_order_received_page()) {
					$this->mount_purchase_data_layer();
				}
			endif;
		}

		private function mount_purchase_data_layer(){
			$transactionId = empty($_GET[ 'order' ]) ? ($GLOBALS[ 'wp' ]->query_vars[ 'order-received' ] ? $GLOBALS[ 'wp' ]->query_vars[ 'order-received' ] : 0) : absint($_GET[ 'order' ]);

			if(!empty($transactionId)){
				$order = wc_get_order( $transactionId );
				$user = $order->get_user();
				$products = $order->get_items();
				$transactionTotal = $order->get_total();
				$transactionTax = $order->get_total_tax();
				$transactionShipping = $order->get_shipping_total();

				$product_data = [];
				foreach ($order->get_items() as $item) {
					$sku  = $item->get_product_id();
					$price = $item->get_total();

					$product_categories = get_the_terms($sku, 'product_cat');
					if ((is_array($product_categories)) && (count($product_categories) > 0)) {
						$product_cat = array_pop($product_categories);
						$product_cat = $product_cat->name;
					} else {
						$product_cat = '';
					}

					$product_data[]  = [
						'name'     => $item['name'], //Required
						'sku'      => $sku, //Required
						'category' => $product_cat, //Optional
						'price'    => $price, //Required
						'quantity' => $item['qty'] //Required
					];
				}

				$dataLayer = [
					'transactionId'=>$transactionId, //Required
//                'transactionAffiliation'=>, //Optional
					'transactionTotal'=> $transactionTotal, //Required
					'transactionShipping'=> $transactionShipping, //Optional
					'transactionTax'=> $transactionTax, //Optional
					'transactionProducts'=> $product_data
				];

			}

			$this->push_to_data_layer($dataLayer);
		}

		private function push_to_data_layer($dataLayer){
			$encodedDataLayer = json_encode($dataLayer);
			$scriptTag = '<script data-cfasync="false" type="text/javascript">if(typeof dataLayer=="undefined"){dataLayer = []} dataLayer.push( %s );</script>';
			echo sprintf($scriptTag, $encodedDataLayer);
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
                <p><b>DataLayer Google Tag Manager for Woocommerce</b> requires Woocommerce activated. <a href='/wp-admin/options-general.php?page=wp-tracking-codes&desativate_datalayer=true'>Disable function</a>.</p>
                <button type='button' class='notice-dismiss'>
                    <span class='screen-reader-text'>Dismiss this notice.</span>
                </button>
            </div>");
		}

	}
	RenderDataLayerGtm::get_instance();
endif;
