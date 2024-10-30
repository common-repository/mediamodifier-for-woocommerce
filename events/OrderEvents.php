<?php

namespace mediamodifier\events;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;
use mediamodifier\models\Order;

class OrderEvents
{
    public static function add($itemId, $item, $orderId){

        if(static::isItemValid(Order::ORDER_POD_ID, $item))
        {
            wc_add_order_item_meta($itemId,  Order::ORDER_POD_ID, $item->legacy_values[ Order::ORDER_POD_ID]);
        }

        if(static::isItemValid(Order::ORDER_THUMB, $item))
        {
            wc_add_order_item_meta($itemId,  Order::ORDER_THUMB, $item->legacy_values[Order::ORDER_THUMB]);
        }

        if(static::isItemValid(Order::ORDER_SOURCE_FILE, $item))
        {
            wc_add_order_item_meta($itemId,  Order::ORDER_SOURCE_FILE, $item->legacy_values[Order::ORDER_SOURCE_FILE]);
        }
    }

    public static function isItemValid($key, $item)
    {
        return (
            isset($item->legacy_values) &&
            isset($item->legacy_values[$key]) &&
            !empty($item->legacy_values[$key])
        );
    }



}