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
    }
}
