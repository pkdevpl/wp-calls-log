<?php

namespace pkdevpl\wpcallslog;

class Devices_Post_Type {

    function register_actions() {
        add_action( 'init', [$this, 'register_post_type'] );
        add_filter( 'manage_pkdevpl_devices_posts_columns', [$this, 'manage_columns'] );
        add_filter( 'manage_edit-pkdevpl_devices_sortable_columns', [$this, 'manage_sortable_columns'] );
        add_action( 'manage_pkdevpl_devices_posts_custom_column', [$this, 'manage_columns_content'], 10, 2 );
        add_filter( 'post_row_actions', [$this, 'set_post_row_actions'], 10, 2 );
        add_filter( 'pre_get_posts', [$this, 'add_custom_meta_to_search'] );
        add_filter( 'pkdevpl_add_admin_capabilities', [$this, 'add_admin_capabilities'] );
        add_action( 'admin_post_get_tasker_xml', [$this, 'generate_tasker_profile_xml'] );
    }
    
    function add_admin_capabilities($capability_types) {
        $capability_types[] = ['device', 'devices'];
        return $capability_types;
    }

    function set_post_row_actions( $actions, $post ) {
        
        if('pkdevpl_devices' === $post->post_type) {
            $devices = new Devices;
            $device = $devices->get_device($post->ID);

            $xml_action_url = admin_url( 'admin-post.php' ) . '?action=get_tasker_xml&device_api_key=' . $device->api_key;
            $xml_action_url = wp_nonce_url( $xml_action_url, 'generate_xml' );
        
            $actions_before = [
                'get-tasker-xml'=>'<a href="' . $xml_action_url . '" target="_blank" title="Pobierz plik XML z konfiguracją profilu tasker dla tego urządzenia">Pobierz profil Tasker</a>'
            ];
        
            unset($actions['inline hide-if-no-js']);
            $actions = array_merge($actions_before, $actions);
        }
        return $actions;
    }    
    
    function manage_columns($columns) {
        unset($columns['title']);
        unset($columns['date']);
        $columns['device_name'] = 'Nazwa urządzenia';
        $columns['device_api_key'] = 'Klucz API';
        return $columns;
    }

    function manage_sortable_columns($columns) {
        $columns['device_name'] = 'device_name';
        $columns['device_api_key'] = 'device_api_key';
        return $columns;
    }

    function add_custom_meta_to_search( $wp_query ) {
		
        $search_fields = [
			'pkdevpl_device_name',
            'pkdevpl_device_api_key'
        ];

		if( is_admin() && 'pkdevpl_devices' === $wp_query->query['post_type'] ) {
            
            $search_term = $wp_query->query_vars['s'];
            
            $wp_query->query_vars['s'] = '';
            
            if( $search_term !== '' ) {
                $args = ['relation'=>'OR'];
                foreach( $search_fields as $field ) {
                    array_push( $args, [
                        'key'=>$field,
                        'value'=>$search_term,
                        'compare'=>'LIKE'
                    ]);
                }
                $wp_query->set( 'meta_query', $args );
            }
        };
    }

    function manage_columns_content($column, $post_id) {
        switch($column):
            case 'device_name':
                $device_name = get_post_meta( $post_id, 'pkdevpl_device_name', true);
                echo $device_name;
                break;
            case 'device_api_key':
                $api_key = get_post_meta( $post_id, 'pkdevpl_device_api_key', true);
                echo $api_key;
                break;
            default:
                echo 'Brak danych';
        endswitch;        
    }

    function generate_tasker_profile_xml() {

        $location = admin_url('edit.php?post_type=pkdevpl_devices');

        $is_nonce_correct = check_admin_referer( 'generate_xml' );
        
        if( ! $is_nonce_correct ) wp_redirect( $location );
        
        $device_api_key = $_GET['device_api_key'];

        $devices = new Devices;
        $device = $devices->get_device_by_api_key($device_api_key);

        if( is_wp_error( $device ) || is_null( $device ) ) wp_redirect( $location );

        $xml = new XML_Generator( $device->api_key );
    }

    function register_post_type() {        
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
            'labels'                => $labels,
            'public'                => false,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'capability_type'       => 'device',
            'has_archive'           => false,
            'hierarchical'          => false,
            'menu_position'         => 5,
            'supports'              => false,
            'menu_icon'             => 'dashicons-smartphone'
        ];
        
        register_post_type( 'pkdevpl_devices', $args );
    }
}