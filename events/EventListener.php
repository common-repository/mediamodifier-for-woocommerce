<?php

namespace mediamodifier\events;

class EventListener
{
    public function __construct(){
        add_action('post_updated', array(PostEvents::class, 'update'));
        add_filter( 'woocommerce_add_cart_item_data', array(CartEvents::class, 'add'), 10, 3 );
        add_action( 'woocommerce_cart_item_removed', array(CartEvents::class, 'remove'), 10, 3 );
        add_action( 'woocommerce_new_order_item', array(OrderEvents::class, 'add'), 10, 4 );
    }
}