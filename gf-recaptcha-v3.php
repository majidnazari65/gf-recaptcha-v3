<?php
/*
Plugin Name: reCAPTCHA v3 For Gravity Forms
Description: Adds Google reCAPTCHA v3 validation to Gravity Forms.
Version: 1.0
Author: Aniltarah
Author URI: https://aniltarah.com
Text Domain: gf-recaptcha-v3
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if Gravity Forms is active
if ( ! class_exists( 'GFCommon' ) ) {
	return;
}

class GF_Recaptcha_V3 {

	private static $instance = null;
	const SETTINGS_SLUG = 'grv3-settings';
	const OPTION_GROUP  = 'grv3_option_group';
	const OPTION_NAME   = 'grv3_settings';
	const DEFAULT_THRESHOLD = 0.5;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'gform_loaded', [ $this, 'init' ], 5 );
	}

	public function init() {
		//load_plugin_textdomain( 'gf-recaptcha-v3', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Settings
		add_action( 'admin_init', [ $this, 'settings_init' ] );
		add_filter( 'gform_settings_menu', [ $this, 'add_settings_tab' ], 10, 1 );
		add_action( 'gform_settings_' . self::SETTINGS_SLUG, [ $this, 'render_settings_page_content' ] );

		// Frontend
		add_action( 'gform_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10, 2 );
		add_action( 'gform_form_tag', [ $this, 'add_hidden_field' ], 10, 2 );

		// Validation
		add_filter( 'gform_validation', [ $this, 'validate_captcha' ] );

		// Form Specific Settings
		add_filter( 'gform_form_settings_fields', [ $this, 'add_form_setting_field' ], 10, 2 );
		add_filter( 'gform_pre_save_form_settings', [ $this, 'save_form_setting' ], 10, 2 );
	}

	public function settings_init() {
		register_setting( self::OPTION_GROUP, self::OPTION_NAME, [ $this, 'sanitize_settings' ] );

		add_settings_section(
			'grv3_general_section',
			__( 'Google reCAPTCHA v3 Keys', 'gf-recaptcha-v3' ),
			null,
			self::SETTINGS_SLUG
		);

		add_settings_field(
			'grv3_site_key',
			__( 'Site Key', 'gf-recaptcha-v3' ),
			[ $this, 'field_callback' ],
			self::SETTINGS_SLUG,
			'grv3_general_section',
			[ 'id' => 'grv3_site_key', 'label' => __( 'Site Key', 'gf-recaptcha-v3' ) ]
		);

		add_settings_field(
			'grv3_secret_key',
			__( 'Secret Key', 'gf-recaptcha-v3' ),
			[ $this, 'field_callback' ],
			self::SETTINGS_SLUG,
			'grv3_general_section',
			[ 'id' => 'grv3_secret_key', 'label' => __( 'Secret Key', 'gf-recaptcha-v3' ) ]
		);

		add_settings_field(
			'grv3_threshold',
			__( 'Score Threshold', 'gf-recaptcha-v3' ),
			[ $this, 'field_callback' ],
			self::SETTINGS_SLUG,
			'grv3_general_section',
			[
				'id'    => 'grv3_threshold',
				'label' => __( 'Minimum score (e.g., 0.5)', 'gf-recaptcha-v3' ),
				'type'  => 'number'
			]
		);
	}

	public function sanitize_settings( $input ) {
		$new_input = [];
		if ( isset( $input['grv3_site_key'] ) ) {
			$new_input['grv3_site_key'] = sanitize_text_field( $input['grv3_site_key'] );
		}
		if ( isset( $input['grv3_secret_key'] ) ) {
			$new_input['grv3_secret_key'] = sanitize_text_field( $input['grv3_secret_key'] );
		}
		if ( isset( $input['grv3_threshold'] ) ) {
			$new_input['grv3_threshold'] = floatval( $input['grv3_threshold'] );
		}
		return $new_input;
	}

	public function field_callback( $args ) {
		$options = get_option( self::OPTION_NAME );
		$value   = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
		$type    = isset( $args['type'] ) ? $args['type'] : 'text';

		if ( $type === 'number' ) {
			$value = $value ? $value : self::DEFAULT_THRESHOLD;
			echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '" step="0.1" min="0" max="1" style="width: 100px;">';
			echo '<p class="description">' . esc_html( $args['label'], 'gf-recaptcha-v3' ) . '. ' . esc_html__( 'Google recommends 0.5 as a default.', 'gf-recaptcha-v3' ) . '</p>';
		} else {
			echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '" class="regular-text">';
			echo '<p class="description">' . esc_html( $args['label'], 'gf-recaptcha-v3' ) . '</p>';
		}
	}

	public function render_settings_fields() {
		settings_fields( self::OPTION_GROUP );
		do_settings_sections( self::SETTINGS_SLUG );
	}

	public function enqueue_scripts( $form, $is_ajax ) {
		static $scripts_added = false;

		if ( ! $this->is_recaptcha_enabled( $form ) || $scripts_added ) {
			return;
		}

		$options  = get_option( self::OPTION_NAME );
		$site_key = isset( $options['grv3_site_key'] ) ? $options['grv3_site_key'] : '';

		if ( empty( $site_key ) ) {
			return;
		}

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
			[ 'jquery', 'google-recaptcha' ],
			'1.0',
			true
		);

		$inline_script = "const grv3_site_key = '" . esc_js( $site_key ) . "';";
		wp_add_inline_script( 'grv3-main-js', $inline_script, 'before' );

		$scripts_added = true;
	}

	public function add_hidden_field( $form_tag, $form ) {
		if ( ! $this->is_recaptcha_enabled( $form ) ) {
			return $form_tag;
		}

		$field_id     = 'g-recaptcha-response-' . $form['id'];
		$hidden_field = '<input type="hidden" name="g-recaptcha-response" id="' . esc_attr( $field_id ) . '">';

		return $form_tag . $hidden_field;
	}

	public function validate_captcha( $validation_result ) {
		$form = $validation_result['form'];

		if ( ! $this->is_recaptcha_enabled( $form ) ) {
			return $validation_result;
		}

		$options = get_option( self::OPTION_NAME );
		$token   = rgpost( 'g-recaptcha-response' );

		if ( empty( $token ) ) {
			$validation_result['is_valid'] = false;
			$this->set_validation_error( $validation_result, $form, __( 'reCAPTCHA Error: Token not found.', 'gf-recaptcha-v3' ) );
			return $validation_result;
		}

		$secret_key = isset( $options['grv3_secret_key'] ) ? $options['grv3_secret_key'] : '';
		$url        = 'https://www.google.com/recaptcha/api/siteverify';
		$args       = [
			'body' => [
				'secret'   => $secret_key,
				'response' => $token,
				'remoteip' => GFFormsModel::get_ip(),
			],
		];

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			$validation_result['is_valid'] = false;
			$this->set_validation_error( $validation_result, $form, __( 'Error connecting to reCAPTCHA server.', 'gf-recaptcha-v3' ) );
			return $validation_result;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$threshold = isset( $options['grv3_threshold'] ) && is_numeric( $options['grv3_threshold'] ) ? (float) $options['grv3_threshold'] : self::DEFAULT_THRESHOLD;

		if ( empty( $body['success'] ) || $body['score'] < $threshold || $body['action'] !== 'gravity_form_submit' ) {
			$validation_result['is_valid'] = false;
			$error_message = __( 'reCAPTCHA validation failed. Please try again.', 'gf-recaptcha-v3' );
			$this->set_validation_error( $validation_result, $form, $error_message );
		}

		return $validation_result;
	}

	private function set_validation_error( &$validation_result, $form, $message ) {
		foreach ( $form['fields'] as &$field ) {
			if ( $field->type != 'hidden' && $field->type != 'html' ) {
				$field->failed_validation  = true;
				$field->validation_message = $message;
				break;
			}
		}
	}

	public function is_recaptcha_enabled( $form ) {
		$options = get_option( self::OPTION_NAME );
		if ( empty( $options['grv3_site_key'] ) || empty( $options['grv3_secret_key'] ) ) {
			return false;
		}

		$setting = rgar( $form, 'grv3_enabled' );

		if ( $setting === '0' || $setting === '' ) {
			return false;
		}

		return true;
	}

	public function add_form_setting_field( $fields, $form ) {
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

	public function save_form_setting( $settings, $form ) {
		if ( ! isset( $settings['grv3_enabled'] ) ) {
			$settings['grv3_enabled'] = '0';
		}
		return $settings;
	}

	public function add_settings_tab( $tabs ) {
		$tabs[] = [
			'name'  => self::SETTINGS_SLUG,
			'label' => __( 'reCAPTCHA v3', 'gf-recaptcha-v3' ),
		];
		return $tabs;
	}

	public function render_settings_page_content() {
		?>
		<form action="options.php" method="post">
			<?php
			$this->render_settings_fields();
			?>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'gf-recaptcha-v3' ); ?>" />
			</p>
		</form>
		<?php
	}
}

// Initialize the plugin
GF_Recaptcha_V3::get_instance();