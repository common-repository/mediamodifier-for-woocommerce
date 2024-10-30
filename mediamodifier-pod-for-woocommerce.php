<?php
namespace mediamodifier;

require_once 'vendor/autoload.php';

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

use mediamodifier\components\ClientOrderReceived;

use mediamodifier\events\EventListener;
use mediamodifier\models\PodTemplate;
use mediamodifier\models\Order;
use mediamodifier\services\PoDApiREST;
use mediamodifier\services\PoDMediaModifierREST;
use mediamodifier\components\PodMenu;
use mediamodifier\components\ClientProductView;
use mediamodifier\components\AdminOrderThumbImage;
use mediamodifier\components\ClientCartImage;
use mediamodifier\extentsions\AdminProductTable;
use mediamodifier\assets\Assets;
/**
 * Plugin Name: Mediamodifier for WooCommerce
 * Description: Extends WooCommerce with Mediamodifier PoD.
 * Version: 1.0
 * Author: Mediamodifier
 * Author URI: https://mediamodifier.com
 * License: GPLv2 or later
 * Text Domain: mediamodifier-pod-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 2.6
 * WC tested up to: 5.4.2
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main file constant
 */


/**
 * @class    Mediamodifier_PoD_For_WooCommerce
 * @category Plugin
 * @package  Mediamodifier_PoD_For_WooCommerce
 */
class Mediamodifier_PoD_For_WooCommerce
{

    private static $configData = [];

    public static function config($attr)
    {
        if (count(static::$configData) == 0) {
            static::$configData = include_once(__DIR__ . '/config/main.php');
        }

        if(isset(static::$configData[$attr])){
            return static::$configData[$attr];
        };
        return null;
    }

    /**
     * Class constructor
     */
    function __construct()
    {

        // Get the current WC_Session_Handler obect
        if($this->validateWCExistance()) {
            add_action('plugins_loaded', array($this, 'plugins_loaded'));
            //do cleanup on plugin unload
            register_deactivation_hook( __FILE__, array($this, 'pluginCleanup') );

        }
    }

    /*
     * @description:  validate Woocommerce existance and unload on false
     * */
    private function validateWCExistance(){

        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $this->plugin_unload();
            return false;
        }

        return true;
    }

    public static function getCustomerApiKey(){
        return get_option(static::config('API_KEY_EDITOR'), null);
    }


    public static function getAdminApiKey(){
        return get_option(static::config('API_KEY_ADMIN'), null);
    }

    public static function hasOrderButton(){
        $result = get_option(static::config('REPLACEMENT_CONFIG'), "");
        return $result == "" || empty($result) ? true : false;
    }
    /**
     * Initialize plugin
     * @return void
     */
    function my_product_carousel_options($options) {

        $options['slideshow'] = true;
        return $options;
    }
    public function plugins_loaded()
    {
        // Check if payment gateways are available
        $this->load_translations();


        add_filter("woocommerce_single_product_carousel_options", array($this, "my_product_carousel_options"), 10);
        if(is_admin()) {
            new AdminProductTable();
            new AdminOrderThumbImage();
            new PodMenu();
        }
        new Assets();
        new PodTemplate();
        new PoDMediaModifierREST();
        new ClientProductView();
        new ClientCartImage();
        new ClientOrderReceived();
        new EventListener();
    }

    public function plugin_unload()
    {
        global $wpdb;

        if (is_admin() && get_option(static::config('API_KEY_ADMIN')) !== null) {

            $this->pluginCleanup();

            deactivate_plugins([__FILE__]);

            add_action( 'admin_notices', function(){
                ?>
                <div class="error notice">
                    <p><?php echo esc_html__("WooComerce plugin must be activated to proceed with Mediamodifier Templates PoD activation", "mediamodifer_pod_for_woocommerce");?></p>
                </div>
                <?php
            } );

        }
    }
    private function pluginCleanup(){
        global $wpdb;

        delete_option(static::config('API_KEY_ADMIN'));
        delete_option(static::config('API_KEY_EDITOR'));

        $results= $wpdb->get_results("select * from {$wpdb->prefix}term_taxonomy where taxonomy LIKE  '" . PodTemplate::TAXENOMY. "'", ARRAY_A );

        foreach ($results as $row){

            $wpdb->query("delete from {$wpdb->prefix}terms where term_id =  '".$row['term_id']."'") ;

            $wpdb->query("delete from {$wpdb->prefix}term_relationships where term_taxonomy_id = '".$row['term_taxonomy_id']."'");
        }

        $wpdb->query("delete from {$wpdb->prefix}posts where post_type =  '". PodTemplate::TAXENOMY ."'") ;

        $wpdb->query("delete from {$wpdb->prefix}postmeta where meta_key in ('pod_template_id', '". Order::ORDER_THUMB ."', '". Order::ORDER_POD_ID ."','". Order::ORDER_SOURCE_FILE ."')") ;

        $wpdb->query("delete from {$wpdb->prefix}term_taxonomy where taxonomy LIKE '" . PodTemplate::TAXENOMY ."'");


    }
    /**
     * Load translations
     *
     * Allows overriding the offical translation by placing
     * the translation files in wp-content/languages/mediamodifier-for-woocommerce
     *
     * @return void
     */
    function load_translations()
    {
        $domain = 'wc-gateway-mediamodifier';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/mediamodifier-for-woocommerce/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, dirname(plugin_basename(static::config('GATEWAYS_MAIN_FILE'))) . '/languages/');
    }





    public static function getUploadDirectory(){

        $upload_conf = wp_get_upload_dir();
        $plugin_upload = $upload_conf['basedir'] . "/" .Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_UPLOAD_NAME');

        if(!file_exists($plugin_upload)){
            mkdir($plugin_upload);
        }

        return  $plugin_upload ;
    }

    public static function getUploadUrl(){

        $upload_conf = wp_get_upload_dir();
        $plugin_upload_url = $upload_conf['baseurl'] . "/" .Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_UPLOAD_NAME');

        return  $plugin_upload_url ;
    }

    public static function podApi(){
        return new PoDApiREST();
    }
}

new Mediamodifier_PoD_For_WooCommerce();

