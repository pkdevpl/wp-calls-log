<?php

namespace pkdevpl\wpcallslog;

/**
 * Single Device object representing device connected to Tasker app.
 * 
 * It should not be created directly. Use Devicess->get_device() to create Device object.
 */

class Device {
    public $ID;
    public $post;
    public $name;
    /**
     * Device api_key used in Tasker app to vlidate http request.
     * 
     * @param string $api_key   Consists of 8 letters and numbers ^[0-9A-Z]{8}$
     */
    public $api_key;

    /**
     * Setup metadata for Device object
     * 
     * @param WP_Post $post Accepts WP_Post object (not post_id). 
     */
    
    function init( $post ) {        
        $this->post =   $post;
        $this->ID =     $this->post->ID;

        $post_meta = get_post_meta( $this->post->ID );
        
        $this->name =       $post_meta['pkdevpl_device_name'][0];
        $this->api_key =    $post_meta['pkdevpl_device_api_key'][0];
    }
}