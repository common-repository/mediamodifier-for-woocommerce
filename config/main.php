<?php

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

return [
    'PLUGIN_NAME'=>'mediamodifier_pod_for_woocommerce',
    'PLUGIN_UPLOAD_NAME'=>'mediamodifier_pod',
    'PLUGIN_TITLE'=>'Mediamodifier PoD',
    'LEVEL_ACCESS'=>'administrator', // mediamodifier access level
    'JS_BUNDLE'=>'https://static.mediamodifier.com/pod-editor/static/js/bundle.min.js',
    'CSS_BUNDLE'=>'https://static.mediamodifier.com/pod-editor/static/css/bundle.min.css',
    'REST_HOST'=>'https://pod.mediamodifier.com/api/',
    'DOMAIN'=>'https://pod.mediamodifier.com',
    'API_KEY_ADMIN'=>'MEDIAMOFIDIER_API_KEY_ADMIN',
    'REPLACEMENT_CONFIG'=>'MEDIAMOFIDIER_REPLACE_ORDER_BTN',
    'API_KEY_EDITOR'=>'MEDIAMOFIDIER_API_KEY_EDITOR',
    'MODAL_CONTAINER_ID'=>'mm-pod-editor', // default modal identificator
    'PRODUCT_TMPL_ID'=>'pod_template_id', // product table template id
    'PRODUCT_TMPL_LBL'=>'PoD Template', // product table template id columnt name
    'WC_MM_GATEWAYS_MAIN_FILE'=> __FILE__,
    'PLUGIN_DIR' => __DIR__ . '/../',
    'PLUGIN_DIR_URL' => plugin_dir_url(__FILE__) . '../',

];
