<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 10:38 PM
 * About: I will tell you later
 */


namespace fingerprint;


class UrlEncode{

    function createValidBase64FMD($url_safe_string){
        $len_param = strlen($url_safe_string);
        if ($len_param % 4 === 2){
            $url_safe_string = $url_safe_string . "==";
        }

        if ($len_param % 4 === 3){
            $url_safe_string = $url_safe_string . "=";
        }

        $url_safe_string = str_replace("-", "+", $url_safe_string);
        $url_safe_string = str_replace("_", "/", $url_safe_string);
        return $url_safe_string;
    }

    function base64UrlEncode($base64_string){
        $base64_string = str_replace("+", "-", $base64_string);
        $base64_string = str_replace("/", "_", $base64_string);
        $base64_string = str_replace("=", "", $base64_string);
        return $base64_string;
    }
}