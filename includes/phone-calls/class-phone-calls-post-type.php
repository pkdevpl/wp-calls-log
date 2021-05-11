<?php

namespace pkdevpl\wpcallslog;

class Phone_Calls_Post_Type {

    function register_actions() {
        add_action( 'init', [$this, 'register_post_type'] );    
        add_filter( 'manage_pkdevpl_phone_calls_posts_columns', [$this, 'manage_columns'] );
        add_filter( 'manage_edit-pkdevpl_phone_calls_sortable_columns', [$this, 'manage_sortable_columns'] );
        add_action( 'manage_pkdevpl_phone_calls_posts_custom_column', [$this, 'manage_columns_content'], 10, 2 );
        add_filter( 'post_row_actions', [$this, 'set_post_row_actions'], 10, 2 );        
        add_filter( 'pkdevpl_add_admin_capabilities', [$this, 'add_admin_capabilities'] );
        add_action( 'admin_footer', [$this, 'remove_add_new_button'] );
        add_action( 'admin_init', array( $this, 'remove_admin_pages' ) );
    }
    
    function add_admin_capabilities($capability_types) {
        $capability_types[] = 'phone_call';
        return $capability_types;
    }

    function set_post_row_actions( $actions, $post ) {
        if('pkdevpl_phone_calls' === $post->post_type) {
            unset($actions['inline hide-if-no-js']);
            unset($actions['edit']);
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

    /**
     * Removes Add New button from post type screen
     * 
     * Adds <style> tag that makes Add New button invisible for edit-post_type and single post_type screens in admin
     */
    
    function remove_add_new_button() {
        $screens = [
            'edit-pkdevpl_phone_calls',
            'pkdevpl_phone_calls'
        ];
		$screen = get_current_screen();
		if( in_array( $screen->id, $screens ) ) { ?>
			<style>
				.wp-heading-inline+.page-title-action:first-of-type {
					display: none;
				}
			</style>
		<?php }
    }

    /**
     * Removes unnecessary pages from admin menu
     */
    
    function remove_admin_pages() {

        // Removes 'add new phone call' and 'all phone calls' subpages from admin menu, leaving only top level page.

        $parent_slug = 'edit.php?post_type=pkdevpl_phone_calls';
        $page_slug = 'post-new.php?post_type=pkdevpl_phone_calls';
        
        remove_submenu_page( $parent_slug, $page_slug );
        
        $page_slug = 'edit.php?post_type=pkdevpl_phone_calls';
        
        remove_submenu_page( $parent_slug, $page_slug );
    }

    function manage_columns_content($column, $post_id) {
        switch($column):
            case 'phone_call_time':
                $call_time = get_post_meta($post_id, 'pkdevpl_phone_call_time', true);
                echo wp_date( 'd.m.Y H:i', $call_time);
            break;
            case 'phone_call_from':
                $call_from = get_post_meta($post_id, 'pkdevpl_phone_call_incoming_number', true);
                $phone_calls = new Phone_Calls;
                $formatted = $phone_calls->format_phone_number($call_from, 'add-spaces');
                echo apply_filters( 'pkdevpl_incoming_call_number_column', $formatted, $call_from, $post_id );
            break;
            case 'phone_call_to':
                $call_to = get_post_meta($post_id, 'pkdevpl_phone_call_receiving_number', true);
                $phone_calls = new Phone_Calls;
                $formatted = $phone_calls->format_phone_number($call_to, 'add-spaces');
                echo apply_filters( 'pkdevpl_receiving_call_number_column', $formatted, $call_to, $post_id );
            break;
            case 'phone_call_device':
                $device_id = get_post_meta($post_id, 'pkdevpl_phone_call_device_wp_id', true);
                $devices = new Devices;
                $device = $devices->get_device($device_id);
                if( null !== $device ) {
                    echo apply_filters( 'pkdevpl_receiving_call_number_column', $device->name, $device, $post_id );
                } else {
                    echo 'Nieznane urządzenie';
                }
            break;
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