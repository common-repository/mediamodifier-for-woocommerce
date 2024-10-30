<?php

namespace mediamodifier\assets;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;
use mediamodifier\models\Order;

class Assets
{
    public function __construct(){
        // adding modal block to footer of front
        add_action('wp_footer', array($this, 'add_modal_block'));

        add_action('wp_footer', array($this, 'add_customer_config'));

        add_action('admin_footer', array($this, 'add_admin_config'));
        // adding modal block to footer of admin
        add_action('admin_footer', array($this, 'add_modal_block'));


        //register bundles for frontpage
        add_action('wp_enqueue_scripts', array($this, 'register_bundles'));
        //register bundles for admin
        add_action('admin_enqueue_scripts', array($this, 'register_admin_bundles'));
    }
    /* register editor external scripts for front */
    public function register_bundles(){
        add_thickbox();
        $name = Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME');
        wp_enqueue_style($name .'_externalcss', Mediamodifier_PoD_For_WooCommerce::config('CSS_BUNDLE'));
        wp_enqueue_script( $name.'_externaljs', Mediamodifier_PoD_For_WooCommerce::config('JS_BUNDLE'));
        wp_enqueue_script($name . '_scripts', plugin_dir_url(__FILE__) . 'script/main.js', array('jquery'));
    }

    /* register editor external scripts for backend */
    public function register_admin_bundles(){
        add_thickbox();

        $name = Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME');
        wp_enqueue_style($name.'_externalcss', Mediamodifier_PoD_For_WooCommerce::config('CSS_BUNDLE'));
        wp_enqueue_script( $name .'_externaljs', Mediamodifier_PoD_For_WooCommerce::config('JS_BUNDLE'));
        wp_enqueue_script($name . '_scripts', plugin_dir_url(__FILE__) . 'script/admin.js', array('jquery'));
        wp_enqueue_style($name . '_styles', plugin_dir_url(__FILE__) . 'style/main.css');
    }

    /**
     * Require functionality
     *
     * @return void
     */
    public function add_modal_block(){
        echo "<div id=\"" . esc_html__(Mediamodifier_PoD_For_WooCommerce::config('MODAL_CONTAINER_ID')) . "\"></div>";
    }

    /* js configuration */
    public function add_customer_config(){
        ?>
        <script type="text/javascript">

            if(window.mm == undefined){
                window.mm = {};
            }

            window.mm.config = {
                container: "<?php esc_html_e(Mediamodifier_PoD_For_WooCommerce::config('MODAL_CONTAINER_ID')) ?>",
                api: "<?php esc_html_e(Mediamodifier_PoD_For_WooCommerce::getCustomerApiKey()) ?>",
                domain: "<?php esc_html_e(Mediamodifier_PoD_For_WooCommerce::config('DOMAIN')) ?>",
                order_button:<?php echo Mediamodifier_PoD_For_WooCommerce::hasOrderButton() ? "true" : "false";?>,
                order_container: '<?php esc_html_e(Order::ORDER_THUMB);?>',
                msg : {
                    'add_pod_template': "<?php esc_html_e('Add PoD Template', 'mediamodifier-pod-for-woocommerce');?>"
                }
            }
        </script>
        <?php ;
    }

    /* js configuration */
    public function add_admin_config(){
        ?>
        <script type="text/javascript">
            if(window.mm == undefined){
                window.mm = {};
            }
            window.mm.config = {
                container: "<?php esc_html_e(Mediamodifier_PoD_For_WooCommerce::config('MODAL_CONTAINER_ID')); ?>",
                api: "<?php esc_html_e(Mediamodifier_PoD_For_WooCommerce::getAdminApiKey()); ?>",
                domain: "<?php esc_html_e(Mediamodifier_PoD_For_WooCommerce::config('DOMAIN')) ?>",


                msg : {
                    'edit_pod_template': "<?php esc_html_e('Edit PoD Template', 'mediamodifier-pod-for-woocommerce');?>",
                    'add_pod_template': "<?php esc_html_e('Add PoD Template', 'mediamodifier-pod-for-woocommerce');?>",
                    'set_template_prompt': "<?php esc_html_e('Set new PoD Template ID', 'mediamodifier-pod-for-woocommerce');?>",
                    'unlink_template_id': "<?php esc_html_e('Unlink PoD Template', 'mediamodifier-pod-for-woocommerce');?>",
                    'unlink_template_confirmation': "<?php esc_html_e('Please confirm PoD Template ID unlink action', 'mediamodifier-pod-for-woocommerce');?>"
                }
            }
        </script>
        <?php
    }


}