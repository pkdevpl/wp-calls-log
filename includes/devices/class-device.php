<?php

namespace pkdevpl\wpcallslog;

class Device {
    public $ID;
    public $post;
    public $name;
    public $api_key;

    function init( $post ) {
        
        $this->post =   $post;
        $this->ID =     $this->post->ID;

        $post_meta = get_post_meta( $this->post->ID );
        
        $this->name =       $post_meta['pkdevpl_device_name'][0];
        $this->api_key =    $post_meta['pkdevpl_device_api_key'][0];
    }
}