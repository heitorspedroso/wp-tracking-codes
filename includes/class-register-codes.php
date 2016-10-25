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

class RegisterCodes extends Wp_Tracking_Codes{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
    /**
     * Add options page
     */
    public function add_submenu_page()
    {
        add_submenu_page(
          'options-general.php',          // admin page slug
          __( 'WP Tracking Codes', 'wptc' ), // page title
          __( 'WP Tracking Codes', 'wptc' ), // menu title
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
       	// Add the field with the names and function to use for our new
       	// settings, put it in our new section
       	add_settings_field(
      		'tracking_analytics',
      		'Google Analytics UA',
      		array( $this, 'tracking_analytics_callback_function' ),
      		'wp-tracking-codes',
      		'tracking_section'
      	);
        add_settings_field(
      		'tracking_analytics_remarketing',
      		'Google Remarketing Conversion ID',
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
        add_settings_field(
      		'tracking_google_tag_manager',
      		'Google Tag Manager ID',
      		array( $this, 'tracking_google_tag_manager_callback_function' ),
      		'wp-tracking-codes',
      		'tracking_section'
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
        if( isset( $input['analytics_remarketing'] ) )
            $new_input['analytics_remarketing'] = sanitize_text_field( $input['analytics_remarketing'] );
        if( isset( $input['facebook_pixel_code'] ) )
            $new_input['facebook_pixel_code'] = sanitize_text_field( $input['facebook_pixel_code'] );
        if( isset( $input['google_tag_manager'] ) )
            $new_input['google_tag_manager'] = sanitize_text_field( $input['google_tag_manager'] );
        return $new_input;
    }
    /**
     * Print the Section text
     */
    public function tracking_section_callback_function()
    {
        //print 'Enter your settings below:';
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function tracking_analytics_callback_function()
    {
      printf(
            '<input type="text" id="analytics" name="tracking_option[analytics]"/ value="%s">
             <p class="description">Example: UA-XXXXXXXX-X - <a href="https://support.google.com/analytics/answer/1032385" target="_blank">Help me</a></p>',
            isset( $this->options['analytics'] ) ? esc_attr( $this->options['analytics']) : ''
        );
    }
    public function tracking_analytics_remarketing_callback_function()
    {
      printf(
            '<input type="text" id="analytics_remarketing" name="tracking_option[analytics_remarketing]"/ value="%s">
             <p class="description">Example: 123456789 - <a href="https://support.google.com/tagmanager/answer/6105160?hl=en&ref_topic=6334091" target="_blank">Help me</a></p>',
            isset( $this->options['analytics_remarketing'] ) ? esc_attr( $this->options['analytics_remarketing']) : ''
        );
    }
    public function tracking_facebook_pixel_code_callback_function()
    {
      printf(
            '<input type="text" id="facebook_pixel_code" name="tracking_option[facebook_pixel_code]"/ value="%s">
             <p class="description">Example: 1234567890 - <a href="https://www.facebook.com/business/help/742478679120153/?ref=u2u" target="_blank">Help me</a></p>',
            isset( $this->options['facebook_pixel_code'] ) ? esc_attr( $this->options['facebook_pixel_code']) : ''
        );
    }
    public function tracking_google_tag_manager_callback_function()
    {
      printf(
            '<input type="text" id="google_tag_manager" name="tracking_option[google_tag_manager]"/ value="%s">
             <p class="description">Example: GTM-XXXXXX - <a href="https://support.google.com/tagmanager/answer/6103696" target="_blanl">Help me</a></p>',
            isset( $this->options['google_tag_manager'] ) ? esc_attr( $this->options['google_tag_manager']) : ''
        );
    }
}

if( is_admin() )
    $RegisterCodes = new RegisterCodes();
