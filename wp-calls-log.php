<?php

/*
Plugin Name: WP Calls Log
Plugin URI: github.com/pkdevpl/wp-calls-log
Description: Plugin registers incoming phone calls using Tasker App for Android and WP REST API. It can work with any app capable of detecting incoming phone calls and sending http requests.
Author: Piotr Kucułyma
Author URI: github.com/pkdevpl
License: LGPL
*/

namespace pkdevpl\wpcallslog;

defined('ABSPATH') or die;

define( __NAMESPACE__ . '\PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define( __NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url( __FILE__ ));
define( __NAMESPACE__ . '\PLUGIN_PREFIX', 'wpcallslog-');

require_once(PLUGIN_PATH . '/includes/custom-post-types.php');
require_once(PLUGIN_PATH . '/includes/functions.php');

if( ! class_exists('WP_Calls_Log') ) :

class WP_Calls_Log {

    function init() {
        
    }
    
    function activate() {
        set_admin_capabilities('add');
        flush_rewrite_rules();
    }
    
    function deactivate() {
        set_admin_capabilities('remove');
        flush_rewrite_rules();
    }
}

$wp_calls_log = new WP_Calls_Log();
$wp_calls_log->init();

endif;

register_activation_hook( __FILE__, [$wp_calls_log, 'activate'] );
register_deactivation_hook( __FILE__, [$wp_calls_log, 'deactivate'] );
