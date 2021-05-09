<?php

namespace pkdevpl\wpcallslog;

class Phone_Calls_Post_Type {

    function register_actions() {
        add_action( 'init', [$this, 'register_post_type'] );    
        add_filter( 'manage_pkdevpl_phone_calls_posts_columns', [$this, 'manage_columns'] );
        add_filter( 'manage_edit-pkdevpl_phone_calls_sortable_columns', [$this, 'manage_sortable_columns'] );
        add_filter( 'post_row_actions', [$this, 'set_post_row_actions'], 10, 2 );        
        add_filter( 'pkdevpl_add_admin_capabilities', [$this, 'add_admin_capabilities'] );
    }
    
    function add_admin_capabilities($capability_types) {
        $capability_types[] = 'phone_call';
        return $capability_types;
    }

    function set_post_row_actions( $actions, $post ) {
        if('pkdevpl_phone_calls' === $post->post_type) {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }    
    
    function manage_columns($columns) {
        unset($columns['title']);
        unset($columns['date']);
        $columns['phone_call_time'] = 'Data połączenia';
        $columns['phone_call_from'] = 'Od';
        $columns['phone_call_to'] = 'Do';
        $columns['phone_call_device'] = 'Urządzenie';
        return $columns;
    }

    function manage_sortable_columns($columns) {
        $columns['phone_call_time'] = 'phone_call_time';
        $columns['phone_call_from'] = 'phone_call_from';
        $columns['phone_call_to'] = 'phone_call_to';
        $columns['phone_call_device'] = 'phone_call_device';
        return $columns;
    }

    function manage_columns_content($column, $post_id) {
        switch($column):
            default:
                echo 'Brak danych';
        endswitch;        
    }

    function register_post_type() {        
        $labels = [
            'name'                      => 'Połączenia',
            'singular_name'             => 'Połączenie',
            'menu_name'                 => 'Połączenia',
            'name_admin_bar'            => 'Połączenie',
            'add_new'                   => 'Dodaj połączenie',
            'add_new_item'              => 'Dodaj nowe połączenie',
            'new_item'                  => 'Nowe połączenie',
            'edit_item'                 => 'Edytuj połączenie',
            'view_item'                 =>'Pokaż połączenie',
            'all_items'                 =>'Wszystkie połączenia',
            'search_items'              =>'Wyszukaj połączenia',
            'parent_item_colon'         =>'Nadrzędne połączenie',
            'not_found'                 =>'Nie znaleziono połączeń',
            'not_found_in_trash'        =>'Nie znaleziono w koszu',
            'featured_image'            =>'Miniaturka',
            'set_featured_image'        =>'Ustaw miniaturkę',
            'remove_featured_image'     =>'Usuń miniaturkę',
            'use_featured_image'        =>'Użyj miniaturki',
            'archives'                  =>'Archiwum',
            'insert_into_item'          =>'Dodaj do połączenia',
            'uploaded_to_this_item'     =>'Przesłane do tego połączenia',
            'filter_items_list'         =>'Filtruj listę połączeń',
            'items_list_navigation'     =>'Nawiguj po połączeniach',
            'items_list'                =>'Lista połączeń'
        ];
        
        $args = [
            'labels'                => $labels,
            'public'                => false,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'capability_type'       => 'phone_call',
            'has_archive'           => false,
            'hierarchical'          => false,
            'menu_position'         => 5,
            'supports'              => false,
            'menu_icon'             => 'dashicons-phone'
        ];
        
        register_post_type( 'pkdevpl_phone_calls', $args );
    }
}

$phone_calls_post_type = new Phone_Calls_Post_Type();
$phone_calls_post_type->register_actions();