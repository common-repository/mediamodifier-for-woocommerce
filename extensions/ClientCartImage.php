<?php

namespace mediamodifier\components;

use mediamodifier\models\Order;

class ClientCartImage
{
    public function __construct()
    {
        add_filter('woocommerce_cart_item_thumbnail', array($this, 'custom_product_image'), 10, 3);
        add_filter('woocommerce_cart_item_permalink', array($this, 'change_product_url'), 10, 3);
    }

    function custom_product_image($_product_img, $cart_item, $cart_item_key)
    {
        if (isset($cart_item[Order::ORDER_THUMB]) && !empty($cart_item[Order::ORDER_THUMB])) {
            $gallery = "";
            $count = 0;
            $uid = uniqid();
            $default_thumbnail = get_the_post_thumbnail_url($cart_item["product_id"]);

            foreach($cart_item[Order::ORDER_THUMB] as $custom_thumbnail) {
                if(!empty($custom_thumbnail)) {
                    $gallery .= '<a href="' . esc_url($custom_thumbnail) . '" target="_blank" class="thickbox" rel="' . esc_attr('gal'.$uid) . '" style="' . (++$count > 1 ? 'display:none' : 'display:block') .'">
                                    <img src="'. esc_url( $custom_thumbnail) .'"/>
                                </a>';
                }
            }
            $gallery .= '<a href="' . esc_url($default_thumbnail) . '" target="_blank" class="thickbox" rel="' . esc_attr('gal'.$uid) . '" style="' . esc_attr(++$count > 1 ? 'display:none' : 'display:block') .'">
                            <img src="'. esc_url( $default_thumbnail) .'"/>
                        </a>';
            return $gallery;
        }

        return $_product_img;

    }

    function change_product_url($permalink, $cart_item, $cart_item_key)
    {
        if (isset($cart_item[Order::ORDER_THUMB]) && !empty($cart_item[Order::ORDER_THUMB])) {
            return '';
        }
        return $permalink;
    }


}