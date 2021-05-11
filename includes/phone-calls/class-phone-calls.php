<?php

namespace pkdevpl\wpcallslog;

use \libphonenumber\PhoneNumberUtil;
use \libphonenumber\PhoneNumberFormat;

use WP_Error;

/**
 * Manages functions releated to pkdevpl_phone_calls post type
 */

class Phone_Calls {

    /**
     * Adds phone call as a WP_Post to wp_posts table
     * 
     * @param   $incoming_number    string  Caller Phone ID
     * @param   $receiving_number   string  Caller ID of phone receiving call (in case of dual-sim)
     * @param   $device             post_id | api_key
     * 
     * @return  int|WP_Error    Returns new post_id or WP_Error
     */
    
    function register_phone_call( $incoming_number, $receiving_number, $device_id ) {
        
        $number_from = $this->format_phone_number($incoming_number);
        if(is_wp_error( $number_from )) {
            return new WP_Error('wpcl_invalid_incoming_number', 'Incoming number is not a valid number');
        }

        $number_to = $this->format_phone_number($receiving_number);
        if(is_wp_error( $number_to )) {
            return new WP_Error('wpcl_invalid_receiving_number', 'Receiving number is not a valid number');
        }

        $devices = new Devices;
        if( is_int( $device_id ) || $device_id instanceof WP_Post ) {
            $device = $devices->get_device($device_id);
        } else {
            $device = $devices->get_device_by_api_key($device_id);
        }

        if(is_wp_error($device)) return $device;
        if(is_null($device)) return new WP_Error('wpcl_no_device_found', 'No device was found with provided ID / api_key');

        $postarr = [
            'post_type'=>'pkdevpl_phone_calls',
            'post_status'=>'publish',
            'post_title'=>'Automatycznie zapisany szkic',
            'post_content'=>'empty',
            'meta_input'=>[
                'pkdevpl_phone_call_incoming_number'=>$number_from,
                'pkdevpl_phone_call_receiving_number'=>$number_to,
                'pkdevpl_phone_call_device_wp_id'=>$device->ID,
                'pkdevpl_phone_call_time'=>current_time('timestamp'),
            ],
        ];
        
        $post_id = wp_insert_post( $postarr, true );

        if( is_wp_error($post_id) ) return $post_id;

        return $post_id;

    }

    /**
     * Accepts phone number string and returns formatted string ex. +48789123456
     * 
     * @param   $phone_number   string  Phone number string
     * @return  string  ex. +48789123456
     */

    function format_phone_number($phone_number) {
        
        $phone_util = PhoneNumberUtil::getInstance();
        $format = PhoneNumberFormat::E164;

        try {
            $phone_base_number = $phone_util->parse($phone_number, 'PL');
            if($phone_util->isValidNumber($phone_base_number)) {
                $phone_formatted = $phone_util->format($phone_base_number, $format);
                return $phone_formatted;
            } else {
                return new WP_Error('wpcl_invalid_phone_number', 'Provided phone number is invalid');
            }
        } catch(\libphonenumber\NumberParseException $e) {
            return new WP_Error('wpcl_libphonenumber_parse_error', 'There was a problem parsing phone number');
        }
    }

}