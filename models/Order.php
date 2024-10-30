<?php

namespace mediamodifier\models;

class Order
{
    public const ORDER_THUMB = '_pod_order_thumb';

    public const ORDER_POD_ID = '_pod_order_id';

    public const ORDER_SOURCE_FILE = '_pod_order_source';



    private const POST_TYPE = 'shop_order';

    private $order = null;

    public static function find($id){

        $post = get_post($id);

        if($post==null) {
            return null;
        }
        return new Order($post);
    }

    public function __construct($order){
        $this->order = $order;
    }

    public function getThumbnail() : ?string{
        $data =  get_post_meta($this->order->ID, static::ORDER_THUMB, true);

        if($data !== null && isset($data[0])){
            return $data[0];
        }
        return null;
    }

    public function getThumbnails() : ?array {
        return get_post_meta($this->order->ID, static::ORDER_THUMB, true);
    }

    public function addThumbnail($imageData) : bool {
        return add_post_meta($this->order->ID, static::ORDER_THUMB, $imageData);
    }

    public function addThumbnails($imageData) : bool {
        return add_post_meta($this->order->ID, static::ORDER_THUMB, $imageData);
    }
}