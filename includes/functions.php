<?php

namespace pkdevpl\wpcallslog;

/* 

    In register_post_type() we specify capabilities for editing / reading / deleting post type.
    By default admin has not been granted those capabilities and we need to add them on plugin
    activation.

*/

if( ! function_exists('set_admin_capabilities') ) {
    
    function set_admin_capabilities( $action ) {
        
        $capability_types = apply_filters(PLUGIN_PREFIX . 'add-admin-capabilities', []);
        
        if(! empty($capability_types) ) {
            
            $admin = get_role( 'administrator' );
            
            foreach($capability_types as $capability_type) {
                if( is_array($capability_type) ) {
                    $post = $capability_type[0];        // Singular
                    $posts = $capability_type[1];       // Plural
                } else {
                    $post = $capability_type;           // Singular
                    $posts = $capability_type . 's';    // Plural
                }

                $meta_caps = ["edit_$post", "read_$post", "delete_$post", "edit_$posts", "edit_others_$posts", "publish_$posts", "read_private_$posts"];

                foreach( $meta_caps as $single_cap) {
                    if('add' === $action) {
                        if(!$admin->has_cap($single_cap)) $admin->add_cap($single_cap);
                    } else if('remove' === $action) {
                        if($admin->has_cap($single_cap)) $admin->remove_cap($single_cap);
                    }
                }
            }
        }
    }
}

if( ! function_exists('show_pre')) {
    function show_pre($content) {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }
}