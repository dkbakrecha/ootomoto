<?php

if (!function_exists('pr')) {

    function pr($key) {
        echo "<pre>";
        print_r($key);
        echo "</pre>";
    }

}

if (!function_exists('prd')) {

    function prd($key) {
        pr($key);
        exit;
    }

}

if (!function_exists('show_rating')) {

    function show_rating($key) {
        for ($i = 0; $i < 5; $i++) {
            $cls = "";
            if ($i < $key) {
                $cls = "checked";
            }

            echo '<span class="fa fa-star ' . $cls . '"></span>';
        }
    }

}

if (!function_exists('show_services')) {

    function show_services($booking) {
        //prd($booking->bookingservices);
        $_serviceArr = array();
        foreach($booking->bookingservices  as $_services){
            $_serviceArr[] = $_services->service->name;
        }
        
        return implode(', ', $_serviceArr);
    }

}
