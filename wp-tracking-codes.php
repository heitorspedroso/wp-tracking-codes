<?php
/*
Plugin Name: Wp Tracking Codes
Plugin URI:  https://br.wordpress.org/plugins/wp-tracking-codes/
Description: Centralize os códigos de acompanhamento em apenas um lugar. Suporte: Google Analytics, Google Adwords Remarketing, Facebook Pixel Code
Version:     1.2.0
Author:      Heitor Pedroso
Author URI:  https://github.com/heitorspedroso
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: wp-tracking-codes
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
    const VERSION = '1.2.0';
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;
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
      add_action( 'wp_head',  array( $this, 'hook_analytics' ) );
      //Load Tracking Analytics Remarketing
      add_action( 'wp_footer',  array( $this, 'hook_analytics_remarketing' ) );
      //Load Tracking Facebook ID
      add_action( 'wp_head',  array( $this, 'hook_facebook_pixel_code' ) );
      //Load Tracking Google Tag Manager in Head and after Body
      add_action('wp_head', array($this, 'hook_google_tag_manager_head'));
      add_filter('template_include', array( $this, 'custom_include' ),0 );
      add_filter('shutdown', array( $this, 'hook_google_tag_manager_body' ),0);
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
    public function hook_analytics(){
        $this->options = get_option( 'tracking_option' );
        if( empty( $this->options['tracking_option']['analytics'] ) && !empty( $this->options['analytics'] ) )
        if($analytics = $this->options['analytics']):
            echo "
            <!-- Google Analytics Tag -->
            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

              ga('create', '$analytics', 'auto');
              ga('send', 'pageview');

            </script>
            <!-- Google Analytics Tag -->
            ";
        endif;
    }
    public function hook_analytics_remarketing(){
        $this->options = get_option( 'tracking_option' );
        if( empty( $this->options['tracking_option']['analytics_remarketing'] ) && !empty( $this->options['analytics_remarketing'] ) )
        if($analytics_remarketing = $this->options['analytics_remarketing']):
            echo '
            <!-- Google Remarketing Tag -->
            <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = '.$analytics_remarketing.';
            var google_custom_params = window.google_tag_params;
            var google_remarketing_only = true;
            /* ]]> */
            </script>
            <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
            <noscript>
              <div style="display:inline;">
                    <img height="1" width="1" style="border-style:none;" alt=""
                    src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/'.$analytics_remarketing.'/?value=0&amp;guid=ON&amp;script=0"/>
              </div>
            </noscript>
            <!-- Google Remarketing Tag -->
            ';
        endif;
    }
    public function hook_facebook_pixel_code(){
        $this->options = get_option( 'tracking_option' );
        if( empty( $this->options['tracking_option']['facebook_pixel_code'] ) && !empty( $this->options['facebook_pixel_code'] ) )
        if($facebook_pixel_code = $this->options['facebook_pixel_code']):
            echo "
            <!-- Facebook Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');

            fbq('init', '$facebook_pixel_code');
            fbq('track', 'PageView');</script>
            <noscript><img height='1' width='1' style='display:none'
            src='https://www.facebook.com/tr?id=1556124141356092&ev=PageView&noscript=1'
            /></noscript>
            <!-- End Facebook Pixel Code -->
            ";
        endif;
    }

    public function custom_include($template) {
            ob_start();
            return $template;
    }

    public function hook_google_tag_manager_head(){
      $this->options = get_option('tracking_option');
      if (empty($this->options['tracking_option']['google_tag_manager']) && !empty($this->options['google_tag_manager']))
        if ($google_tag_manager = $this->options['google_tag_manager']) :
          echo "
                <!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','$google_tag_manager');</script>
                <!-- End Google Tag Manager -->
                ";
        endif;
    }
    
    public function hook_google_tag_manager_body($classes){
        $this->options = get_option( 'tracking_option' );
        if( empty( $this->options['tracking_option']['google_tag_manager'] ) && !empty( $this->options['google_tag_manager'] ) )
        if($google_tag_manager = $this->options['google_tag_manager']):
            $content = ob_get_clean();
            $code_tag = "
              <!-- Google Tag Manager (noscript) -->
              <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=$google_tag_manager'
              height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
              <!-- End Google Tag Manager (noscript) -->
            ";
            $content = preg_replace('#<body([^>]*)>#i',"<body$1>{$code_tag}",$content);
            echo $content;
        endif;
    }
  }
add_action( 'plugins_loaded', array( 'Wp_Tracking_Codes', 'get_instance' ) );
endif;
//register_deactivation_hook ( __FILE__ , 'pluginprefix_function_to_run'  );
