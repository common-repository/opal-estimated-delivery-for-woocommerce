<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class OEDW_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		OEDW
 * @subpackage	Classes/OEDW_Run
 * @author		Opal
 * @since		1.0.0
 */
class OEDW_Run{

	/**
	 * Our OEDW_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 20 );		
	
	}

	/**
	 * ######################
	 * ###
	 * #### WP Translations Data
	 * ###
	 * ######################
	 */
	private function oedw_print_translations_data() {
        require OEDW_PLUGIN_DIR.'includes/helpers/translation.php';
        wp_localize_script('form-builder-lib', 'oedw_trans_lib', $translations_lib);
        wp_localize_script('form-render-lib', 'oedw_trans_lib', $translations_lib);

        wp_localize_script('oedw-backend-scripts', 'oedw_trans', $translations);
        wp_localize_script('opalwoocu-frontend-scripts', 'oedw_trans', $translations);
    }

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		global $post_type_object, $typenow, $pagenow, $current_screen;

		wp_register_script( 'form-repeater-lib', OEDW_PLUGIN_URL . 'assets/js/libs/form-repeater.js', array( 'jquery' ), OEDW_VERSION, true );
		wp_register_script( 'toast-notice-script', OEDW_PLUGIN_URL . 'assets/js/libs/jquery.toast.min.js', array( 'jquery' ), OEDW_VERSION, true );
		wp_register_script( 'flatpickr-lib', OEDW_PLUGIN_URL . 'assets/js/libs/flatpickr.min.js', array( 'jquery' ), OEDW_VERSION, true );
		wp_register_script( 'oedw-backend-scripts', OEDW_PLUGIN_URL . 'assets/js/backend/backend-scripts.js', array( 'jquery' ), OEDW_VERSION, true );

		wp_register_style( 'oedw-backend-styles', OEDW_PLUGIN_URL . 'assets/css/backend-styles.css', array(), OEDW_VERSION, 'all' );
		wp_register_style( 'toast-notice-style', OEDW_PLUGIN_URL . 'assets/css/libs/jquery.toast.min.css', array(), OEDW_VERSION, 'all' );
		wp_register_style( 'flatpickr-style', OEDW_PLUGIN_URL . 'assets/css/libs/flatpickr.min.css', array(), OEDW_VERSION, 'all' );

		wp_localize_script( 'oedw-backend-scripts', 'oedw_script', array(
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "oedw-nonce-ajax" )
		));

		wp_enqueue_style( 'oedw-backend-styles' );
		wp_enqueue_style( 'toast-notice-style' );
		wp_enqueue_style( 'flatpickr-style' );
		
		wp_enqueue_script( 'flatpickr-lib' );
		wp_enqueue_script( 'form-repeater-lib' );
		wp_enqueue_script( 'toast-notice-script' );
		wp_enqueue_script( 'oedw-backend-scripts' );

		wp_enqueue_media();
		wp_enqueue_script('wp-color-picker');
    	wp_enqueue_style('wp-color-picker');

		$this->oedw_print_translations_data();
	}

	
	/**
	 * Enqueue the frontend related scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_frontend_scripts_and_styles() {
		wp_register_script( 'oedw-frontend-scripts', OEDW_PLUGIN_URL . 'assets/js/frontend/frontend-scripts.js', array( 'jquery' ), OEDW_VERSION, true );
		wp_localize_script( 'oedw-frontend-scripts', 'oedw_script', array(
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "oedw-nonce-ajax" )
		));

		wp_register_style( 'oedw-frontend-styles', OEDW_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), OEDW_VERSION, 'all' );
		wp_enqueue_style( 'oedw-frontend-styles' );

		wp_enqueue_script( 'oedw-frontend-scripts' );

		$this->oedw_print_translations_data();

	}


}
