<?php
namespace mediamodifier\services;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

class PoDMediaModifierREST
{
    public function __construct(){
        add_action('rest_api_init', array($this, 'init_rests'));
    }

    public function init_rests(){
        $this->add_update_product_rest();
        $this->add_set_template_id_rest();
        $this->add_create_template_rest();
        $this->add_delete_template_rest();
        $this->add_order_create_rest();
        $this->add_web_hook_rest();
    }

    private function add_update_product_rest(){

        register_rest_route(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') , '/set', array(
            'methods' => 'POST',
            'callback' =>   [$this,'update_post_callback'],
            'permission_callback' => function () {
                //allow to its own server domain and own requests
                return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' );
            }
        ));

    }
    
    private function add_set_template_id_rest(){

        register_rest_route(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') , '/settemplate', array(
            'methods' => 'POST',
            'callback' =>   [$this,'update_post_callback'],
            'permission_callback' => function () {
                //allow to its own server domain and own requests
                return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' );
            }
        ));

    }

    private function add_delete_template_rest(){

        register_rest_route(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') , '/unlink', array(
            'methods' => 'POST',
            'callback' =>   [$this,'delete_template_callback'],
            'permission_callback' => function () {
                //allow to its own server domain and own requests
                return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' );
            }
        ));

    }
    
    public function update_post_callback($request){
        $params = $request->get_json_params();

        $meta = get_post_meta($params['id'], 'pod_template_id');

        if(!$meta) {
            return add_post_meta($params['id'], 'pod_template_id', $params['template_id'], false);
        }

        return update_post_meta( $params['id'], 'pod_template_id', $params['template_id']);
    }

    public function delete_template_callback($request){
        $params = $request->get_json_params();

        $meta = get_post_meta($params['id'], 'pod_template_id');

        if($meta) {
            return delete_post_meta($params['id'], 'pod_template_id');
        }
        
        return true;

    }

    private function add_create_template_rest(){

        register_rest_route(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') , '/add-template', array(
            'methods' => 'POST',
            'callback' =>   [$this,'add_template_callback'],
            'permission_callback' => function () {
                //allow to its own server domain and own requests
                return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' );
            }
        ));

    }

    private function add_order_create_rest(){

        register_rest_route(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') , '/add-order', array(
            'methods' => 'POST',
            'callback' =>   [$this,'add_order_callback'],
            'permission_callback' => function () {
                //allow to its own server domain and own requests
                return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' );
            }
        ));

    }

    public function add_order_callback($request){

        $params = $request->get_json_params();

        $service = Mediamodifier_PoD_For_WooCommerce::podApi();

        $tmpl = get_post_meta($params['id'], Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), true);

        if($tmpl !== null && !empty($tmpl)){
            return;
        }

        $post = get_post($params['id']);

        if ($post->post_type=='product' ){

            $post_ID = $post->ID;

            $sku = get_post_meta($post_ID, '_sku', true);

            $p_name = $post->post_title;

            $p_cat = '';

            $terms = get_the_terms( $post_ID, 'product_cat');

            $p_link = get_permalink($post_ID);

            return true;

        }

    }

    public function add_template_callback($request){

        $params = $request->get_json_params();

        $service = Mediamodifier_PoD_For_WooCommerce::podApi();



        $tmpl = get_post_meta($params['id'], Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), true);

        if($tmpl !== null && !empty($tmpl)){
            return;
        }

        $post = get_post($params['id']);

        if ($post->post_type=='product' ){

            $post_ID = $post->ID;

            $sku = get_post_meta($post_ID, '_sku', true);

            $p_name = $post->post_title;

            $p_cat = '';

            $terms = get_the_terms( $post_ID, 'product_cat');

            $p_link = get_permalink($post_ID);

            foreach ($terms as $term) {
                $p_cat = $term->name;
                break;
            }

            $response = $service->createTemplate(['meta'=>['product'=>['sku'=>$sku, 'name'=>$p_name, 'category'=>$p_cat, 'url'=>$p_link]]]);

            $template_id = $response['_id'];

            add_post_meta($params['id'], 'pod_template_id', $template_id, false);

            return ['template_id'=>$template_id];

        }

    }

    public function update_customer_order_thumbs($request){

        if ( isset(WC()->session) && ! WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie( true );
        }

        $params = $request->get_json_params();

        $service = Mediamodifier_PoD_For_WooCommerce::podApi();

        $tmpl = get_post_meta($params['id'], Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), true);

        if($tmpl !== null && !empty($tmpl)){
            return;
        }

        $post = get_post($params['id']);

        $thumb_data = '';

        $product_id = '';

        // Set the session data
        WC()->session->set( 'pod_data', array( 'product_id' => $product_id, 'pod_thumb' => $thumb_data ) );

    }


    /* accepting web hook data */
    public function add_web_hook_rest(){


        register_rest_route(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') , '/webhook', array(
            'methods' => 'GET',
            'callback' =>   [$this,'webhook_callback'],
            'permission_callback' => function () {
                //allow to its own server domain and own requests
                return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' );
            }
        ));
    }

    public function webhook_callback($request)
    {

        if ( defined( 'WC_ABSPATH' ) ) {
            // WC 3.6+ - Cart and other frontend functions are not included for REST requests.

            include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
            include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
            include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
        }
        if ( null === WC()->session ) {
            $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
            WC()->session = new $session_class();
            WC()->session->init();
        }


        if (isset(WC()->session)) {
            if (!WC()->session->has_session()) {
                WC()->session->set_customer_session_cookie(true);
            }
        }


        if ( null === WC()->cart ) {
            WC()->cart = new \WC_Cart();

            // We need to force a refresh of the cart contents from session here (cart contents are normally refreshed on wp_loaded, which has already happened by this point).
            WC()->cart->get_cart();
        }
        $session_data_array = WC()->session->get_session_data();
        ## -------------- Get the cleaned unserialized data ------------- ##

        $session_cart = WC()->session->get('cart');
        $session_cart_totals = WC()->session->get('cart_totals');
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();



    }
}