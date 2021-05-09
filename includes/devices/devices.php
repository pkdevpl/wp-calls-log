<?php

namespace pkdevpl\wpcallslog;

// Register devices post type

add_action( 'init', function() {
    
    $labels = [
        'name'                      => 'Urządzenia',
        'singular_name'             => 'Urządzenie',
        'menu_name'                 => 'Urządzenia',
        'name_admin_bar'            => 'Urządzenie',
        'add_new'                   => 'Dodaj urządzenie',
        'add_new_item'              => 'Dodaj nowe urządzenie',
        'new_item'                  => 'Nowe urządzenie',
        'edit_item'                 => 'Edytuj urządzenie',
        'view_item'                 =>'Pokaż urządzenie',
        'all_items'                 =>'Wszystkie urządzenia',
        'search_items'              =>'Wyszukaj urządzenia',
        'parent_item_colon'         =>'Nadrzędne urządzenie',
        'not_found'                 =>'Nie znaleziono urządzeń',
        'not_found_in_trash'        =>'Nie znaleziono w koszu',
        'featured_image'            =>'Miniaturka',
        'set_featured_image'        =>'Ustaw miniaturkę',
        'remove_featured_image'     =>'Usuń miniaturkę',
        'use_featured_image'        =>'Użyj miniaturki',
        'archives'                  =>'Archiwum',
        'insert_into_item'          =>'Dodaj do urządzenia',
        'uploaded_to_this_item'     =>'Przesłane do tego urządzenia',
        'filter_items_list'         =>'Filtruj listę urządzeń',
        'items_list_navigation'     =>'Nawiguj po urządzeniach',
        'items_list'                =>'Lista urządzeń'
    ];
 
    $args = [
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'device',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => false,
    ];
 
    register_post_type( PLUGIN_PREFIX . 'devices', $args );
});



// Add post type capabilities to admin role

add_filter(PLUGIN_PREFIX . 'add-admin-capabilities', function($capability_types) {
    $capability_types[] = ['device', 'devices'];
    return $capability_types;
});



// Add custom columns to devices posts table

add_filter('manage_' . PLUGIN_PREFIX . 'devices_posts_columns', function($columns) {
    unset($columns['title']);
    unset($columns['date']);
    $columns['device_name'] = 'Nazwa urządzenia';
    $columns['device_api_key'] = 'Klucz API';
    return $columns;
});

add_action('manage_' . PLUGIN_PREFIX . 'devices_posts_custom_column', function($column, $post_id) {
    switch($column):
        case 'device_name':
            $device_name = get_post_meta( $post_id, PLUGIN_PREFIX . 'device_name', true);
            echo $device_name;
            break;
        case 'device_api_key':
            $api_key = get_post_meta( $post_id, PLUGIN_PREFIX . 'device_api_key', true);
            echo $api_key;
            break;
        default:
            echo 'Brak danych';
    endswitch;        
}, 10, 2);