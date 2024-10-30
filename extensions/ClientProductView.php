<?php

namespace mediamodifier\components;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;
use mediamodifier\models\Order;

class ClientProductView
{
    public function __construct()
    {


        add_action('woocommerce_before_add_to_cart_button', array($this, 'add_to_cart_btn'));

        if (!Mediamodifier_PoD_For_WooCommerce::hasOrderButton()) {
            add_action('woocommerce_single_product_summary', array($this, 'replace_order_button_html'), 31);
        }

    }

    public function add_to_cart_btn()
    {
        $postId = get_the_ID();

        $tmplId = get_post_meta($postId, 'pod_template_id', true);

        if ($tmplId !== false && !empty($tmplId)) {

            $html = "<input type='hidden' 
                        name='" . esc_attr('MMPod[' . Order::ORDER_SOURCE_FILE . '][]') . "'   
                        id='" . esc_attr(Order::ORDER_SOURCE_FILE) . "' 
                        class='mm_pod' autocomplete='false'/>";
            $html .= "<input type='hidden' 
                        name='" . esc_attr('MMPod[' . Order::ORDER_THUMB . '][]') . "'   
                        id='" . esc_attr(Order::ORDER_THUMB) . "' 
                        class='mm_pod' autocomplete='false'/>";
            $html .= "<input type='hidden' 
                        name='" . esc_attr('MMPod[' . Order::ORDER_POD_ID . ']') . "'   
                        id='" . esc_attr(Order::ORDER_POD_ID) . "' 
                        class='mm_pod' autocomplete='false'/>";
            $html .= "<a href='#' onclick=\"" . esc_js('customize_mediamodifier_design(' . $postId . ', "' . $tmplId . '")') . "\" 
                       class=\"button product_type_simple\">" . esc_html__("Add Custom Design",
                    'mediamodifier-pod-for-woocommerce') . "</a>";


            echo wp_kses($html, [
                'input' => [
                    'type' => [],
                    'name' => [],
                    'id' => [],
                    'class' => []
                ],
                'a' => [
                    'href' => [],
                    'onclick'=>[],
                    'title' => [],
                    'class' => []
                ]
            ]);
        }
    }

    // Utility function to disable add to cart when volume exceeds 68m3
    function get_total_volume()
    {
        $total_volume = 0;

        // Loop through cart items and calculate total volume
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_volume = (float)get_post_meta($cart_item['product_id'], '_item_volume', true);
            $total_volume += $product_volume * $cart_item['quantity'];
        }
        return $total_volume;
    }

    /* hide add to cart button*/
    function replace_order_button_html($order_button)
    {
        ?>
        <style>.single_add_to_cart_button {
                display: none
            }</style>
        <?php
    }


}