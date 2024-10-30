<?php
namespace mediamodifier\services;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

class PoDApiREST
{
    private const GET_TEMPLATES = "templates";

    private const CREATE_TEMPLATE = "templates";

    private const CREATE_ORDER = "templates/orders";

    private const UPDATE_TEMPLATE = "templates/%s";

    private function postRequest($url, $data = []){
        $response = wp_remote_post(Mediamodifier_PoD_For_WooCommerce::config('REST_HOST') . $url,
            [
                'timeout' => 10,
                'body'=>$data,
                'data_format' => 'body',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization'=> Mediamodifier_PoD_For_WooCommerce::getAdminApiKey()
                ]
            ]
        );

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    private function getRequest($url){
        $response = wp_remote_get(Mediamodifier_PoD_For_WooCommerce::config('REST_HOST') . $url,
            [
                'timeout' => 10,
                'headers' => [
                    'Authorization'=> Mediamodifier_PoD_For_WooCommerce::getAdminApiKey()
                ]
            ]
        );

        $response_code = wp_remote_retrieve_response_code($response);

        if($response_code == 200){
            return json_decode( wp_remote_retrieve_body( $response ), true );
        }else{
            return [];
        }

    }

    public function updateTemplate($tmplid, $data){
        return $this->postRequest(sprintf(static::UPDATE_TEMPLATE, $tmplid), wp_json_encode($data));
    }

    public function fetchTemplates(){
        return $this->getRequest(static::GET_TEMPLATES);
    }

    public function createTemplate($data = []){
        return $this->postRequest(static::CREATE_TEMPLATE, wp_json_encode($data));
    }

    public function createOrder($data = []){
        return $this->postRequest(static::CREATE_ORDER, wp_json_encode($data));
    }
}