<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'OEDW_Meta' ) ) :

    /**
     * Main OEDW_Start_Instance_Admin Class.
     *
     * @package		OEDW
     * @subpackage	Classes/OEDW_Meta
     * @since		1.0.0
     * @author		Opal
     */
    Class OEDW_Meta {
        private static $_instance = null;
        public function __construct() {
            
        }

        public static function instance($file = '', $version = '1.0.0') {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        
    }  

endif; // End if class_exists check.