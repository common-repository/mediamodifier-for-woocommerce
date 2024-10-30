<?php

namespace mediamodifier\components;

use mediamodifier\models\Order;


class AdminOrderThumbImage
{
    public function __construct(){
        add_action('woocommerce_admin_order_item_headers', array($this,'custom_columns_headers'));
        add_action('woocommerce_admin_order_item_values', array($this,'custom_admin_order_item_values'), 10, 3);
        add_filter('woocommerce_hidden_order_itemmeta', array($this,'hide_order_item_meta_fields' ));
    }

    function hide_order_item_meta_fields( $fields ) {
        $fields[] = Order::ORDER_POD_ID;
        $fields[] = Order::ORDER_THUMB;
        return $fields;
    }

    function custom_columns_headers() {
        $column_name1 = esc_html('Source files', 'mediamodifier_pod_for_woocommerce');
        $column_name2 = esc_html('Customized design', 'mediamodifier_pod_for_woocommerce');
        echo wp_kses('<th>' . $column_name1 . '</th><th>' . $column_name2 . '</th>', ['th'=>[]]);
    }

    function custom_admin_order_item_values($_product, $item, $item_id = null) {

        $thumbs = wc_get_order_item_meta($item_id, OrdeR::ORDER_THUMB, true);
        $sources = wc_get_order_item_meta($item_id, OrdeR::ORDER_SOURCE_FILE, true);
        $source_data = $designed_data = "";

        if(!empty($sources) && count($sources)>0) {
            foreach($sources as $image)
            {
                $source_data .= '<li style="margin:2px"><a href="' .esc_attr($image) . '?" target="_blank" class="attachment-thumbnail size-thumbnail" rel="'.esc_attr('src_' . $item_id).'"><img src="'.esc_attr($image).'?" class="attachment-thumbnail size-thumbnail" height="30px" class="attachment-thumbnail size-thumbnail " ></a></li>';
            }
            $source_data = sprintf('<ul>%s</ul>', $source_data);
        }

        if(!empty($thumbs) && count($thumbs)>0) {
            foreach($thumbs as $image)
            {
                $designed_data .= '<li style="margin:2px"><a href="' . esc_attr($image) . '?" class="thickbox" rel="' . esc_attr('thumbs_' . $item_id). '"><img src="' . esc_attr($image) . '?" height="30px" class="attachment-thumbnail size-thumbnail " alt="" ></a></li>';
            }
            $designed_data = sprintf('<ul>%s</ul>', $designed_data);
        }

        if($item['type']=="line_item"){
            echo wp_kses(sprintf('<td>%s</td><td>%s</td>', $source_data,  $designed_data),
            [
                'td'=>[],
                'ul'=>[],
                'li'=>['style'=>[]],
                'a'=>['href'=>[], 'class'=>[], 'rel'=>[], 'alt'=>[], 'target'=>[]],
                'img'=>['src'=>[], 'class'=>[], 'height'=>[],'alt'=>[]]
            ]) ;
        }
    }
}