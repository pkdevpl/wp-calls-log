<?php

namespace pkdevpl\wpcallslog;

use DOMDocument;

/**
 * Class creates XML file that user can download and import into Tasker app as a tasker profile
 * 
 * XML projects are the easiest way to setup Tasker to work with this plugin. This class supplies tasker standard XML profile
 * with custom variables like REST URL or device api_key.
 * 
 */

class XML_Generator {

    /**
     * Creates new XML document for device with given api key. XML is used to setup adnroid phone.
     * 
     * Creates XML project file used by Tasker app to setup event recognition on androind phone. XML adds Tasker Profile and Tasker Task
     * Profile tells Tasker to react to incoming phone calls by running Task. Task sends HTTP POST request to website's REST route and thus
     * registers phone call in databse. User can download this file from wp admin and import it to Tasker app. 
     * After XML import phone does not need any additional configuration.
     * 
     * @param string $device_api_key    8 character long string identifyig 
     */
    
    function __construct( $device_api_key ) {
        $dom = new DOMDocument;
        $dom->load( PLUGIN_PATH . '/includes/devices/templates/tasker-project-template.xml' );

        $root = $dom->documentElement;
        
        $base_url = get_site_url( null, 'wp-json/wpcl/v1/incoming_call' );
        $body_data = 'api_key=' . $device_api_key . "\n" . 'incoming_number=%CNUM';

        $task_node = $dom->getElementsByTagName('Task')[0];
        $action_node = $task_node->getElementsByTagName('Action')[0];
        $string_nodes = $action_node->getElementsByTagName('Str');

        $string_nodes[0]->nodeValue = $base_url;
        $string_nodes[2]->nodeValue = $body_data;

        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="WP_Calls_Log_' . $device_api_key . '.prj.xml"');
        echo $dom->saveXML();
    }

}
