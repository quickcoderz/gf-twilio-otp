<?php

/**
 * Plugin Name: Gravity Forms Add-on - Twilio OTP
 * Description: Seamlessly integrates Twilio SMS OTP functionality with Gravity Forms, enabling customizable message templates and flexible timing configurations for enhanced security and user verification.
 * Version: 1.0.0
 * Author: Quickcoderz
 * Author URL: https://quickcoderz.com
 * Text Domain: gf-twilio-otp
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('GF_TWILIO_OTP_PATH', plugin_dir_path(__FILE__));

require_once GF_TWILIO_OTP_PATH . 'includes/class-gf-twilio-otp.php';

\GF_Twilio_OTP\GF_Twilio_OTP::instance();

