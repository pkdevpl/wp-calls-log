<?php

namespace pkdevpl\wpcallslog;

/**
 * Sets capabilities for custom post types to administrator role on plugin activation / deactivation
 * 
 * Uses filter pkdevpl_add_admin_capabilities to gather all capability types for post types and gives 
 * access to editing, reading and managing posts. It is required to let admin see custom post types
 * in admin after registration.
 * 
 * @param string $action    Accepts 'add' and 'remove'.
 */

if( ! function_exists('set_admin_capabilities') ) {

    // Action = add / remove
    
    function set_admin_capabilities( $action ) {
        
        $capability_types = apply_filters('pkdevpl_add_admin_capabilities', []);
        
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

/**
 * Makes it easier to display objects and arrays. Used mainly in development to print data for debugging
 * 
 * @param Array|Object|String $content  Anything that can be displayed with print_r
 */

if( ! function_exists('show_pre')) {
    function show_pre($content) {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }
}