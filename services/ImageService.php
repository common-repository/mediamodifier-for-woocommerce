<?php

namespace mediamodifier\services;

use mediamodifier\Mediamodifier_PoD_For_WooCommerce;

class ImageService
{
    public static function dataToImage($data, $name_pattern)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $data = str_replace(' ', '+', $data);
            $data = base64_decode($data);

            if ($data === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $filepath = Mediamodifier_PoD_For_WooCommerce::getUploadDirectory() . "/{$name_pattern}.{$type}";

        $fileurl = Mediamodifier_PoD_For_WooCommerce::getUploadUrl() . "/{$name_pattern}.{$type}";

        if (file_put_contents($filepath, $data)) {
            return $fileurl;
        }
    }

    public static function removeImage($file){
        $uploadDir = Mediamodifier_PoD_For_WooCommerce::getUploadDirectory();
        $filename = basename($file);
        $fullpath = $uploadDir . "/" . $filename;
        if(file_exists($fullpath)){
            unlink($fullpath);
        }
    }
}