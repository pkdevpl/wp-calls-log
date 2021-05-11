<?php

namespace pkdevpl\wpcallslog;

class Devices_Info_Metabox {

    function register_actions() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_metabox_data']);        
    }
    
    function add_meta_boxes() {
        add_meta_box(
            'pkdevpl_devices_info',
            'Informacje o urządzeniu',
            [$this, 'print_metabox'],
            'pkdevpl_devices'
        );
    }
    
    function save_metabox_data( $post_id ) { 

        $nonce_action = 'save-device-info-metabox'; 
        $nonce_name =   'device-info-nonce';
    
        if( ! isset( $_POST[ $nonce_name ] ) ) return false;
        if( ! wp_verify_nonce( $_POST[$nonce_name], $nonce_action ) ) die( 'Nonce verification failed' );
        
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;
    
        $device_name =      sanitize_text_field( $_POST['device_name'] );
        $device_api_key =   sanitize_text_field( $_POST['device_api_key'] );
        
        update_post_meta( $post_id, 'pkdevpl_device_name', $device_name );
        update_post_meta( $post_id, 'pkdevpl_device_api_key', $device_api_key );  

    }
    
    function print_metabox( $post ) { 

        $nonce_action = 'save-device-info-metabox'; 
        $nonce_name =   'device-info-nonce';
        
        $post_id = $post->ID;
        $post_meta = get_post_meta($post_id);
        
        $device_name = '';
        $api_key = strtoupper(substr(md5($post->ID), 0, 8));

        if(!empty($post_meta['pkdevpl_device_name'][0])) {
            $device_name = $post_meta['pkdevpl_device_name'][0];
        }
        
        if(!empty($post_meta['pkdevpl_device_api_key'][0])) {
            $api_key = $post_meta['pkdevpl_device_api_key'][0];
        }

        wp_nonce_field( $nonce_action, $nonce_name );

        require_once( PLUGIN_PATH . '/admin/metaboxes/devices-info.php');
        
    }

}


