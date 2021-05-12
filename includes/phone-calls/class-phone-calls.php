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
     * Adds phone call to wp_posts table
     * 
     * Creates new post of pkdevpl_phone_call post type and stores it's metadata.
     * 
     * @param string $incoming_number   Caller Phone ID
     * @param int|string $device        Int representing post_id or string containing device_api_key
     * 
     * @return int|WP_Error Returns post_id of created phone call post or WP_Error
     */
    
    function register_phone_call( $incoming_number, $device_id ) {
        
        $number_from = $this->format_phone_number($incoming_number);
        if(is_wp_error( $number_from )) {
            return new WP_Error('wpcl_invalid_incoming_number', 'Incoming number is not a valid number');
        }

        // Get device by post_id or device_api_key

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
                'pkdevpl_phone_call_device_wp_id'=>$device->ID,
                'pkdevpl_phone_call_time'=>time()
            ],
        ];
        
        $post_id = wp_insert_post( $postarr, true );

        if( is_wp_error($post_id) ) return $post_id;

        return $post_id;

    }

    /**
     * Accepts phone number in any format and returns formatted or WP_Error.
     * 
     * @param string $phone_number  Phone number string in any format.
     * @param string|null $format   Optional. Can be 'add-spaces' or null.
     
     * @return string String in E164 format (+48789123456) or (+48 789 123 456)
     */

    function format_phone_number($phone_number, $format = null) {
        
        $phone_util = PhoneNumberUtil::getInstance();
        if( 'add-spaces' === $format ) {
            $format = PhoneNumberFormat::INTERNATIONAL;
        } else {
            $format = PhoneNumberFormat::E164;
        }

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