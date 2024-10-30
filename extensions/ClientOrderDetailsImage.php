<?php

namespace mediamodifier\extentsions;

use mediamodifier\models\Order;

class ClientOrderDetailsImage
{
    public function __construct()
    {
        add_filter('woocommerce_cart_item_thumbnail', array($this, 'custom_product_image'), 10, 3);
        add_filter('woocommerce_cart_item_permalink', array($this, 'change_product_url'), 10, 3);
    }

    function custom_product_image($_product_img, $cart_item, $cart_item_key)
    {
        $thumb = get_the_post_thumbnail_url($cart_item["product_id"]);

        if (isset($cart_item[Order::ORDER_THUMB]) && !empty($cart_item[Order::ORDER_THUMB])) {
            $thumb = $cart_item[Order::ORDER_THUMB];
        }

        return '<img src="' . esc_url($thumb) . '" />';

    }

    function change_product_url($permalink, $cart_item, $cart_item_key)
    {
        if (isset($cart_item[Order::ORDER_THUMB]) && !empty($cart_item[Order::ORDER_THUMB])) {
            return '';
        }
        return $permalink;
    }
}