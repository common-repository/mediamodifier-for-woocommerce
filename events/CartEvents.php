<?php

namespace mediamodifier\events;

use mediamodifier\models\Order;
use mediamodifier\models\PodTemplate;
use mediamodifier\services\ImageService;

class CartEvents
{
    public static function add($cart_item_data, $product_id, $variation_id ){

        $data = $_POST['MMPod'];
        $uid = uniqid();
        if( isset( $data[Order::ORDER_THUMB] ) ) {
            $idx = 0;
            $images = [];
            foreach($data[Order::ORDER_THUMB] as $thumb) {
                if(!empty($thumb)) {
                    $images [] = ImageService::dataToImage($thumb, sprintf("%s-%s-%s", $uid, $product_id, $idx++));
                }
            }
            $cart_item_data[Order::ORDER_THUMB] = $images;

        }

        if( isset( $data[Order::ORDER_SOURCE_FILE] ) ) {
            $images = [];
            foreach($data[Order::ORDER_SOURCE_FILE] as $thumb) {
                if(!empty($thumb)){
                    $images [] = ImageService::dataToImage($thumb, sprintf("%s-%s-%s-source", $uid, $product_id, $idx++));
                }
            }

            $cart_item_data[Order::ORDER_SOURCE_FILE] = $images;
        }

        if( isset( $data[Order::ORDER_POD_ID] ) ) {
            $cart_item_data[Order::ORDER_POD_ID] = sanitize_text_field($data[Order::ORDER_POD_ID]);
        }

        return $cart_item_data;

    }


    public static function remove(  $cart_item_key, $cart ){
            $cart_item = $cart->removed_cart_contents[ $cart_item_key ];
        if(isset($cart_item[Order::ORDER_THUMB]) && count($cart_item[Order::ORDER_THUMB])>0){
            foreach($cart_item[Order::ORDER_THUMB] as $file){
                ImageService::removeImage($file);
            }
        }
        if(isset($cart_item[Order::ORDER_SOURCE_FILE]) && count($cart_item[Order::ORDER_SOURCE_FILE])>0){
            foreach($cart_item[Order::ORDER_SOURCE_FILE] as $file){
                ImageService::removeImage($file);
            }
        }
        return true;
    }

}