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

require PLUGIN_PATH . '/vendor/autoload.php';

require_once( PLUGIN_PATH . '/includes/devices/class-devices-post-type.php' );
require_once( PLUGIN_PATH . '/includes/devices/class-devices-info-metabox.php' );
require_once( PLUGIN_PATH . '/includes/devices/class-devices.php' );
require_once( PLUGIN_PATH . '/includes/devices/class-device.php' );

require_once( PLUGIN_PATH . '/includes/phone-calls/class-phone-calls-post-type.php' );
require_once( PLUGIN_PATH . '/includes/phone-calls/class-phone-calls-rest.php' );
require_once( PLUGIN_PATH . '/includes/phone-calls/class-phone-calls.php' );
require_once( PLUGIN_PATH . '/includes/functions.php' );

error_reporting(E_ALL);

ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', PLUGIN_PATH . '/error.log');

class WP_Calls_Log {

    function init() {
        
        $phone_calls_post_type = new Phone_Calls_Post_Type();
        $phone_calls_post_type->register_actions();
        
        $devices_post_type = new Devices_Post_Type();
        $devices_post_type->register_actions();
        
        $devices_info_metabox = new Devices_Info_Metabox();
        $devices_info_metabox->register_actions();
        
        $phone_calls_rest = new Phone_Calls_REST();
        $phone_calls_rest->register_actions();
        

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

register_activation_hook( __FILE__, [$wp_calls_log, 'activate'] );
register_deactivation_hook( __FILE__, [$wp_calls_log, 'deactivate'] );
