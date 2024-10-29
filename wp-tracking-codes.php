<?php
/**
 * Plugin Name: Wp Tracking Codes
 * Plugin URI:  https://br.wordpress.org/plugins/wp-tracking-codes/
 * Description: The tracking codes in one place. Support: GTM, GA4 Global Tag, ADS Remarketing Global Tag, Google Merchant Customer Reviews for WooCommerce, Facebook.
 * Version:     1.9.3
 * Requires at least: 5.2.0
 * Tested up to: 6.6.2
 * Requires PHP:      7.2
 * Author:      Array.codes
 * Author URI:  https://array.codes
 * Developer: Heitor Sousa
 * Developer URI: https://array.codes/
 * Domain Path: /languages
 * Text Domain: wp-tracking-codes
 *
 *  WC requires at least: 4.8.0
 *  WC tested up to: 9.9.3
 *
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
if(!defined('ABSPATH')){
	exit;
}

if(!class_exists('Wp_Tracking_Codes')):
	class Wp_Tracking_Codes {
		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '1.9.3';
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;
        /**
         * Options
         *
         * @var array $options Options
         */
        private $options = array();
		/**
		 * Initialize the plugin public actions.
		 */
		private function __construct() {
			// Load the instalation hook
			register_activation_hook( __FILE__, 'wp_tracking_codes_install' );
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			//Load includes
			$this->includes();
			//Load Tracking Analytics
			add_action( 'wp_head',  array( $this, 'hook_analytics_4' ) );
			//Load Tracking Analytics Remarketing
			add_action( 'wp_footer',  array( $this, 'hook_analytics_remarketing' ) );
			//Load Tracking Facebook ID
			add_action( 'wp_head',  array( $this, 'hook_facebook_pixel_code' ) );
			//Load Tracking Google Tag Manager in Head and after Body
			add_action('wp_head', array($this, 'hook_google_tag_manager_head'));
			add_filter('template_include', array( $this, 'custom_include' ),0 );
			add_filter('wp_body_open', array( $this, 'hook_google_tag_manager_body' ),0);
            add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility_hpos') );
		}
		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return plugin_dir_path( __FILE__ ) . 'templates/';
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-tracking-codes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Instalation Plugin
		 */
		public function wp_tracking_codes_install() {
			// Trigger our function that registers the custom post type
			pluginprefix_setup_post_types();
			// Clear the permalinks after the post type has been registered
			flush_rewrite_rules();
		}
		/**
		 * Includes
		 */
		private function includes() {
			include_once 'includes/class-register-codes.php';
			include_once 'includes/class-render-data-layer-gtm.php';
			include_once 'includes/class-render-google-merchant.php';
		}
		/**
		 * Debug
		 */
		public function log_me($message) {
			if (WP_DEBUG === true) {
				if (is_array($message) || is_object($message)) {
					error_log(print_r($message, true));
				} else {
					error_log($message);
				}
			}
		}

		public function hook_analytics_4(){
			$this->options = get_option( 'tracking_option' );
			if( isset( $this->options['analytics_4'] ) && !empty( $this->options['analytics_4'] ) ){
				$analytics_4 = $this->options['analytics_4'];
				printf("
			            <!-- Global site tag (gtag.js) - Google Analytics -->
						<script async src='https://www.googletagmanager.com/gtag/js?id=%s'></script>
						<script>
						 window.dataLayer = window.dataLayer || [];
						  function gtag(){dataLayer.push(arguments);}
						  gtag('js', new Date());
						
						  gtag('config', '%s');
						</script>
	            ", esc_attr($analytics_4), esc_attr($analytics_4));
			}

		}

		public function hook_analytics_remarketing() {
			$this->options = get_option( 'tracking_option' );
            if( isset( $this->options['analytics_remarketing'] ) && !empty( $this->options['analytics_remarketing'] ) ){
                $analytics_remarketing = $this->options['analytics_remarketing'];
                printf("
			           <!-- Global site tag (gtag.js) - Google Ads -->
						<script async src='https://www.googletagmanager.com/gtag/js?id=%s'></script>
						<script>
						 window.dataLayer = window.dataLayer || [];
						  function gtag(){dataLayer.push(arguments);}
						  gtag('js', new Date());
						
						  gtag('config', '%s');
						</script>
	            ", esc_attr($analytics_remarketing), esc_attr($analytics_remarketing));
            }
		}

		public function hook_facebook_pixel_code() {
			$this->options = get_option( 'tracking_option' );
			if ( isset( $this->options['facebook_pixel_code'] ) && ! empty( $this->options['facebook_pixel_code'] ) ){
				$facebook_pixel_code = $this->options['facebook_pixel_code'];
					printf( "
						            <!-- Facebook Pixel Code -->
						            <script>
						            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
						            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
						            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
						            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
						            document,'script','https://connect.facebook.net/en_US/fbevents.js');
						
						            fbq('init', '%s');
						            fbq('track', 'PageView');</script>
						            <noscript><img height='1' width='1' style='display:none'
						            src='https://www.facebook.com/tr?id=1556124141356092&ev=PageView&noscript=1'
						            /></noscript>
						            <!-- End Facebook Pixel Code -->
            		", esc_attr($facebook_pixel_code) );
			}
		}

		public function custom_include($template) {
			ob_start();
			return $template;
		}

		public function hook_google_tag_manager_head(){
			$this->options = get_option('tracking_option');
			if ( isset( $this->options['google_tag_manager'] ) && !empty($this->options['google_tag_manager'])){
				$google_tag_manager = $this->options['google_tag_manager'];
				printf(
					"<!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','%s');</script>
                <!-- End Google Tag Manager -->",
					esc_attr( $google_tag_manager )
				);
			}

		}

		public function hook_google_tag_manager_body($classes){
			$this->options = get_option( 'tracking_option' );
			if( isset( $this->options['google_tag_manager'] ) && !empty( $this->options['google_tag_manager'] ) ){
				$google_tag_manager = $this->options['google_tag_manager'];
				printf("<!-- Google Tag Manager (noscript) -->
<noscript><iframe src='https://www.googletagmanager.com/ns.html?id=%s' height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->",
					esc_attr( $google_tag_manager )
				);
			}
		}

        /**
         * Declare_compatibility_hpos
         */
        public function declare_compatibility_hpos() {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'wp-tracking-codes/wp-tracking-codes.php', true );
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', 'wp-tracking-codes/wp-tracking-codes.php', true );
            }
        }
	}
	add_action( 'plugins_loaded', array( 'Wp_Tracking_Codes', 'get_instance' ) );
endif;