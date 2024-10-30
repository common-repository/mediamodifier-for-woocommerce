<?php

namespace mediamodifier\models;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

class PodTemplate
{
    public const TAXENOMY = 'pod_template';

    private const GET_TEMPLATES_REST = "templates";

    private const POST_STATUS_PUBLISHED = "publish";

    public function __construct(){

        add_action('init', array($this, 'registrateTemplatesType'));
        add_filter( 'manage_pod_template_posts_columns', array($this, 'set_custom_pod_templates_columns') );
        add_action( 'manage_pod_template_posts_custom_column', array($this, 'custom_pod_templates_column'), 10, 2);
        add_filter( 'manage_edit-pod_template_sortable_columns', array($this,"custom_column_register_sortable") );

        add_filter('page_row_actions', array($this, 'pod_edit_template_action'), 10, 2);
        //add sync button
        add_action('manage_posts_extra_tablenav', array($this, 'add_synchronization_button'));
        add_action( 'restrict_manage_posts', array($this, 'synchronize_pod_templates') );
        register_activation_hook( __FILE__, array($this, 'synchronizeTemplates'));
    }

    /*
     * @description: synchronize with mediamodifier
     */
    function add_synchronization_button($where)
    {
        global $post_type_object;

        if ($post_type_object->name === static::TAXENOMY) {
            echo wp_kses('<div style="margin:0; text-align:left;">'
                    .'<button type="submit" name="synchronize_pod_templates" style="height:32px;" class="button" value="yes">'
                    . esc_html__('Synchronize with Mediamodifier', 'mediamodifier_pod_for_woocommerce')
                    . '</button>
                </div>', [
                    'div'=>['style'=>[]],
                    'button'=>['type'=>[], 'name'=>[], 'style'=>[], 'value'=>[], 'class'=>[]]
            ]);
        }
    }

// Trigger an action (or run some code) when the button is pressed

    public function synchronize_pod_templates() {
        global $pagenow, $typenow;

        if ( static::TAXENOMY === $typenow && 'edit.php' === $pagenow &&
            isset($_GET['synchronize_pod_templates']) && $_GET['synchronize_pod_templates'] === 'yes' ) {
            $this->synchronizeTemplates();
        }
    }

    private function synchronizeTemplates() {

        $ext_tmpls = $this->fetchExternalTemplates();

        foreach($ext_tmpls as $template){

            $local_tmpl = $this->fetchLocalTemplates($template->pod_template_id);

            if(count($local_tmpl) == 0){
                $this->addLocalTemplate($template);
            }else{
                $this->updateLocalTemplate($local_tmpl[0], $template);
            }
        }

    }

    private function updateLocalTemplate($current_tmpl, $tmpl){
            wp_update_post([
            'ID'=>$current_tmpl->ID,
            'post_title'=>esc_html($tmpl->product_name),
            'post_date'=>$tmpl->date !==  null  ? $tmpl->date : date('Y-m-d H:i:s'),
            'meta_input' => array(
                'sku' => $tmpl->sku,
                'pod_template_id' => $tmpl->pod_template_id
            )
        ]);
    }

    private function addLocalTemplate($tmpl){
        global $user_ID;
        wp_insert_post([
            'post_type'=>static::TAXENOMY,
            'post_title'=> esc_html($tmpl->product_name),
            'post_author'=>$user_ID,
            'post_date'=>$tmpl->date !==  null  ? $tmpl->date : date('Y-m-d H:i:s'),
            'post_status'=>static::POST_STATUS_PUBLISHED,
            'meta_input' => array(
                'sku' => $tmpl->sku,
                'pod_template_id' => $tmpl->pod_template_id
            )
        ]);
    }

    private function fetchLocalTemplates($template_id){
        $args = array(
            'post_type' => static::TAXENOMY,
            'post_status' => static::POST_STATUS_PUBLISHED,
            'posts_per_page' => -1,
            'meta_key' => 'pod_template_id',
            'meta_query' => array(
                array(
                    'key' => 'pod_template_id',
                    'value' => $template_id,
                    'compare' => '=',
                )
            )
        );

        return  get_posts( $args );
    }

    private function fetchExternalTemplates(){

        $templates_response = Mediamodifier_PoD_For_WooCommerce::podApi()->fetchTemplates();

        $list_of_templates = [];

        foreach($templates_response as $tmpl){
            if(isset($tmpl['meta'])){
                $template = $tmpl['meta'];
                $product = $template['product'];

                $o = new \StdClass();
                $o->pod_template_id  = $tmpl['_id'];
                $o->product_name   = $product['name'];
                $o->sku    = isset($product['sku']) ? $product['sku'] : '';
                $o->date   = date('Y-m-d H:i:s');

                $list_of_templates[] = $o;
            }
        }

        return $list_of_templates;
    }

    public function registrateTemplatesType(){
        register_post_type('pod_template',
            array(
                'labels' => array(
                    'id' => Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME'). '_templates',
                    'name'=>esc_html__('Mediamodifier PoD Templates', 'mediamodifier-pod-for-woocommerce'),
                    'singular_name' => esc_html__('Template', 'mediamodifier-pod-for-woocommerce'),
                    'edit' => esc_html__('Edit', 'mediamodifier-pod-for-woocommerce'),
                    'edit_item' => esc_html__('Edit Template', 'mediamodifier-pod-for-woocommerce'),
                    'new_item' => esc_html__('New Template', 'mediamodifier-pod-for-woocommerce'),
                    'view' => esc_html__('View Template', 'mediamodifier-pod-for-woocommerce'),
                    'view_item' => esc_html__('View Template', 'mediamodifier-pod-for-woocommerce'),
                    'search_items' => esc_html__('Search Templates', 'mediamodifier-pod-for-woocommerce'),
                    'not_found' => esc_html__('No Templates found', 'mediamodifier-pod-for-woocommerce'),
                    'not_found_in_trash' => esc_html__('No Templates found in trash', 'mediamodifier-pod-for-woocommerce')
                ),
                'show_in_menu'=>false,
                'capabilities' => array(
                    'create_posts' =>   false, // Removes support for the "Add New"
                    'manage_terms'          => false,
                    'edit_terms'            => false,
                    'delete_terms'          => false,
                ),
                'show_in_rest' => false,
                'map_meta_cap' => false,
                'public' => true,
                'hierarchical' => true,
                'has_archive' => false,
                'supports' => array(
                    'title',
                    'editor',
                    'created_at',
                    'thumbnail',
                    'custom-fields'
                ),
                'can_export' => false,
                'custom-fields' => [
                    'pod_template_id'
                ]
            )

        );
        /* adding custom fields */
        foreach(['pod_template_id', 'sku']as $field){
            register_post_meta('pod_template', $field, array(
                'type'              => 'string',
                'description'       => '',
                'single'            => false,
                'sanitize_callback' => null,
                'auth_callback'     => null,
                'show_in_rest'      => false,
            ));
        }

        /* adding custom attribute to product fields */
        register_post_meta('product', 'pod_template_id', array(
            'type'              => 'string',
            'description'       => '',
            'single'            => false,
            'sanitize_callback' => null,
            'auth_callback'     => null,
            'show_in_rest'      => true,
        ));


        /* synchronize local templates */
        //$this->synchronizeTemplates();
    }

    // Add "Clone" link to each row in the Woo Attributes admin page
    public function pod_edit_template_action($actions, $post)
    {

        if ($post->post_type==static::TAXENOMY ) {
            //remove view action
            unset( $actions['view'] );
            $tmplId = get_post_meta($post->ID, Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), true);
            $hrefEvent = sprintf("mm.edit_template(event, \"%s\")",
                $tmplId
            );
            $actions['pod_templ_editing'] = '<a href="#" onclick="' . esc_js($hrefEvent) . '" title="" rel="nofollow" aria-label="' . esc_html__("Edit PoD Template", 'mediamodifier-pod-for-woocommerce') .'">' . esc_html__("Edit PoD Template", 'mediamodifier-pod-for-woocommerce'). '</a>';

        }

        return $actions;
    }
    /* adding post type POD_TEMPLATE */
    public function addTaxenomy(){

        register_taxonomy(static::TAXENOMY, static::TAXENOMY,
            array(
                "hierarchical" => true,
                "label" => esc_html__("POD Templates", 'mediamodifier-pod-for-woocommerce'),
                "singular_label" => esc_html__("POD Template", 'mediamodifier-pod-for-woocommerce'),
                'query_var' => false,
                'rewrite' => array(
                    'slug' => Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME'). '_templates',
                    'with_front' => true
                ),
                'public' => true,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'show_ui' => true,
                'show_tagcloud' => true,
                '_builtin' => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => false
            )
        );
    }
    /* adding columns and labels */
    public function set_custom_pod_templates_columns($columns) {
        $columns['sku'] = esc_html__('SKU', 'mediamodifier_pod_for_woocommerce');
        $columns['pod_template_id'] = esc_html__('PoD Template', 'mediamodifier_pod_for_woocommerce');
        return $columns;
    }

    /* show values in the column */
    public function custom_pod_templates_column( $column, $post_id ) {
        global $post;
        switch ( $column ) {
            case 'pod_template_id':
                $tmplId = get_post_meta($post_id, Mediamodifier_PoD_For_WooCommerce::config('PRODUCT_TMPL_ID'), true);
                $hrefEvent = "<a href='#' onclick='". esc_js("mm.edit_template(event, \"" . esc_attr($tmplId)  . "\")") . "'>" . esc_attr($tmplId) . "</a>";
                echo wp_kses($hrefEvent, ['a'=>['href'=>[], 'onclick'=>[]]]);
                break;
            case 'sku':
                echo esc_html(get_post_meta( $post_id , $column , true ));
                break;
        }
    }
    /*add columns sortable functionality */
    public function custom_column_register_sortable( $columns ) {
        $columns['sku'] = 'sku';
        $columns['pod_template_id'] = 'pod_template_id';
        return $columns;
    }
}
