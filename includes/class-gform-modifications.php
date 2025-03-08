<?php

namespace GF_Twilio_OTP;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use DOMDocument;

class GForm_Modifications {

    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_popup_html'));
        add_filter('gform_submit_button', array($this, 'add_send_otp_button'), 10, 2);
    }

    public function enqueue_scripts() {
        $options = get_option('gf_twilio_otp_settings');
        wp_enqueue_style('gf-twilio-otp-css', plugin_dir_url(dirname(__FILE__)) . 'assets/css/style.css');
        wp_enqueue_script('gf-twilio-otp-js', plugin_dir_url(dirname(__FILE__)) . 'assets/js/gf-twilio-otp.js', array('jquery'), '1.0.0', true);
        wp_localize_script('gf-twilio-otp-js', 'gf_twilio_otp', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gf_send_otp'),
            'forms' => isset($options['selected_gravity_forms']) ? $options['selected_gravity_forms'] : array(),
            'resend_interval' => (isset($options['otp_resend_interval']) ? $options['otp_resend_interval'] : 0)
        ));

        wp_enqueue_style('gf-twilio-otp-intl-tel-input-css', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css');
        wp_enqueue_script('gf-twilio-otp-intl-tel-input-js', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js', array('jquery'), '1.0.0', true);
    }

    public function add_popup_html() {
        $options = get_option('gf_twilio_otp_settings');
        $selected_forms = isset($options['selected_gravity_forms']) ? $options['selected_gravity_forms'] : array();
        
        if (!empty($selected_forms)) {
            foreach ($selected_forms as $form_id) {
                if (has_shortcode(get_post()->post_content, 'gravityform')) {
                    ?>
                    <div id="gf_otp_popup" style="display: none;">
                        <div class="gf_otp_popup_content">
                            <?php
                            $title_text = isset($options['send_otp_title_popup']) ? esc_html($options['send_otp_title_popup']) : 'Enter OTP';
                            $button_text = isset($options['send_otp_popup_button']) ? esc_html($options['send_otp_popup_button']) : 'Verify OTP';
                            ?>
                            <label for="gf_otp_input"><?php echo $title_text; ?></label>
                            <input type="text" id="gf_otp_input" name="gf_otp_input">
                            <button type="button" id="gf_verify_otp_button"><?php echo $button_text; ?></button>
                            <div id="gf_otp_timer" style="margin-top: 10px; font-size: 14px;"></div>
                            <p class="info resend-wrap">Didn't you received OTP? <a href="#" class="resend-otp">Resend</a></p>
                            <div id="gf_otp_error" style="color: red; display: none;"></div>
                            <div id="gf_otp_message" style="color: green; display: none;"></div>
                        </div>
                    </div>
                    <?php
                    break;
                }
            }
        }
    }
    /*
    public function add_send_otp_button($button, $form) {
        $options = get_option('gf_twilio_otp_settings');
        $selected_forms = isset($options['selected_gravity_forms']) ? $options['selected_gravity_forms'] : array();

        if (in_array($form['id'], $selected_forms)) {
            $send_otp_button = '<input type="submit" id="gf_send_otp_button_' . esc_attr($form['id']) . '" class="button g_send_otp_button" value="Send OTP">';
            
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $button);
            $input = $dom->getElementsByTagName('input')->item(0);
            $classes = $input->getAttribute('class');
            $classes .= " gf_twilio_otp_hidden";
            $input->setAttribute('class', $classes);
            return $send_otp_button . $dom->saveHtml($input);
        }

        return $button;
    }*/

    public function add_send_otp_button($button, $form) {
        $options = get_option('gf_twilio_otp_settings');
        $enable_otp_button = isset($options['enable_otp_button']) && $options['enable_otp_button'] == '1';
    
        try {
            $selected_forms = isset($options['selected_gravity_forms']) ? $options['selected_gravity_forms'] : array();
            
            // Check if the current form ID exists in the selected forms
            $form_selected = array_filter($selected_forms, function($selected_form) use ($form) {
                return isset($selected_form['form_id']) && $selected_form['form_id'] == $form['id'];
            });
    
            if (!empty($form_selected)) {
                $otp_button_class = $enable_otp_button ? 'gf_twilio_otp_hidden' : '';
                $submit_button_class = $enable_otp_button ? '' : 'gf_twilio_otp_hidden';
    
                // Add OTP button
                $send_otp_button = '<input type="submit" id="gf_send_otp_button_' . esc_attr($form['id']) . '" class="button g_send_otp_button ' . esc_attr($otp_button_class) . '" value="Send OTP">';
    
                // Modify existing submit button
                $dom = new DOMDocument();
                libxml_use_internal_errors(true); // Prevents HTML parsing errors
                $dom->loadHTML('<?xml encoding="utf-8" ?>' . $button);
                libxml_clear_errors();
    
                $input = $dom->getElementsByTagName('input')->item(0);
                if ($input) {
                    $classes = $input->getAttribute('class');
                    $classes .= " " . esc_attr($submit_button_class);
                    $input->setAttribute('class', trim($classes));
                }
    
                // JavaScript to ensure proper button toggling on multi-page forms
                $script = "<script>
                    (function($) {
                        $(document).on('gform_page_loaded', function(event, form_id, current_page) {
                            if(form_id == " . esc_js($form['id']) . ") {
                                var enableOtpButton = " . json_encode($enable_otp_button) . ";
    
                                if (enableOtpButton) {
                                    $('.g_send_otp_button').hide();
                                    $('.gform_button').removeClass('gf_twilio_otp_hidden');
                                } else {
                                    $('.g_send_otp_button').show();
                                    $('.gform_button').addClass('gf_twilio_otp_hidden');
                                }
                            }
                        });
                    })(jQuery);
                </script>";
    
                return $send_otp_button . $dom->saveHtml($input) . $script;
            }
        } catch (\Exception $e) {
            error_log('Error Adding Button: ' . $e->getMessage());
        }
        return $button;
    }
    
}    
