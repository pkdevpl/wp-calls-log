<?php

namespace pkdevpl\wpcallslog;

use WP_Query;
use WP_Error;

class Devices {

    /**
     * Gets Device object by post_id or post object
     * 
     * @param WP_Post|int $post You can provide either post object or post_id
     * @return Device|null If device is not found returns null.
     */

    function get_device( $post ) {
        
        if(! $post instanceof WP_Post ) {
            $post = get_post( $post );
            if( null === $post ) return null;
        }

        if( 'pkdevpl_devices' !== $post->post_type ) return null;
        
        $device = new Device;
        $device->init($post);
        
        return $device;

    }

    /**
     * Gets Device object with provided api key
     * 
     * @param   $api_key  string  8 characters string containing capital letters and numbers
     * @return  Device|null|WP_Error
     */

    function get_device_by_api_key( $api_key ) {

        preg_match('/^([0-9A-Z]{8})$/', $api_key, $matches);

        if( ! isset( $matches[1] ) ) {
            return new WP_Error('wpcl_invalid_device_api_key', 'Provided device \'api_key\' is invalid');
        }

        $args = [
            'post_type'=>'pkdevpl_devices',
            'post_status'=>'publish',
            'posts_per_page'=>1,
            'meta_query'=>[
                [
                    'key'=>'pkdevpl_device_api_key',
                    'value'=>$api_key,
                    'compare'=>'='
                ],
            ],
        ];

        $query = new WP_Query($args);
        if($query->have_posts()) {
            $device = $this->get_device($query->posts[0]);
            return $device;
        } else {
            return null;
        }
    }

    /**
     * Searches device by name or api_key and returns Device object or null
     * 
     * @param   $search_string  string  Name of device or api key
     * @return  Device|null
    */

    function search_device( $search_string ) {
        $args = [
            'post_type'=>'pkdevpl_devices',
            'post_status'=>'publish',
            'posts_per_page'=>1,
            'meta_query'=>[
                'relation'=>'OR',
                [
                    'key'=>'pkdevpl_device_name',
                    'value'=>$search_string,
                    'compare'=>'LIKE'
                ],[
                    'key'=>'pkdevpl_device_api_key',
                    'value'=>$search_string,
                    'compare'=>'LIKE'
                ],
            ],
        ];

        $query = new WP_Query( $args );

        if( $query->have_posts() ) {
            $device = $this->get_device($query->posts[0]);
            return $device;
        } else {
            return null;
        }
    }
}