<?php

namespace GF_Twilio_OTP\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use GFAPI;
use GF_Twilio_OTP\Helper;

class Settings
{
    private static $options;

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'gf_twilio_otp_admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_settings_page'));
        add_action('admin_init', array($this, 'initialize_settings'));
    }

    public function gf_twilio_otp_admin_enqueue_scripts()
    {
        wp_enqueue_style('gf-twilio-otp-admin-css', plugin_dir_url(__FILE__) . '../assets/css/admin.css');
    }

    public static function display_settings_page()
    {
        self::$options = get_option('gf_twilio_otp_settings');
?>
        <div class="wrap gf_twilio_otp_settings">
            <h1>GF Twilio OTP Settings</h1>
            <form method="post" action="options.php" class="gf_twilio_settings_form">
                <?php
                settings_fields('gf_twilio_otp_settings_group');
                do_settings_sections('gf-twilio-otp');
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    public static function add_admin_settings_page()
    {
        add_options_page(
            'GF Twilio OTP Settings',
            'GF Twilio OTP',
            'manage_options',
            'gf-twilio-otp',
            array('GF_Twilio_OTP\Admin\Settings', 'display_settings_page')
        );
    }

    public static function initialize_settings()
    {
        register_setting(
            'gf_twilio_otp_settings_group',
            'gf_twilio_otp_settings',
            array('GF_Twilio_OTP\Admin\Settings', 'sanitize')
        );

        add_settings_section(
            'gf_twilio_otp_settings_section',
            'Twilio API Settings',
            null,
            'gf-twilio-otp'
        );

        add_settings_field(
            'twilio_account_sid',
            'Twilio Account SID',
            array('GF_Twilio_OTP\Admin\Settings', 'render_twilio_account_sid_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'twilio_auth_token',
            'Twilio Auth Token',
            array('GF_Twilio_OTP\Admin\Settings', 'render_twilio_auth_token_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'twilio_phone_number',
            'Twilio Phone Number',
            array('GF_Twilio_OTP\Admin\Settings', 'render_twilio_phone_number_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'selected_gravity_forms',
            'Select Forms for OTP',
            array('GF_Twilio_OTP\Admin\Settings', 'render_selected_gravity_forms_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_lifetime',
            'OTP Lifetime (minutes)',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_lifetime_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_resend_interval',
            'OTP Resend Interval (minutes)',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_resend_interval_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_max_tries',
            'OTP Max Tries',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_max_tries_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_length',
            'OTP Length',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_length_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_sent_success_message',
            'OTP Sent Success Message',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_sent_success_message_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_sent_failed_message',
            'OTP Sent Failed Message',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_sent_failed_message_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_verify_success_message',
            'OTP Verify Success Message',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_verify_success_message_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'otp_verify_failed_message',
            'OTP Verify Failed Message',
            array('GF_Twilio_OTP\Admin\Settings', 'render_otp_verify_failed_message_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );

        add_settings_field(
            'twilio_message_template',
            'Twilio Message Template',
            array('GF_Twilio_OTP\Admin\Settings', 'render_twilio_message_template_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );
        add_settings_field(
            'send_otp_button_text',
            'Send OTP Button Text',
            array('GF_Twilio_OTP\Admin\Settings', 'render_send_otp_button_text_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );
        add_settings_field(
            'send_otp_title_popup',
            'Send OTP Title Popup',
            array('GF_Twilio_OTP\Admin\Settings', 'render_send_otp_title_popup_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );
        add_settings_field(
            'send_otp_popup_button',
            'OTP Popup Button Text',
            array('GF_Twilio_OTP\Admin\Settings', 'render_send_otp_popup_button_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );
        add_settings_field(
            'enable_otp_button',
            'Disable the OTP Functionality',
            array('GF_Twilio_OTP\Admin\Settings', 'render_enable_otp_button_field'),
            'gf-twilio-otp',
            'gf_twilio_otp_settings_section'
        );
        
        
    }

    public static function sanitize($input)
    {
        $sanitized = array();

        $twilio_account_sid = Helper::encrypt(sanitize_text_field($input['twilio_account_sid'] ?? ''), 'twilio_account_sid');
        $twilio_auth_token = Helper::encrypt(sanitize_text_field($input['twilio_auth_token'] ?? ''), 'twilio_auth_token');
        $sanitized['twilio_account_sid'] = $twilio_account_sid;
        $sanitized['twilio_auth_token'] = $twilio_auth_token;

        $sanitized['twilio_phone_number'] = sanitize_text_field($input['twilio_phone_number'] ?? '');
        $sanitized['otp_lifetime'] = intval($input['otp_lifetime'] ?? 0);
        $sanitized['otp_resend_interval'] = intval($input['otp_resend_interval'] ?? 0);

        $sanitized['otp_sent_success_message'] = sanitize_text_field($input['otp_sent_success_message'] ?? '');
        $sanitized['otp_sent_failed_message'] = sanitize_text_field($input['otp_sent_failed_message'] ?? '');
        $sanitized['otp_verify_success_message'] = sanitize_text_field($input['otp_verify_success_message'] ?? '');
        $sanitized['otp_verify_failed_message'] = sanitize_text_field($input['otp_verify_failed_message'] ?? '');

        $sanitized['otp_max_tries'] = intval($input['otp_max_tries'] ?? 0);
        $sanitized['otp_length'] = intval($input['otp_length'] ?? 0);

        // Sanitize selected_gravity_forms array
        if (isset($input['selected_gravity_forms']) && is_array($input['selected_gravity_forms'])) {
            $sanitized['selected_gravity_forms'] = array_map(function ($form) {
                return [
                    'form_id' => sanitize_text_field($form['form_id'] ?? ''),
                    'phone_field_id' => sanitize_text_field($form['phone_field_id'] ?? '')
                ];
            }, $input['selected_gravity_forms']);
        } else {
            $sanitized['selected_gravity_forms'] = [];
        }

        $sanitized['twilio_message_template'] = sanitize_textarea_field($input['twilio_message_template'] ?? '');
        $sanitized['send_otp_button_text'] = sanitize_text_field($input['send_otp_button_text']?? '');
        $sanitized['send_otp_title_popup'] = sanitize_text_field($input['send_otp_title_popup']?? '');
        $sanitized['send_otp_popup_button'] = sanitize_text_field($input['send_otp_popup_button']?? '');
        $sanitized['enable_otp_button'] = isset($input['enable_otp_button']) ? '1' : '0';
        
        
        return $sanitized;
    }

    public static function render_twilio_account_sid_field()
    {
        $value = self::$options['twilio_account_sid'] ?? '';
        $decrypted_value = !empty($value) ? Helper::decrypt($value, 'twilio_account_sid') : '';
        echo "<input type='text' name='gf_twilio_otp_settings[twilio_account_sid]' value='" . esc_attr($decrypted_value) . "' style='width: 100%;max-width: 400px;'>";
    }

    public static function render_twilio_auth_token_field()
    {
        $value = self::$options['twilio_auth_token'] ?? '';
        $decrypted_value = !empty($value) ? Helper::decrypt($value, 'twilio_auth_token') : '';
        echo "<input type='password' name='gf_twilio_otp_settings[twilio_auth_token]' value='" . esc_attr($decrypted_value) . "' style='width: 100%;max-width: 400px;'>";
    }

    public static function render_twilio_phone_number_field()
    {
        $value = self::$options['twilio_phone_number'] ?? '';
        echo "<input type='text' name='gf_twilio_otp_settings[twilio_phone_number]' placeholder='+1234567787' value='" . esc_attr($value) . "'>";
    }

    public static function render_twilio_message_template_field()
    {
        $value = self::$options['twilio_message_template'] ?? '';
        $otp_life = isset(self::$options['otp_lifetime']) ? self::$options['otp_lifetime'] : 1;

        echo "<textarea name='gf_twilio_otp_settings[twilio_message_template]' rows='5' cols='100' style='width:100%'>" . esc_textarea($value) . "</textarea>";
        echo '<p class="text-sm text-gray-500 mt-2">
				Use the following placeholders in the message template:
				<br>
				<code>{otp}</code> - OTP code
                <br>
				<code>{otp_expires_in}</code> - OTP expires in. (example output: ' . $otp_life . ' minutes)
				<br>
				<code>{blog_name}</code> - Website Title
			</p>';
    }

    public static function render_otp_lifetime_field()
    {
        $value = isset(self::$options['otp_lifetime']) ? self::$options['otp_lifetime'] : '';
        echo "<input type='number' name='gf_twilio_otp_settings[otp_lifetime]' value='" . esc_attr($value) . "' min='1' step='1'>";
        echo "<p class='info'>Set the expiry time for otp here. OTP is only valid for above given time.</p>";
    }

    public static function render_otp_resend_interval_field()
    {
        $value = isset(self::$options['otp_resend_interval']) ? self::$options['otp_resend_interval'] : '';
        echo "<input type='number' name='gf_twilio_otp_settings[otp_resend_interval]' value='" . esc_attr($value) . "' min='1' step='1'>";
        echo "<p class='info'>Time interval for after what time you allow to resend the OTP again.</p>";
    }

    public static function render_otp_sent_success_message_field()
    {
        $value = self::$options['otp_sent_success_message'] ?? '';
        echo "<input type='text' name='gf_twilio_otp_settings[otp_sent_success_message]' value='" . esc_attr($value) . "' style='width: 100%;'>";
    }

    public static function render_otp_sent_failed_message_field()
    {
        $value = self::$options['otp_sent_failed_message'] ?? '';
        echo "<input type='text' name='gf_twilio_otp_settings[otp_sent_failed_message]' value='" . esc_attr($value) . "' style='width: 100%;'>";
    }

    public static function render_otp_verify_success_message_field()
    {
        $value = self::$options['otp_verify_success_message'] ?? '';
        echo "<input type='text' name='gf_twilio_otp_settings[otp_verify_success_message]' value='" . esc_attr($value) . "' style='width: 100%;'>";
    }

    public static function render_otp_verify_failed_message_field()
    {
        $value = self::$options['otp_verify_failed_message'] ?? '';
        echo "<input type='text' name='gf_twilio_otp_settings[otp_verify_failed_message]' value='" . esc_attr($value) . "' style='width: 100%;'>";
    }
    public static function render_send_otp_button_text_field() {
        $options = get_option('gf_twilio_otp_settings');
        $button_text = isset($options['send_otp_button_text']) ? esc_attr($options['send_otp_button_text']) : 'Send OTP';
        echo '<input type="text" name="gf_twilio_otp_settings[send_otp_button_text]" value="' . $button_text . '" class="regular-text">';
    }
    public static function render_send_otp_title_popup_field() {
        $options = get_option('gf_twilio_otp_settings');
        $button_text = isset($options['send_otp_title_popup']) ? esc_attr($options['send_otp_title_popup']) : 'Enter OTP';
        echo '<input type="text" name="gf_twilio_otp_settings[send_otp_title_popup]" value="' . $button_text . '" class="regular-text">';
    }
    public static function render_send_otp_popup_button_field() {
        $options = get_option('gf_twilio_otp_settings');
        $button_text = isset($options['send_otp_popup_button']) ? esc_attr($options['send_otp_popup_button']) : 'Verify OTP';
        echo '<input type="text" name="gf_twilio_otp_settings[send_otp_popup_button]" value="' . $button_text . '" class="regular-text">';
    }
    
    public static function render_otp_max_tries_field()
    {
        $value = isset(self::$options['otp_max_tries']) ? self::$options['otp_max_tries'] : '';
        echo "<input type='number' name='gf_twilio_otp_settings[otp_max_tries]' value='" . esc_attr($value) . "' min='1' step='1'>";
    }

    public static function render_otp_length_field()
    {
        $value = isset(self::$options['otp_length']) ? self::$options['otp_length'] : '';
        echo "<input type='number' name='gf_twilio_otp_settings[otp_length]' value='" . esc_attr($value) . "' min='1' step='1'>";
    }
    public static function render_enable_otp_button_field() {
        $options = get_option('gf_twilio_otp_settings', []);
        $checked = isset($options['enable_otp_button']) && $options['enable_otp_button'] == '1' ? 'checked' : '';
        
        echo '<input type="checkbox" id="enable_otp_button" name="gf_twilio_otp_settings[enable_otp_button]" value="1" ' . $checked . '>';
    }
    
    
    public static function render_selected_gravity_forms_field()
    {
        $selected_forms = self::$options['selected_gravity_forms'] ?? [];
        $gravity_forms = GFAPI::get_forms();

        echo '<div class="gf-twilio-otp-repeater">';

        if (!empty($selected_forms) && is_array($selected_forms)) {
            foreach ($selected_forms as $index => $form_data) {
                $form_id = isset($form_data['form_id']) ? esc_attr($form_data['form_id']) : '';
                $phone_field_id = isset($form_data['phone_field_id']) ? esc_attr($form_data['phone_field_id']) : '';

                // Add data attribute to store the phone field ID for this item
                echo '<div class="gf-twilio-otp-repeater-item" data-phone-field-id="' . $phone_field_id . '">';

                // Form selection dropdown
                echo '<label for="form_select_' . $index . '">Form:</label>';
                echo '<select name="gf_twilio_otp_settings[selected_gravity_forms][' . $index . '][form_id]" id="form_select_' . $index . '" class="form-select">';
                echo '<option value="">Select a form</option>';

                foreach ($gravity_forms as $form) {
                    $selected = ($form_id === (string)$form['id']) ? 'selected="selected"' : '';
                    echo '<option value="' . esc_attr($form['id']) . '" ' . $selected . '>' .
                        esc_html($form['title']) . '</option>';
                }
                echo '</select>';

                // Phone field dropdown
                echo '<label for="phone_field_' . $index . '">Phone Field:</label>';
                echo '<select name="gf_twilio_otp_settings[selected_gravity_forms][' . $index . '][phone_field_id]" id="phone_field_' . $index . '">';
                echo '<option value="">Select a field</option>';

                $selected_form = array_filter($gravity_forms, fn($form) => (string)$form['id'] === $form_id);
                $selected_form = reset($selected_form) ?: null;

                if ($selected_form && !empty($selected_form['fields'])) {
                    foreach ($selected_form['fields'] as $field) {
                        $field_id = (string)$field->id;
                        $selected = ($phone_field_id === $field_id) ? 'selected="selected"' : '';
                        echo '<option value="' . esc_attr($field_id) . '" ' . $selected . '>' .
                            esc_html($field->label) . ' (ID: ' . $field_id . ')' . '</option>';
                    }
                }
                echo '</select>';

                echo '<button type="button" class="gf-twilio-otp-remove-field button button-danger">Remove</button>';
                echo '</div>';
            }
        } else {
            echo '<p class="info">No forms selected yet.</p>';
        }
        echo '</div>';

        echo '<button type="button" class="gf-twilio-otp-add-field button">Add Form</button>';

    ?>
        <script>
            jQuery(document).ready(function($) {
                const formsData = <?php echo json_encode(array_map(function ($form) {
                                        return [
                                            'id' => (string)$form['id'],
                                            'title' => $form['title'],
                                            'fields' => array_map(function ($field) {
                                                return [
                                                    'id' => (string)$field->id,
                                                    'label' => $field->label
                                                ];
                                            }, $form['fields'])
                                        ];
                                    }, $gravity_forms)); ?>;

                // Add new repeater item
                $('.gf-twilio-otp-add-field').on('click', function() {
                    const index = $('.gf-twilio-otp-repeater-item').length;
                    const template = `
                        <div class="gf-twilio-otp-repeater-item">
                            <label for="form_select_${index}">Form:</label>
                            <select name="gf_twilio_otp_settings[selected_gravity_forms][${index}][form_id]" 
                                    class="form-select" id="form_select_${index}">
                                <option value="">Select a form</option>
                                ${formsData.map(form => 
                                    `<option value="${form.id}">${form.title}</option>`).join('')}
                            </select>
                            <label for="phone_field_${index}">Phone Field:</label>
                            <select name="gf_twilio_otp_settings[selected_gravity_forms][${index}][phone_field_id]" 
                                    id="phone_field_${index}">
                                <option value="">Select a field</option>
                            </select>
                            <button type="button" class="gf-twilio-otp-remove-field button button-danger">Remove</button>
                        </div>
                    `;

                    if (index === 0) {
                        $('.gf-twilio-otp-repeater').empty();
                    }
                    $('.gf-twilio-otp-repeater').append(template);
                });

                // Remove repeater item
                $(document).on('click', '.gf-twilio-otp-remove-field', function() {
                    $(this).closest('.gf-twilio-otp-repeater-item').remove();
                    if ($('.gf-twilio-otp-repeater-item').length === 0) {
                        $('.gf-twilio-otp-repeater').html('<p class="info">No forms selected yet.</p>');
                    }
                });

                // Dynamic phone field population
                $(document).on('change', '.form-select', function() {
                    const $select = $(this);
                    const formId = $select.val();
                    const $phoneSelect = $select.closest('.gf-twilio-otp-repeater-item')
                        .find('select[id^="phone_field_"]');

                    $phoneSelect.empty().append('<option value="">Select a field</option>');

                    if (formId) {
                        const selectedForm = formsData.find(form => form.id === formId);
                        if (selectedForm && selectedForm.fields) {
                            selectedForm.fields.forEach(field => {
                                $phoneSelect.append(
                                    `<option value="${field.id}">${field.label} (ID: ${field.id})</option>`
                                );
                            });
                        }
                    }
                });

                // Trigger change event on page load to populate existing phone fields
                $('.form-select').each(function() {
                    const $select = $(this);
                    $select.trigger('change'); // Populate the phone field options
                    const $repeaterItem = $select.closest('.gf-twilio-otp-repeater-item');
                    const savedPhoneId = $repeaterItem.data('phone-field-id');
                    if (savedPhoneId) {
                        const $phoneSelect = $repeaterItem.find('select[id^="phone_field_"]');
                        $phoneSelect.val(savedPhoneId); // Set the saved phone field value
                    }
                });
            });
        </script>
<?php
    }
}
