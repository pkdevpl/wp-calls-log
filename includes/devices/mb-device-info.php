<?php

namespace pkdevpl\wpcallslog;

add_action('add_meta_boxes', function() {
    $screens = [PLUGIN_PREFIX . 'devices'];
    foreach( $screens as $screen ) {
        add_meta_box(
            PLUGIN_PREFIX . 'devices-info',
            'Informacje o urządzeniu',
            __NAMESPACE__ . '\print_device_info_metabox',
            $screen
        );
    }
});

add_action('save_post', function( $post_id ) {
    $device_name =  sanitize_text_field( $_POST['device_name'] );
    $api_key =      sanitize_text_field( $_POST['api_key'] );
    
    update_post_meta( $post_id, PLUGIN_PREFIX . 'device_name', $device_name );
    update_post_meta( $post_id, PLUGIN_PREFIX . 'api_key', $api_key );
});

if( ! function_exists( 'print_device_info_metabox' )) {
    function print_device_info_metabox( $post ) { 
        $post_id = $post->ID;
        $post_meta = get_post_meta($post_id);
        
        $device_name = '';
        $api_key = strtoupper(substr(md5($post->ID), 0, 8));

        if(!empty($post_meta[PLUGIN_PREFIX . 'device_name'][0])) {
            $device_name = $post_meta[PLUGIN_PREFIX . 'device_name'][0];
        }

        if(!empty($post_meta[PLUGIN_PREFIX . 'api_key'][0])) {
            $api_key = $post_meta[PLUGIN_PREFIX . 'api_key'][0];
        }
    ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="device_name">Nazwa urządzenia</label>
                </th>
                <td>
                    <input type="text" name="device_name" id="device_name" class="regular-text" required value="<?= $device_name ?>"/>
                    <p class="description">Twoja własna, krótka nazwa urządzenia</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="device_name">Klucz API</label>
                </th>
                <td>
                    <input type="text" name="device_api_key" id="device_api_key" disabled value="<?= $api_key ?>" style="background-color: #efefef; border-color: #efefef; color: #000"/>
                </td>
            </tr>
        </table>
    <?php }
}


