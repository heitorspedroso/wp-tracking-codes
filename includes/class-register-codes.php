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

if ( ! class_exists( 'RegisterCodes' ) ) :
	class RegisterCodes{
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
			add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
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

		/**
		 * Add options page
		 */
		public function add_submenu_page()
		{
			add_submenu_page(
				'options-general.php',          // admin page slug
				__( 'WP Tracking Codes', 'wp-tracking-codes' ), // page title
				__( 'WP Tracking Codes', 'wp-tracking-codes' ), // menu title
				'manage_options',               // capability required to see the page
				'wp-tracking-codes',                // admin page slug, e.g. options-general.php?page=wporg_options
				array( $this, 'create_admin_page' )            // callback function to display the options page
			);
		}

		/**
		 * Options page callback
		 */
		public function create_admin_page()
		{
			$this->options = get_option( 'tracking_option' );
			include 'admin/views/html-admin-page.php';
		}

		/**
		 * Register and add settings
		 */
		public function page_init()
		{
			// Add the section to reading settings so we can add our
			// fields to it
			add_settings_section(
				'tracking_section',
				'',
				array( $this, 'tracking_section_callback_function' ),
				'wp-tracking-codes'
			);
			add_settings_section(
				'google_merchant_section',
				'',
				array( $this, 'google_merchant_section_callback_function' ),
				'wp-tracking-codes'
			);
			// Add the field with the names and function to use for our new
			// settings, put it in our new section
            add_settings_field(
                'tracking_google_tag_manager',
                'Google Tag Manager ID',
                array( $this, 'tracking_google_tag_manager_callback_function' ),
                'wp-tracking-codes',
                'tracking_section'
            );
			add_settings_field(
				'tracking_analytics_4',
				'Google Analytics 4 (global tag)',
				array( $this, 'tracking_analytics4_callback_function' ),
				'wp-tracking-codes',
				'tracking_section'
			);
			add_settings_field(
				'tracking_analytics_remarketing',
				'Google ADS (global tag)',
				array( $this, 'tracking_analytics_remarketing_callback_function' ),
				'wp-tracking-codes',
				'tracking_section'
			);
			add_settings_field(
				'tracking_facebook_pixel_code',
				'Facebook Pixel ID',
				array( $this, 'tracking_facebook_pixel_code_callback_function' ),
				'wp-tracking-codes',
				'tracking_section'
			);
			// settings checkbox
			add_settings_field(
				'tracking_google_merchant',
				'Google Merchant ID - Customer Reviews',
				array( $this, 'tracking_google_merchant_callback_function' ),
				'wp-tracking-codes',
				'google_merchant_section'
			);
			add_settings_field(
				'tracking_google_merchant_estimative_delivery_days',
				'Google Merchant Estimative Delivery Days - Customer Reviews',
				array( $this, 'tracking_google_merchant_estimative_delivery_days_callback_function' ),
				'wp-tracking-codes',
				'google_merchant_section'
			);
            add_settings_field(
                'data_layer_google_tag_manager',
                'DataLayer Google Tag Manager',
                array( $this, 'data_layer_google_tag_manager_callback_function' ),
                'wp-tracking-codes',
                'google_merchant_section'
            );


			// Register our setting so that $_POST handling is done for us and
			// our callback function just has to echo the <input>
			register_setting(
				'wp-tracking-codes', // Option group
				'tracking_option', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function sanitize( $input )
		{
			$new_input = array();
			if( isset( $input['analytics'] ) )
				$new_input['analytics'] = sanitize_text_field( $input['analytics'] );
			if( isset( $input['analytics_4'] ) )
				$new_input['analytics_4'] = sanitize_text_field( $input['analytics_4'] );
			if( isset( $input['analytics_remarketing'] ) )
				$new_input['analytics_remarketing'] = sanitize_text_field( $input['analytics_remarketing'] );
			if( isset( $input['facebook_pixel_code'] ) )
				$new_input['facebook_pixel_code'] = sanitize_text_field( $input['facebook_pixel_code'] );
			if( isset( $input['google_tag_manager'] ) )
				$new_input['google_tag_manager'] = sanitize_text_field( $input['google_tag_manager'] );
			if( isset( $input['google_merchant'] ) )
				$new_input['google_merchant'] = sanitize_text_field( $input['google_merchant'] );
			if( isset( $input['google_merchant_estimative_delivery_days'] ) )
				$new_input['google_merchant_estimative_delivery_days'] = sanitize_text_field( $input['google_merchant_estimative_delivery_days'] );
			if( isset( $input['data_layer_google_tag_manager'] ) )
				$new_input['data_layer_google_tag_manager'] = sanitize_text_field( $input['data_layer_google_tag_manager'] );
			return $new_input;
		}

		/**
		 * Print the Section text
		 */
		public function tracking_section_callback_function()
		{
//        print 'For all:';
		}

		/**
		 * Print the Section text
		 */
		public function google_merchant_section_callback_function()
		{
			print '<div class="title-section-">'.esc_html_e( 'Only WooCommerce:', 'wp-tracking-codes' ).'</div>';

		}

		public function tracking_analytics4_callback_function()
		{
			printf(
				'<input type="text" id="analytics_4" name="tracking_option[analytics_4]"/ value="%s">',
				isset( $this->options['analytics_4'] ) ? esc_attr( $this->options['analytics_4']) : ''
			);
			printf(
				'<div class="status" title="Running"><span class="dashicons %s"></span></span></div>',
				isset( $this->options['analytics_4'] ) && !empty($this->options['analytics_4']) ? 'dashicons-yes' : ''
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'Example: G-XXXXXXXXXX - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://developers.google.com/tag-platform/devguides/install-gtagjs#google-analytics',
					esc_html__( 'Help me', 'wp-tracking-codes' )
				)
			);
			printf(
				'</p>'
			);
		}

		public function tracking_analytics_remarketing_callback_function()
		{
			printf(
				'<input type="text" id="analytics_remarketing" name="tracking_option[analytics_remarketing]"/ value="%s">',
				isset( $this->options['analytics_remarketing'] ) ? esc_attr( $this->options['analytics_remarketing']) : ''
			);
			printf(
				'<div class="status" title="Running"><span class="dashicons %s"></span></span></div>',
				isset( $this->options['analytics_remarketing'] ) && !empty($this->options['analytics_remarketing']) ? 'dashicons-yes' : ''
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'Example: AW-YYYYYY - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://developers.google.com/tag-platform/devguides/install-gtagjs#google-ads',
					esc_html__( 'Help me', 'wp-tracking-codes' )
				)
			);
			printf(
				'</p>'
			);
		}

		public function tracking_facebook_pixel_code_callback_function()
		{
			printf(
				'<input type="text" id="facebook_pixel_code" name="tracking_option[facebook_pixel_code]" value="%s">',
				isset( $this->options['facebook_pixel_code'] ) ? esc_attr( $this->options['facebook_pixel_code']) : ''
			);
			printf(
				'<div class="status" title="Running"><span class="dashicons %s"></span></span></div>',
				isset( $this->options['facebook_pixel_code'] ) && !empty($this->options['facebook_pixel_code']) ? 'dashicons-yes' : ''
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'Example: 1234567890 - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://www.facebook.com/business/help/742478679120153/',
					esc_html__( 'Help me', 'wp-tracking-codes' )
				)
			);
			printf(
				'</p>'
			);
		}

		public function tracking_google_tag_manager_callback_function()
		{
			printf(
				'<input type="text" id="google_tag_manager" name="tracking_option[google_tag_manager]" value="%s">',
				isset( $this->options['google_tag_manager'] ) ? esc_attr( $this->options['google_tag_manager']) : ''
			);
			printf(
				'<div class="status" title="Running"><span class="dashicons %s"></span></span></div>',
				isset( $this->options['google_tag_manager'] ) && !empty($this->options['google_tag_manager']) ? 'dashicons-yes' : ''
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'Example: GTM-XXXXXX - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://support.google.com/tagmanager/answer/6103696',
					esc_html__( 'Help me', 'wp-tracking-codes' )
				)
			);
			printf(
				'</p>'
			);
		}

		public function tracking_google_merchant_callback_function()
		{
			printf(
				'<input type="text" id="google_merchant" name="tracking_option[google_merchant]" value="%s">',
				isset( $this->options['google_merchant'] ) ? esc_attr( $this->options['google_merchant']) : ''
			);
			printf(
				'<div class="status" title="Running"><span class="dashicons %s"></span></span></div>',
				isset( $this->options['google_merchant'] ) && !empty($this->options['google_merchant']) && isset( $this->options['google_merchant_estimative_delivery_days'] ) && !empty($this->options['google_merchant_estimative_delivery_days']) ? 'dashicons-yes' : ''
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'Example: 123456789 - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a><br/>',
					'https://support.google.com/paymentscenter/answer/7163092?hl=en',
					esc_html__( 'Help me', 'wp-tracking-codes' )
				)
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'This function activates %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://support.google.com/merchants/answer/190657?hl=en',
					esc_html__( 'Seller ratings', 'wp-tracking-codes' )
				)
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( ' on the purchase page - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://support.google.com/merchants/answer/7124018?hl=en&ref_topic=7105048',
					esc_html__( 'Help me', 'wp-tracking-codes' )
				)
			);
			printf(
				'</p>'
			);
		}

		public function tracking_google_merchant_estimative_delivery_days_callback_function()
		{
			printf(
				'<input type="text" id="google_merchant_estimative_delivery_days" name="tracking_option[google_merchant_estimative_delivery_days]" value="%s">',
				isset( $this->options['google_merchant_estimative_delivery_days'] ) ? esc_attr( $this->options['google_merchant_estimative_delivery_days']) : ''
			);
			printf(
				esc_html__(' days','wp-tracking-codes')
			);
			printf(
				'<div class="status" title="Running"><span class="dashicons %s"></span></span></div>',
				isset( $this->options['google_merchant'] ) && !empty($this->options['google_merchant']) && isset( $this->options['google_merchant_estimative_delivery_days'] ) && !empty($this->options['google_merchant_estimative_delivery_days']) ? 'dashicons-yes' : ''
			);
			printf(
				'<p class="description">'.esc_html__( 'Example: 9 - The estimated average number of delivery days for all orders', 'wp-tracking-codes' ).'</p>'
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'This number added to the order day will be the date that Google will send the survey to the user email, if user allows - %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://support.google.com/merchants/answer/7106244?hl=en&ref_topic=7105160&visit_id=637233659345484301-2826177191&rd=1',
					esc_html__( 'How it works?', 'wp-tracking-codes' )
				)
			);
			printf(
				'</p>'
			);
		}

		public function data_layer_google_tag_manager_callback_function($args)
		{
			$options = get_option('tracking_option');
			$checked = ( isset($options['data_layer_google_tag_manager']) && $options['data_layer_google_tag_manager'] == 1) ? 1 : 0;

			printf(
				'<input type="checkbox" id="data_layer_google_tag_manager" name="tracking_option[data_layer_google_tag_manager]" value="1"' . checked( 1, $checked, false ) . '/>',
				isset( $this->options['data_layer_google_tag_manager'] ) ? esc_attr( $this->options['data_layer_google_tag_manager']) : ''
			);
			printf(
				'<p class="description">'
			);
			printf(
			/* translators: %1$s: search term */
				esc_html__( 'Activate to use datalayer with %1$s', 'wp-tracking-codes' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://support.google.com/tagmanager/answer/6107169?hl=en#standard-ecommerce',
					esc_html__( 'Standard Ecommerce (UA)', 'wp-tracking-codes' )
				)
			);
			printf(
				' - <a href="%s" target="_blank">%s</a>',
				'https://support.google.com/tagmanager/answer/6164391?hl=en',
				esc_html__( 'Help me', 'wp-tracking-codes' )
			);
			printf(
				'</p>'
			);

		}
	}
endif;

if( is_admin() ){
	RegisterCodes::get_instance();
}
