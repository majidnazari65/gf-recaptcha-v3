<?php
/*
Plugin Name: Gravity Forms reCAPTCHA v3
Description: Adds Google reCAPTCHA v3 validation to Gravity Forms.
Version: 1.0
Author: Aniltarah
Author URI: https://aniltarah.com
Text Domain: gf-recaptcha-v3
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// check if gravityforms is enabled
if ( ! class_exists( 'GFCommon' ) ) {
    return;
}


add_action( 'gform_loaded', 'grv3_load_plugin', 5 );
function grv3_load_plugin() {

    define( 'GRV3_SETTINGS_SLUG', 'grv3-settings' );
    define( 'GRV3_OPTION_GROUP', 'grv3_option_group' );
    define( 'GRV3_OPTION_NAME', 'grv3_settings' );
    define( 'GRV3_DEFAULT_THRESHOLD', 0.5 );

    load_plugin_textdomain( 'gf-recaptcha-v3', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


    // settings (for options.php)
    add_action( 'admin_init', 'grv3_settings_init' );

    // enqueue scripts
    add_action( 'gform_enqueue_scripts', 'grv3_enqueue_scripts', 10, 2 );

    // add recaptcha hidden field
    add_action( 'gform_form_tag', 'grv3_add_hidden_field', 10, 2 );

    // recaptcha validation
    add_filter( 'gform_validation', 'grv3_validate_captcha' );

    // gravityforms form setting
    add_filter( 'gform_form_settings_fields', 'grv3_add_form_setting_field', 10, 2 );
    add_filter( 'gform_pre_save_form_settings', 'grv3_save_form_setting', 10, 2 );

    // add settings to gravityforms setting page
    add_filter( 'gform_settings_menu', 'grv3_add_settings_tab', 10, 1 );
    add_action( 'gform_settings_' . GRV3_SETTINGS_SLUG, 'grv3_render_settings_page_content' );

} 


/**
 * settings (for options.php)
 */

// register settings 
function grv3_settings_init() {
    register_setting( GRV3_OPTION_GROUP, GRV3_OPTION_NAME );

    add_settings_section(
        'grv3_general_section',
        __( 'Google reCAPTCHA v3 Keys', 'gf-recaptcha-v3' ),
        null,
        GRV3_SETTINGS_SLUG
    );

    add_settings_field(
        'grv3_site_key',
        __( 'Site Key', 'gf-recaptcha-v3' ),
        'grv3_field_callback',
        GRV3_SETTINGS_SLUG,
        'grv3_general_section',
        ['id' => 'grv3_site_key', 'label' => __( 'Site Key', 'gf-recaptcha-v3' )] 
    );

    add_settings_field(
        'grv3_secret_key',
        __( 'Secret Key', 'gf-recaptcha-v3' ),
        'grv3_field_callback',
        GRV3_SETTINGS_SLUG,
        'grv3_general_section',
        ['id' => 'grv3_secret_key', 'label' => __( 'Secret Key', 'gf-recaptcha-v3' )] 
    );
    
    add_settings_field(
        'grv3_threshold',
        __( 'Score Threshold', 'gf-recaptcha-v3' ), 
        'grv3_field_callback',
        GRV3_SETTINGS_SLUG,
        'grv3_general_section',
        ['id' => 'grv3_threshold', 'label' => __( 'Minimum score (e.g., 0.5)', 'gf-recaptcha-v3' ), 'type' => 'number'] 
    );
}

// render settings fields
function grv3_field_callback( $args ) {
    $options = get_option( GRV3_OPTION_NAME );
    $value = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
    $type = isset( $args['type'] ) ? $args['type'] : 'text';
    
    if ($type === 'number') {
        $value = $value ? $value : GRV3_DEFAULT_THRESHOLD;
        echo '<input type="' . $type . '" id="' . $args['id'] . '" name="' . GRV3_OPTION_NAME . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" step="0.1" min="0" max="1" style="width: 100px;">';
        echo '<p class="description">' . esc_html__( $args['label'], 'gf-recaptcha-v3' ) . '. ' . esc_html__( 'Google recommends 0.5 as a default.', 'gf-recaptcha-v3' ) . '</p>';
    } else {
        echo '<input type="' . $type . '" id="' . $args['id'] . '" name="' . GRV3_OPTION_NAME . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__( $args['label'], 'gf-recaptcha-v3' ) . '</p>';
    }
}

// render options page
function grv3_render_settings_fields() {
    settings_fields( GRV3_OPTION_GROUP );
    do_settings_sections( GRV3_SETTINGS_SLUG );
}

/**
 * enqueue fornt scripts
 */
function grv3_enqueue_scripts( $form, $is_ajax ) {
    static $scripts_added = false;

    if ( ! grv3_is_recaptcha_enabled( $form ) ) {
        return;
    }

    if ( $scripts_added ) {
        return;
    }

    $options = get_option( GRV3_OPTION_NAME );
    $site_key = $options['grv3_site_key'];

    wp_enqueue_script(
        'google-recaptcha',
        'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $site_key ),
        [],
        null,
        true
    );

    wp_enqueue_script(
        'grv3-main-js',
        plugin_dir_url( __FILE__ ) . 'js/main.js',
        ['jquery', 'google-recaptcha'],
        '1.0',
        true
    );

    $inline_script = "const grv3_site_key = '" . esc_js( $site_key ) . "';";
    wp_add_inline_script( 'grv3-main-js', $inline_script, 'before' );

    $scripts_added = true;
}

/**
 * add recaptcha hidden filed
 */
function grv3_add_hidden_field( $form_tag, $form ) {

    if ( ! grv3_is_recaptcha_enabled( $form ) ) {
        return $form_tag;
    }
    
    $field_id = 'g-recaptcha-response-' . $form['id'];
    $hidden_field = '<input type="hidden" name="g-recaptcha-response" id="' . esc_attr( $field_id ) . '">';
    
    return $form_tag . $hidden_field;
}

/**
 * recaptcha validation
 */
function grv3_validate_captcha( $validation_result ) {
    $form = $validation_result['form'];

    if ( ! grv3_is_recaptcha_enabled( $form ) ) {
        return $validation_result;
    }

    $options = get_option( GRV3_OPTION_NAME );
    
    $token = rgpost( 'g-recaptcha-response' );
    if ( empty( $token ) ) {
        $validation_result['is_valid'] = false;
        grv3_set_validation_error( $validation_result, $form, __( 'reCAPTCHA Error: Token not found.', 'gf-recaptcha-v3' ) );
        return $validation_result;
    }

    $secret_key = $options['grv3_secret_key'];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $args = [
        'body' => [
            'secret'   => $secret_key,
            'response' => $token,
            'remoteip' => GFFormsModel::get_ip()
        ]
    ];

    $response = wp_remote_post( $url, $args );

    if ( is_wp_error( $response ) ) {
        $validation_result['is_valid'] = false;
        grv3_set_validation_error( $validation_result, $form, __( 'Error connecting to reCAPTCHA server.', 'gf-recaptcha-v3' ) );
        return $validation_result;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    $threshold = isset( $options['grv3_threshold'] ) && is_numeric($options['grv3_threshold']) ? (float) $options['grv3_threshold'] : GRV3_DEFAULT_THRESHOLD;

    if ( ! $body['success'] || $body['score'] < $threshold || $body['action'] !== 'gravity_form_submit' ) {
        $validation_result['is_valid'] = false;
        $error_message = __( 'reCAPTCHA validation failed. Please try again.', 'gf-recaptcha-v3' );
        grv3_set_validation_error( $validation_result, $form, $error_message );
    }

    return $validation_result;
}

// helper function for show validation message
function grv3_set_validation_error( &$validation_result, $form, $message ) {
    foreach ( $form['fields'] as &$field ) {
        if ( $field->type != 'hidden' && $field->type != 'html' ) {
            $field->failed_validation = true;
            $field->validation_message = $message;
            break;
        }
    }
}

/**
 * helper function for check if recaptcha v3 is enabled for form
 */
function grv3_is_recaptcha_enabled( $form ) {
    $options = get_option( GRV3_OPTION_NAME );
    if ( empty( $options['grv3_site_key'] ) || empty( $options['grv3_secret_key'] ) ) {
        return false;
    }

    $setting = rgar( $form, 'grv3_enabled' );

    if ( $setting === '0' ) {
        return false;
    }
    
    if ($setting==''){
        return false;
    }

    return true;
}

/**
 * add fields to form settings page
 */
function grv3_add_form_setting_field( $fields, $form ) {
    
    $fields['grv3_settings'] = [
        'title'  => __( 'reCAPTCHA v3 Settings', 'gf-recaptcha-v3' ),
        'fields' => [
            [
                'name'    => 'grv3_enabled',
                'label'   => __( 'Enable reCAPTCHA v3', 'gf-recaptcha-v3' ),
                'type'    => 'checkbox',
                'choices' => [
                    [
                        'name'  => 'grv3_enabled',
                        'label' => __( 'Enable reCAPTCHA v3 for this form', 'gf-recaptcha-v3' ),
                        'value' => '1',
                    ],
                ],
                'tooltip' => __( 'Adds Google reCAPTCHA v3 validation to this form. This is enabled by default for all forms unless explicitly disabled.', 'gf-recaptcha-v3' ),
            ],
        ],
    ];
    
    return $fields;
}

function grv3_save_form_setting( $settings, $form ) {
    if ( ! isset( $settings['grv3_enabled'] ) ) {
        $settings['grv3_enabled'] = '0';
    }
    return $settings;
}


/**
 * add gravityforms settings tab
 */
function grv3_add_settings_tab( $tabs ) {
    
    $tabs[] = [
        'name'  => GRV3_SETTINGS_SLUG,
        'label' => __( 'reCAPTCHA v3', 'gf-recaptcha-v3' )
    ];
    return $tabs;
}

// add setting forms to new tab
function grv3_render_settings_page_content() {
        
    ?>
    <form action="options.php" method="post">
        <?php
        grv3_render_settings_fields();
        
        ?>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'gf-recaptcha-v3' ); ?>" />
        </p>
    </form>
    <?php
    
}
