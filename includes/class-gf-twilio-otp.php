<?php

namespace GF_Twilio_OTP;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use GF_Twilio_OTP\Admin\Settings;
use GF_Twilio_OTP\GForm_Modifications;
use GF_Twilio_OTP\OTP;

class GF_Twilio_OTP
{
    private static $instance = null;

    private function __construct()
    {
        $this->load_dependencies();

        if (is_admin()) {
            new Settings();
        }
        
        $gform_modifications = new GForm_Modifications();
        $gform_modifications->init();

        new OTP();
    }

    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load_dependencies()
    {
        require_once GF_TWILIO_OTP_PATH . 'includes/class-admin-settings.php';
        require_once GF_TWILIO_OTP_PATH . 'includes/class-helper.php';
        require_once GF_TWILIO_OTP_PATH . 'includes/class-otp.php';
        require_once GF_TWILIO_OTP_PATH . 'includes/class-gform-modifications.php';
    }
}
