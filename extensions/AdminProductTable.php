<?php

namespace mediamodifier\extentsions;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

class AdminProductTable {

    private static $parent = null;

    public function __construct(){
        add_filter( 'post_row_actions', array($this, 'pod_edit_template_action'), 10, 2);
        /* remove template link */
        add_filter( 'post_row_actions', array($this, 'pod_remove_template_action'), 10, 2);
        add_filter( 'post_row_actions', array($this, 'pod_set_template_action'), 10, 2);
        /* adding extra columns */
        add_filter( 'manage_product_posts_columns', array($this, 'set_custom_product_columns') );
        add_action( 'manage_product_posts_custom_column', array($this, 'custom_product_column'), 10, 2);
        add_filter( 'manage_edit-product_sortable_columns', array($this, 'custom_column_register_sortable') );
    }
    // Add "Clone" link to each row in the Woo Attributes admin page
    public function pod_edit_template_action($actions, $post)
    {
        if ($post->post_type=='product' ){
            $tmplId = esc_html(get_post_field(Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), $post));
            $postId = $post->ID;

            if(!empty($tmplId)){
                $hrefEvent = sprintf("mm.product.edit_template(event, %s, \"%s\")", $postId, $tmplId);
                $actions['pod_templ_editing'] = '<a href="#" onclick="'.esc_js($hrefEvent).'" title="" rel="nofollow" aria-label="' . esc_html__("Edit PoD Template", 'mediamodifier-pod-for-woocommerce') . '">' . esc_html__("Edit PoD Template", 'mediamodifier-pod-for-woocommerce') . '</a>';
            }else{
                $hrefEvent = sprintf("mm.product.add_template(event, %s)", $postId);
                $actions['pod_templ_editing'] = '<a href="#" onclick="'.esc_js($hrefEvent).'" title="" rel="nofollow" aria-label="'. esc_html__("Add PoD Template", 'mediamodifier-pod-for-woocommerce'). '">'.esc_html__('Add PoD Template', 'mediamodifier-pod-for-woocommerce') . '</a>';
            }
        }
        return $actions;
    }
    
    public function pod_set_template_action($actions, $post)
    {
        if ($post->post_type=='product' ){
            $tmplId = get_post_field(Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), $post);
            $postId = $post->ID;
            $hrefEvent = sprintf("mm.product.set_template(event, %s,  \"%s\")", $postId, $tmplId);
            $actions['pod_templ_set'] = wp_kses('<a href="#" onclick="'.esc_js($hrefEvent).'" title="" rel="nofollow" aria-label="' . esc_html__("Set PoD Template", 'mediamodifier-pod-for-woocommerce') . '">' . esc_html__("Set PoD Template", 'mediamodifier-pod-for-woocommerce') . '</a>',
            ['a'=>['href'=>[], 'onclick'=>[], 'title'=>[], 'rel'=>[]]]);
            
        }
        return $actions;
    }
    
    public function pod_remove_template_action($actions, $post)
    {
        if ($post->post_type=='product' ){
            $tmplId = get_post_field(Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), $post);
            $postId = $post->ID;

            if(!empty($tmplId)){
                $hrefEvent = sprintf("mm.product.unlink_template(event, %s,  \"%s\")", $postId, $tmplId);
                $actions['pod_templ_unlink'] = wp_kses('<a href="#" onclick="'.esc_js($hrefEvent).'" title="" rel="nofollow" aria-label="' . esc_html__("Unlink PoD Template", 'mediamodifier-pod-for-woocommerce') . '">' . esc_html__("Unlink PoD Template", 'mediamodifier-pod-for-woocommerce') . '</a>',
                    ['a'=>['href'=>[], 'onclick'=>[], 'title'=>[], 'rel'=>[]]]);
            }
        }
        return $actions;
    }
    /* adding columns and labels */
    public function set_custom_product_columns($columns) {
        $positionIndex = 1;
        return array_slice( $columns, 0, $positionIndex, true )
            + ['pod_template_id' => esc_html__("PoD Template", 'mediamodifier-pod-for-woocommerce')]
            + array_slice( $columns, $positionIndex, count( $columns ) - $positionIndex, true);
    }
    /* show values in the column */
    public function custom_product_column( $column, $post_id ) {
        global $post;

        switch ( $column ) {
            case 'pod_template_id'   :
                if(get_post_meta( $post_id , $column , true )) {
                    $tmplId = get_post_meta($post_id, 'pod_template_id', true);
                    echo wp_kses(sprintf("<a href=\"#\" onclick=\"". esc_js("mm.product.edit_template(event, %s, \"%s\")") . "\">%s</a> ",
                        $post_id,
                        $tmplId,
                        $tmplId
                    ), ['a'=>['href'=>[], 'onclick'=>[]]]);

                }else{
                    echo " - ";
                }
                break;

        }
    }
    /*add columns sortable functionality */
    public function custom_column_register_sortable( $columns ) {
        $columns['pod_template_id'] = 'pod_template_id';
        return $columns;
    }
}