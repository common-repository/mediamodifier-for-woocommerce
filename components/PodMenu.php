<?php
namespace mediamodifier\components;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;
use mediamodifier\models\PodTemplate;

class PodMenu
{
    public function  __construct(){
        add_action('admin_menu', array($this, 'loadMenu'), 5);
        add_action('admin_init', array($this, 'registerAndBuildFields'));
    }

    public function loadMenu()
    {
        add_menu_page(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME'), Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_TITLE'), Mediamodifier_PoD_For_WooCommerce::config('LEVEL_ACCESS'), Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME'). '_main',
            array($this, 'displayPoDRootSettings'), Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_DIR_URL') . 'assets/img/favicon-32x32.png', 80);

        add_submenu_page(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_main',
            esc_html__(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_TITLE') . ' Settings', 'mediamodifier-pod-for-woocommerce'),
            esc_html__('Settings', 'mediamodifier-pod-for-woocommerce'),
            Mediamodifier_PoD_For_WooCommerce::config('LEVEL_ACCESS'),
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_main' ,
            array($this, 'displayPoDRootSettings')

        );

        add_submenu_page(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_main',
            esc_html__(Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_TITLE') . ' Templates', 'mediamodifier-pod-for-woocommerce'),
            esc_html__('Templates', 'mediamodifier-pod-for-woocommerce'),
            Mediamodifier_PoD_For_WooCommerce::config('LEVEL_ACCESS'),
            '/edit.php?post_type=' . PodTemplate::TAXENOMY
        );
    }

    /* showing settings page */
    public function displayPoDRootSettings(){
        require_once Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_DIR') . '/partials/pod-main-settings.php';
    }

    /* templates page */
    public function displayPoDTemplates(){
        return wp_redirect("edit.php?post_type=" . PodTemplate::TAXENOMY, 301);
    }

    /* register settings fields for global settings */
    public function registerAndBuildFields()
    {
        add_settings_section(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_section',
            esc_html__('Settings', 'mediamodifier-pod-for-woocommerce'),
            array($this, Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_display_general_account'),
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings'
        );

        $admin_key_settings = array(
            'type' => 'input',
            'subtype' => 'text',
            'required' => 'true',
            'get_options_list' => '',
            'value_type' => 'normal',
            'wp_data' => 'option'
        );

        add_settings_field(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . "_" . Mediamodifier_PoD_For_WooCommerce::config('API_KEY_ADMIN'),
            esc_html__('Api Key Admin', 'mediamodifier-pod-for-woocommerce'),
            array($this, 'mediamodifier_render_settings_field'),
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings',
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_section',
            [
                'type' => 'input',
                'subtype' => 'text',
                'id' => Mediamodifier_PoD_For_WooCommerce::config('API_KEY_ADMIN'),
                'name' => Mediamodifier_PoD_For_WooCommerce::config('API_KEY_ADMIN'),
                'required' => 'required="required"',
                'get_options_list' => '',
                'value_type' => 'normal',
                'wp_data' => 'option',
            ]
        );



        add_settings_field(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . "_" . Mediamodifier_PoD_For_WooCommerce::config('API_KEY_EDITOR'),
            esc_html__('Api Key Editor', 'mediamodifier-pod-for-woocommerce'),
            array($this, 'mediamodifier_render_settings_field'),
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings',
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_section',
            [
                'type' => 'input',
                'subtype' => 'text',
                'id' => Mediamodifier_PoD_For_WooCommerce::config('API_KEY_EDITOR'),
                'name' => Mediamodifier_PoD_For_WooCommerce::config('API_KEY_EDITOR'),
                'required' => 'required="required"',
                'get_options_list' => '',
                'value_type' => 'normal',
                'wp_data' => 'option'
            ]
        );

        add_settings_field(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . "_" . Mediamodifier_PoD_For_WooCommerce::config('REPLACEMENT_CONFIG'),
            esc_html__('Replace “Add to basket” button with plugin button', 'mediamodifier-pod-for-woocommerce'),
            array($this, 'mediamodifier_render_settings_field'),
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings',
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_section',
            [
                'type' => 'input',
                'subtype' => 'checkbox',
                'id' => Mediamodifier_PoD_For_WooCommerce::config('REPLACEMENT_CONFIG'),
                'name' => Mediamodifier_PoD_For_WooCommerce::config('REPLACEMENT_CONFIG'),
                'required' => 'required="required"',
                'get_options_list' => '',
                'value_type' => 'normal',
                'wp_data' => 'option',
            ]
        );

        register_setting(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings',
            Mediamodifier_PoD_For_WooCommerce::config('REPLACEMENT_CONFIG')
        );


        register_setting(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings',
            Mediamodifier_PoD_For_WooCommerce::config('API_KEY_ADMIN')
        );

        register_setting(
            Mediamodifier_PoD_For_WooCommerce::config('PLUGIN_NAME') . '_general_settings',
            Mediamodifier_PoD_For_WooCommerce::config('API_KEY_EDITOR')
        );
    }
    /* global settings title */
    public function mediamodifier_pod_for_woocommerce_display_general_account()
    {
        echo '<p>' . esc_html__('PoD global settings.', 'mediamodifier-pod-for-woocommerce') . '</p>';
    }

    public function mediamodifier_render_settings_field($args)
    {
        if ($args['wp_data'] == 'option') {
            $wp_data_value = get_option($args['name']);
        } elseif ($args['wp_data'] == 'post_meta') {
            $wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
        }
        $_html = "";
        switch ($args['type']) {

            case 'input':
                $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
                if ($args['subtype'] != 'checkbox') {
                    $prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
                    $prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
                    $step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
                    $min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
                    $max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';

                    if (isset($args['disabled'])) {
                        // hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
                        $_html = $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    } else {
                        $_html = $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    }

                } else {
                    $checked = ($value) ? 'checked' : '';
                    $_html = '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
                }
                break;
            default:
                # code...
                break;
        }

        echo wp_kses($_html,
            [
                'div'=>['class'=>[]],
                'span'=>['class'=>[]],
                'input'=>['type'=>[], 'id'=>[], 'min'=>[], 'max'=>'', 'step'=>[], 'size'=>[], 'value'=>[], 'disabled'=>[], 'checked'=>[], 'name'=>[]]
            ]
        );
    }



}