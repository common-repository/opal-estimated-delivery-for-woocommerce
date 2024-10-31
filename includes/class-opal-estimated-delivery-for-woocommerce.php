<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'OEDW_Start_Instance' ) ) :

	/**
	 * Main OEDW_Start_Instance Class.
	 *
	 * @package		OEDW
	 * @subpackage	Classes/OEDW_Start_Instance
	 * @since		1.0.0
	 * @author		WPOPAL
	 */
	final class OEDW_Start_Instance {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|OEDW_Start_Instance
		 */
		private static $instance;

		/**
		 * OEDW helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 */
		public $helpers;

		/**
		 * OEDW settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to clone this class.', 'opal-estimated-delivery-for-woocommerce' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to unserialize this class.', 'opal-estimated-delivery-for-woocommerce' ), '1.0.0' );
		}

		/**
		 * Main OEDW_Start_Instance Instance.
		 *
		 * Insures that only one instance of OEDW_Start_Instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|OEDW_Start_Instance	The one true OEDW_Start_Instance
		 */
		public static function instance() {
			if ( !isset( self::$instance ) && !(self::$instance instanceof OEDW_Start_Instance)) {
				self::$instance	= new OEDW_Start_Instance;
				self::$instance->base_hooks();
				self::$instance->include_classes();
				self::$instance->include_helpers();
				self::$instance->settings = new OEDW_Settings();

				if (oedw_check_woocommerce_active()) {
					// Fire the plugin logic
					new OEDW_Run();
					new OEDW_Admin();
					new OEDW_Frontend();
				}
				
				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'OEDW/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function include_classes() {
			$files_custom = glob(OEDW_PLUGIN_DIR.'includes/classes/*.php');
			foreach ($files_custom as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function include_helpers() {
			$files_custom = glob(OEDW_PLUGIN_DIR.'includes/helpers/*.php');
			foreach ($files_custom as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'opal-estimated-delivery-for-woocommerce', false, dirname( plugin_basename( OEDW_PLUGIN_FILE ) ) . '/languages/' );
		}

		

	}

endif; // End if class_exists check.