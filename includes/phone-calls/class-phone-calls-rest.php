<?php

namespace pkdevpl\wpcallslog;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class Phone_Calls_REST {

    function register_actions() {
        add_action( 'rest_api_init', [$this, 'add_rest_routes'] );
    }

    /**
     * Registers rest route for incoming calls
     */

    function add_rest_routes() {
        $namespace = 'wpcl/v1';
        $route = '/incoming_call';
        $endpoint = [
            'methods'=>'POST',
            'callback'=>[$this, 'handle_incoming_call'],
            'permission_callback' => '__return_true',
        ];

        register_rest_route( $namespace, $route, $endpoint );
    }

    /**
     * Checks parameters for incoming call route and registers new phone call in database
     */

    function handle_incoming_call( WP_REST_Request $request ) {

        $response = [
            'success'=>false,
            'statusCode'=>400,
            'code'=>'wpcl_request_completed',
            'message'=>'Request was completed',
            'data'=>null
        ];

        // Check if api_key is provided
        
        $api_key = $request->get_param('api_key');
        
        if( is_null( $api_key ) ) {
            $response['code'] = 'wpcl_api_key_null';
            $response['message'] = 'You need to provide \'api_key\' parameter';
            return new WP_REST_Response($response);
        }
        
        // Check if incoming_number is provided
        
        $number_from = $request->get_param('incoming_number');
        
        if( is_null( $number_from ) ) {
            $response['code'] = 'wpcl_incoming_number_null';
            $response['message'] = 'You need to provide \'incoming_number\' parameter';
            return new WP_REST_Response($response);
        }

        // Register phone call and handle errors

        $phone_calls = new Phone_Calls();
        $post_id = $phone_calls->register_phone_call($number_from, $api_key);

        if( is_wp_error( $post_id ) ) {
            $response['code'] = $post_id->get_error_code();
            $response['message'] = $post_id->get_error_message();
            return new WP_REST_Response($response);
        }

        $response['success'] = true;
        $response['statusCode'] = 200;
        $response['code'] = 'wpcl_phone_call_registered';
        $response['message'] = 'Phone call was registered in database';
        $response['data'] = ['post_id'=>$post_id];

        return new WP_REST_Response($response);
        
    }

}