<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'OEDW_Admin' ) ) :

	/**
	 * Main OEDW_Admin Class.
	 *
	 * @package		OEDW
	 * @subpackage	Classes/OEDW_Admin
	 * @since		1.0.0
	 * @author		Opal
	 */
	final class OEDW_Admin {

		/**
		 * OEDW settings object.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
		private $settings;

        public function __construct()
        {
            // Create product meta
            OEDW_Meta::instance();

			// Settings object
			$this->settings = oedw()->settings;
         
			add_action( 'admin_menu', [$this, 'oedw_custom_submenu' ] );

			add_action( 'wp_ajax_oedw_load_rule_apply_ajax', [$this, 'oedw_load_rule_apply_ajax'] ); // wp_ajax_{action}
			add_action( 'wp_ajax_oedw_handle_settings_form', [$this, 'oedw_handle_settings_form'] );
			add_action( 'wp_ajax_oedw_settings_export', [$this, 'oedw_settings_export'] );
			add_action( 'wp_ajax_oedw_handle_import_settings', [$this, 'oedw_handle_import_settings'] );
        }

        /**
		 *  Call View Admin Template
		 */
		public static function view($view, $data = array()) {
			extract($data);
			$path_view = apply_filters('oedw_path_view_admin', OEDW_PLUGIN_DIR . 'views/backend/' . $view . '.php', $data);
			include($path_view);
		}

		public function oedw_custom_submenu() {
			global $pagenow;
	
			add_submenu_page(
				'woocommerce',
				__( 'OEDW Setting', 'opal-estimated-delivery-for-woocommerce' ),
				__( 'Estimated Delivery', 'opal-estimated-delivery-for-woocommerce' ),
				'manage_options',
				'oedw-settings',
				[$this, 'oedw_setting_page_callback'],
			);
	
			if (isset($_GET['page']) && $_GET['page'] == 'oedw-settings') {
				remove_all_actions( 'admin_notices' );
			}
		}
		
		public function oedw_setting_page_callback() {
			wp_enqueue_style( 'woocommerce_admin_styles' );
			wp_enqueue_style( 'wc-admin-layout' );
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'jquery-tiptip' );
			
			$settings_data = $this->settings->oedw_get_settings_data();
			self::view('admin-settings', ['settings' => $settings_data]);
		}	

		public function oedw_load_rule_apply_ajax(){
            check_ajax_referer( 'oedw-nonce-ajax', 'ajax_nonce_parameter' );

            if(empty($_GET['q'])) return false;
            if(empty($_GET['term'])) return false;

            $kw = sanitize_text_field($_GET['q']);
            $term = sanitize_text_field($_GET['term']);

            $func_search = 'oedw_get_'.$term.'_by_keyword';

            $return = $this->$func_search($kw);

            if (!$return) return false;
            echo wp_json_encode( $return );
            die;
        }

        private function oedw_get_product_by_keyword($kw) {
        	$return = false;

        	$search_results = new WP_Query( array( 
        	    's'=> wc_clean($kw), // the search query
        	    'post_status' => 'publish', // if you don't want drafts to be returned
        	    'post_type' => 'product',
        	    'posts_per_page' => -1 // how much to show at once
        	) );

        	if( $search_results->have_posts() ) {
        		$return = [];
        	    while( $search_results->have_posts() ) : $search_results->the_post();	
        	        // shorten the title a little
        	        $title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
        	        $return[] = array( $search_results->post->ID, $title );
        	    endwhile;
        	}
        	
        	return $return;
        }

        private function oedw_get_category_by_keyword($kw) {
        	global $wpdb;
        	$taxonomy = 'product_cat';
        	$return = false;

        	$results = $wpdb->get_results(
        	    $wpdb->prepare(
        	        "SELECT t.*, tt.*
        	        FROM $wpdb->terms AS t
        	        INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
        	        WHERE tt.taxonomy = %s
        	        AND t.name LIKE %s",
        	        $taxonomy,
        	        '%' . $wpdb->esc_like($kw) . '%'
        	    )
        	);

        	// In kết quả
        	if ($results && !empty($results)) {
    			$return = [];
    		    foreach ($results as $term) {
    		        // shorten the title a little
    		        $title = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
    		        $return[] = array( $term->term_id, $title );
    		    }
        	}
        	return $return;
        }

        private function oedw_get_tag_by_keyword($kw) {
        	global $wpdb;
        	$taxonomy = 'product_tag';
        	$return = false;

        	$results = $wpdb->get_results(
        	    $wpdb->prepare(
        	        "SELECT t.*, tt.*
        	        FROM $wpdb->terms AS t
        	        INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
        	        WHERE tt.taxonomy = %s
        	        AND t.name LIKE %s",
        	        $taxonomy,
        	        '%' . $wpdb->esc_like($kw) . '%'
        	    )
        	);

        	// In kết quả
        	if ($results && !empty($results)) {
    			$return = [];
    		    foreach ($results as $term) {
    		        // shorten the title a little
    		        $title = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
    		        $return[] = array( $term->term_id, $title );
    		    }
        	}
        	return $return;
        }

        private function oedw_get_type_by_keyword($kw) {
        	$product_types = wc_get_product_types();
    		$return = [];
        	if ($product_types) {
        	    foreach ($product_types as $product_type => $label) {
        	    	$return[] = array( $product_type, $label );
        	    }
        	} 
        	else {
        	    return false;
        	}
        	return $return;
        }

        private function oedw_get_shipping_class_by_keyword($kw) {
        	$shipping_classes = WC()->shipping->get_shipping_classes();
    		$return = [];
        	if ($shipping_classes) {
        	    foreach ($shipping_classes as $class) {
        	    	$return[] = array( $class->term_id, $class->name );
        	    }
        	} 
        	else {
        	    return false;
        	}
        	return $return;
        }

		public function oedw_handle_settings_form() {
			check_ajax_referer( 'oedw-nonce-ajax', 'ajax_nonce_parameter' );
	
			$settings = $this->settings->oedw_get_settings_default();
	
			foreach ($settings as $name => $field) {
				if ($name != 'rule_apply_data') {
					$field_val = isset($_POST[$name]) ? wc_clean($_POST[$name]) : 0;
					$settings[$name] = $field_val;
				}
			}
	
			$rule_field = $settings['rule_apply_data'][0];
			$rule_apply_data = [];
			$i = 0;
			while (isset($_POST['rule_apply_for_'.$i])) {
				$rule_apply_data[$i] = [];
				foreach ($rule_field as $field => $default) {
					if (isset($_POST[$field.'_'.$i])) {
						$rule_apply_data[$i][$field] = wc_clean($_POST[$field.'_'.$i]);
					}
				}
				$i++;
			}
			$settings['rule_apply_data'] = $rule_apply_data;

			$flag = update_option(OEDW_SETTINGS_KEY, wp_json_encode($settings));
			update_option('oedw_updated_settings', 1);

			wp_send_json_success( [
				'message' => esc_html__('Update settings successfully!', 'opal-estimated-delivery-for-woocommerce')
			] );
			
			die();
		}

		public function oedw_settings_export() {
			check_ajax_referer( 'oedw-nonce-ajax', 'ajax_nonce_parameter' );
	
			$file_data = $this->settings->prepare_template_export();
	
			if ( is_wp_error( $file_data ) ) {
				return $file_data;
			}
	
			oedw_send_file_headers( $file_data['name'], strlen( $file_data['content'] ) );
	
			// Clear buffering just in case.
			@ob_end_clean();
	
			flush();
	
			// Output file contents.
			add_filter('esc_html', 'oedw_prevent_escape_html', 99, 2);
			echo esc_html($file_data['content']);
			remove_filter('esc_html', 'oedw_prevent_escape_html', 99, 2);
	
			die;
		}

		public function oedw_handle_import_settings() {
			check_ajax_referer( 'oedw-nonce-ajax', 'ajax_nonce_parameter' );
	
			if (isset($_FILES['oedw_setting_import']["error"]) && $_FILES['oedw_setting_import']["error"] != 4) {
				if ($_FILES['oedw_setting_import']["error"] == UPLOAD_ERR_INI_SIZE) {
					$error_message = esc_html__('The uploaded file exceeds the maximum upload limit', 'opal-estimated-delivery-for-woocommerce');
				} else if (in_array($_FILES['oedw_setting_import']["error"], array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE))) {
					$error_message = esc_html__('The uploaded file exceeds the maximum upload limit', 'opal-estimated-delivery-for-woocommerce');
				}
				$ext = pathinfo($_FILES['oedw_setting_import']['name'], PATHINFO_EXTENSION);
				if ($ext != 'json' || $_FILES['oedw_setting_import']['type'] != 'application/json') {
					$error_message = esc_html__('Only allow upload Json(.json) file', 'opal-estimated-delivery-for-woocommerce');
				}
			}
			else {
				$error_message = esc_html__('Please upload a file to import', 'opal-estimated-delivery-for-woocommerce');
			}
			
			$data_upload = file_get_contents(sanitize_url($_FILES['oedw_setting_import']['tmp_name']));
			// $data_upload = json_decode($data_upload, true);
			if (empty($data_upload)) {
				$error_message = esc_html__('File upload is empty', 'opal-estimated-delivery-for-woocommerce');
			}
	
			if (isset($error_message)) {
				var_dump($error_message);
				$error = new \WP_Error( 'file_error', $error_message );
				if ( is_wp_error( $error ) ) {
					_default_wp_die_handler( $error->get_error_message(), 'OEDW' );
				}
			}
	
			update_option(OEDW_SETTINGS_KEY, $data_upload);
			// set_transient( 'oedw_import_settings', 'yes',  10);
			// var_dump($error_message); die();
			$redirect = admin_url('admin.php?page=oedw-settings');
			
			header("Location: $redirect");
			exit;
		}
    }

endif;

