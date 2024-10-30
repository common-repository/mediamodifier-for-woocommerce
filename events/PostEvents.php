<?php
namespace mediamodifier\events;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

class PostEvents
{
    /* adding trigger for product update */
    public static function update($post_ID){
        $tmpl = get_post_meta($post_ID, Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), true);
        if($tmpl == null || empty($tmpl)){
            return;
        }
        $post = get_post($post_ID);
        if ($post->post_type=='product' ){
            $sku = get_post_meta($post_ID, '_sku', true);
            $p_name = $post->post_title;

            $p_cat = '';

            $terms = get_the_terms( $post_ID, 'product_cat');
            $p_link = get_permalink($post_ID);

            foreach ($terms as $term) {
                $p_cat = $term->name;
                break;
            }

            Mediamodifier_PoD_For_WooCommerce::podApi()->updateTemplate($tmpl, ['meta'=>['product'=>['sku'=>$sku, 'name'=>$p_name, 'category'=>$p_cat, 'url'=>$p_link]]]    );
        }
    }
}