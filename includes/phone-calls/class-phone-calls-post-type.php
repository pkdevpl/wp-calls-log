<?php

namespace pkdevpl\wpcallslog;

/**
 * Class adds pkdevpl_phone_calls post type related actions
 */

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
        add_filter( 'pre_get_posts', [$this, 'add_custom_meta_to_search'] );
    }    

    /**
     * Callback for 'pkdevpl_add_admin_capabilities' filter
     * 
     * Filter is used in function set_admin_capabilities run on plugin activation / deactivation. It gives / removes 
     * capabilities to edit / create / update this specific post type posts. Without it administrator won't see
     * post type page in admin menu.
     * 
     * @param Array $capability_types  Contains capability types defined in register_post_type $args['capability_type']. Usually it's 'post' but for specific post types it might be something else, like 'book' or ['book', 'books']
     */
    
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
        $columns['phone_call_device'] = 'Urządzenie';
        return $columns;
    }

    function manage_sortable_columns($columns) {
        $columns['phone_call_time'] = 'phone_call_time';
        $columns['phone_call_from'] = 'phone_call_from';
        $columns['phone_call_device'] = 'phone_call_device';
        return $columns;
    }

    /**
     * Extends WP_Query search for phone calls to include phone call meta fields
     * 
     * @param WP_Query $wp_query    WP_Query object containig search string and other parameters
     */
    
    function add_custom_meta_to_search( $wp_query ) {
        
        if( is_admin() && 'pkdevpl_phone_calls' === $wp_query->query['post_type'] ) {
            
            $search_term = $wp_query->query_vars['s'];
            
            if( $search_term !== '' ) {
                
                $phone_calls = new Phone_Calls;
                $devices = new Devices;
                
                // If user provided phone number, convert it to match format stored in db
                
                $search_number = $phone_calls->format_phone_number($search_term);

                // If user provided device name, find the device first, to get it's post ID

                $search_device = $devices->search_device($search_term);
                
                $args = ['relation'=>'OR'];

                // If number is valid, add meta field to wp_query
                
                if(! is_wp_error($search_number) ) {
                    $args[] = [
                        'key'=>'pkdevpl_phone_call_incoming_number',
                        'value'=>$search_number,
                        'compare'=>'LIKE'
                    ];
                }

                // If device was found, add it's ID to wp_query
                
                if( $search_device !== null ) {
                    $args[] = [
                        'key'=>'pkdevpl_phone_call_device_wp_id',
                        'value'=>$search_device->ID,
                        'compare'=>'='
                    ];
                }

                // If no device / number was found, don't add anything to wp_query

                if( count( $args ) > 1 ) {
                    $wp_query->query_vars['s'] = '';
                    $wp_query->set( 'meta_query', $args );
                }
            }
        };
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
     * 
     * Removes 'add new phone call' and 'all phone calls' subpages from admin menu. Leaves top-level 'Phone calls' menu
     */
    
    function remove_admin_pages() {

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
                if( $call_time ) {
                    echo wp_date( 'd.m.Y H:i', $call_time);
                } else {
                    echo 'Brak danych';
                }
            break;
            case 'phone_call_from':
                $call_from = get_post_meta($post_id, 'pkdevpl_phone_call_incoming_number', true);
                if( $call_from ) {
                    $phone_calls = new Phone_Calls;
                    $formatted = $phone_calls->format_phone_number($call_from, 'add-spaces');
                    echo apply_filters( 'pkdevpl_incoming_call_number_column', $formatted, $call_from, $post_id );
                } else {
                    echo 'Brak danych';
                }
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