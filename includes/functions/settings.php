<?php
namespace AppleMapsForWordpress\Settings;

/**
 * Setup settings
 *
 * @since 1.0
 */
function setup() {
	add_action(
		'plugins_loaded', function() {
			add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu', 20 );
			add_action( 'admin_init', __NAMESPACE__ . '\setup_fields_sections' );
			add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );
		}
	);
}

/**
 * Output setting menu option
 *
 * @since  1.0
 */
function admin_menu() {
	add_options_page(
		'Apple Maps for WordPress',
		'Apple Maps',
		'manage_options',
		'apple-maps-wordpress',
		__NAMESPACE__ . '\settings_screen'
	);
}

/**
 * Output setting screen
 *
 * @since  1.0
 */
function settings_screen() {
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Apple Maps for WordPress Settings', 'apple-maps-for-wordpress' ); ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'amfwp_settings' ); ?>
			<?php do_settings_sections( 'applemapswordpress' ); ?>
			<button aria-label="Generate Long Life Token"
					class="button"
					id="generate-token"
			><?php esc_html_e( 'Generate Token', 'apple-maps-for-wordpress' ); ?></button>
			<?php submit_button(); ?>

		</form>
	</div>
	<?php
}

/**
 * Register setting fields and sections
 *
 * @since  1.0
 */
function setup_fields_sections() {
	add_settings_section( 'amfwp-section-1', 'Active Authorization Token', '', 'applemapswordpress' );
	add_settings_section( 'amfwp-section-2', 'Generate an Authorization Token', '', 'applemapswordpress' );
	add_settings_field(
		'long_life_auth_token',
		esc_html__( 'Long Life Authorization Token', 'apple-maps-for-wordpress' ),
		__NAMESPACE__ . '\long_life_auth_token',
		'applemapswordpress',
		'amfwp-section-1'
	);

	add_settings_field(
		'token_gen_authkey',
		esc_html__( 'MapKit JS Key', 'apple-maps-for-wordpress' ),
		__NAMESPACE__ . '\token_gen_authkey',
		'applemapswordpress',
		'amfwp-section-2'
	);

	add_settings_field(
		'token_gen_iss',
		esc_html__( 'Apple Developer Team ID', 'apple-maps-for-wordpress' ),
		__NAMESPACE__ . '\token_gen_iss',
		'applemapswordpress',
		'amfwp-section-2'
	);

	add_settings_field(
		'token_gen_kid',
		esc_html__( 'MapKit JS Key Name', 'apple-maps-for-wordpress' ),
		__NAMESPACE__ . '\token_gen_kid',
		'applemapswordpress',
		'amfwp-section-2'
	);
}

/**
 * Render the long life token settings box
 */
function long_life_auth_token() {
	$settings = get_option( 'amfwp_settings' );
	$ll_token = isset( $settings['long_life_token'] ) ? $settings['long_life_token'] : '';
	?>
	<textarea name="amfwp_settings[long_life_token]" cols="40" rows="10" id="long-life-token"><?php echo esc_textarea( $ll_token ); ?></textarea>
	<p class="description">
		<?php echo wp_kses_post( _e( 'This Long Life Authorization Token is used by the Apple Maps for WordPress plugin to authenticate with MapKit JS. <br>For more information please see <a href="https://developer.apple.com/videos/play/wwdc2018/508" target="_blank" rel="noopener noreferrer">Getting and Using a MapKit JS Key on the Apple Developer site.', 'apple-maps-for-wordpress' ) ); ?>
	</p>
	<?php
}

/**
 *
 */
function token_gen_authkey() {
	$settings = get_option( 'amfwp_settings' );
	$authkey  = isset( $settings['token_gen_authkey'] ) ? $settings['token_gen_authkey'] : '';
	?>
	<textarea name="amfwp_settings[token_gen_authkey]" cols="40" rows="10" id="token-gen-authkey"><?php echo esc_textarea( $authkey ); ?></textarea>
	<p class="description">
		<?php echo wp_kses_post( _e( 'Copy and paste the contents of the MapKit JS Key that was generated and downloaded from the Apple Developer website.<br>For instructions on generating a MapKit KS key, see <a href="https://developer.apple.com/videos/play/wwdc2018/508" target="_blank" rel="noopener noreferrer">Getting and Using a MapKit JS Key on the Apple Developer site.', 'apple-maps-for-wordpress' ) ); ?>
	</p>
	<?php
}

/**
 *
 */
function token_gen_iss() {
	$settings = get_option( 'amfwp_settings' );
	$iss      = isset( $settings['token_gen_iss'] ) ? $settings['token_gen_iss'] : '';
	?>
	<input type="text" name="amfwp_settings[token_gen_iss]" id="token-gen-iss" value="<?php echo esc_attr( $iss ); ?>"/>
	<p class="description">
		<?php echo wp_kses_post( _e( 'Your Team ID can be found in your Apple Developer account <a href="https://developer.apple.com/account/#/membership/" target="_blank" rel="noopener noreferrer">here</a>. Requires login.', 'apple-maps-for-wordpress' ) ); ?>
	</p>
	<?php
}

/**
 *
 */
function token_gen_kid() {
	$settings = get_option( 'amfwp_settings' );
	$kid      = isset( $settings['token_gen_kid'] ) ? $settings['token_gen_kid'] : '';
	?>
	<input type="text" name="amfwp_settings[token_gen_kid]" id="token-gen-kid" value="<?php echo esc_attr( $kid ); ?>"/>
	<p class="description">
		<?php echo wp_kses_post( _e( 'This is the name of the MapKit JS key file that was downloaded from the Apple Developer website. Please omit the .p8 extension.', 'apple-maps-for-wordpress' ) ); ?>
	</p>
	<?php
}




/**
 * Register settings for options table
 *
 * @since  1.0
 */
function register_settings() {
	register_setting( 'amfwp_settings', 'amfwp_settings', __NAMESPACE__ . '\sanitize_settings' );
}

/**
 * Sanitize settings for DB
 *
 * @param array $settings The array of setting to sanitize.
 * @return array
 * @since  1.0
 */
function sanitize_settings( $settings ) {
	$new_settings = [];
	if ( isset( $settings['long_life_token'] ) ) {
		$new_settings['long_life_token'] = sanitize_textarea_field( $settings['long_life_token'] );
	}

	if ( isset( $settings['token_gen_authkey'] ) ) {
		$new_settings['token_gen_authkey'] = $settings['token_gen_authkey'];
	}

	if ( isset( $settings['token_gen_iss'] ) ) {
		$new_settings['token_gen_iss'] = sanitize_text_field( $settings['token_gen_iss'] );
	}

	if ( isset( $settings['token_gen_kid'] ) ) {
		$new_settings['token_gen_kid'] = sanitize_text_field( $settings['token_gen_kid'] );
	}
	return $new_settings;
}

