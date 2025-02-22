<?php

namespace GF_Twilio_OTP;

if (!defined('ABSPATH')) {
    exit; // Exit if direct Access.
}

require_once GF_TWILIO_OTP_PATH . '/twilio-lib/src/Twilio/autoload.php';

use Twilio\Rest\Client;

class OTP
{
    public static $options = [];

    public function __construct()
    {
        add_action('wp_ajax_gf_send_otp', array($this, 'send_otp'));
        add_action('wp_ajax_nopriv_gf_send_otp', array($this, 'send_otp'));
        add_action('wp_ajax_gf_verify_otp', array($this, 'verify_otp'));
        add_action('wp_ajax_nopriv_gf_verify_otp', array($this, 'verify_otp'));
    }

    public function send_otp()
    {
        check_ajax_referer('gf_send_otp', 'security');

        try {

            $options = get_option('gf_twilio_otp_settings');


            $account_sid = isset($options['twilio_account_sid']) ? Helper::decrypt($options['twilio_account_sid'], 'twilio_account_sid') : '';
            $auth_token = isset($options['twilio_auth_token']) ? Helper::decrypt($options['twilio_auth_token'], 'twilio_auth_token') : '';

            $otp_sent_message = isset($options['otp_sent_success_message']) ? $options['otp_sent_success_message'] : '';
            $otp_failed_message = isset($options['otp_sent_failed_message']) ? $options['otp_sent_failed_message'] : '';
            $raw_message = isset($options['twilio_message_template']) ? $options['twilio_message_template'] : '';

            $otp_length = isset($options['otp_length']) ? $options['otp_length'] : 6;
            $otp_lifetime = isset($options['otp_lifetime']) ? $options['otp_lifetime'] : 5;
            
            if (!empty($account_sid) && !empty($auth_token)) {
                // Gather Data
                $phone_number = sanitize_text_field($_POST['phone_number']);
                $otp = self::generate_otp($otp_length);
                $message = self::get_message($raw_message, $otp, $otp_lifetime);

                // Store OTP in a transient
                $otp_transient_key = 'gf_otp_' . md5($phone_number);
                $otp_lifetime = intval($otp_lifetime) * 60;
                set_transient($otp_transient_key, $otp, $otp_lifetime);

                // Send OTP Message
                $client = new Client($account_sid, $auth_token);
                $client->messages->create(
                    $phone_number,
                    [
                        'from' => $options['twilio_phone_number'],
                        'body' => $message
                    ]
                );

                wp_send_json_success($otp_sent_message);
            } else {
                wp_send_json_error('Twilio not configured.');
            }
        } catch (\Exception $e) {
            // wp_send_json_error('Error: ' . $e->getMessage());
            wp_send_json_error($otp_failed_message);
        }
    }

    public function verify_otp()
    {
        // check_ajax_referer('gf_verify_otp', 'security');
        $options = get_option('gf_twilio_otp_settings');
        $otp_verify_success_message = isset($options['otp_verify_success_message']) ? $options['otp_verify_success_message'] : '';
        $otp_verify_failed_message = isset($options['otp_verify_failed_message']) ? $options['otp_verify_failed_message'] : '';

        $submitted_otp = sanitize_text_field($_POST['otp']);
        $phone_number = sanitize_text_field($_POST['phone_number']);
        $otp_transient_key = 'gf_otp_' . md5($phone_number);

        $stored_otp = get_transient($otp_transient_key);

        if ($submitted_otp === $stored_otp) {
            delete_transient($otp_transient_key); 
            wp_send_json_success($otp_verify_success_message);
        } else {
            wp_send_json_error($otp_verify_failed_message);
        }
    }

    public static function generate_otp($length = 6)
    {
        $pool = range(0, 9);
        $otp = "";

        for ($i = 0; $i < $length; $i++) {
            $key = random_int(0, count($pool) - 1);
            $otp .= $pool[$key];
        }

        return $otp;
    }

    public static function get_message($raw_message = '', $otp = '', $otp_expiry = '')
    {
        $message = '';
        $blog_name = get_bloginfo('name');
        $otp_expires_in = intval($otp_expiry) > 1 ? $otp_expiry .' minutes': $otp_expiry . 'minute';

        $placeholders = array(
            '{otp}' => $otp,
            '{otp_expires_in}' => $otp_expires_in,
            '{blog_name}' => $blog_name,
        );

        $message = str_replace(array_keys($placeholders), array_values($placeholders), nl2br($raw_message));

        return $message;
    }
}
