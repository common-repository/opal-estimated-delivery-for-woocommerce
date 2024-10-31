<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'OEDW_Frontend' ) ) :

	/**
	 * Main OEDW_Frontend Class.
	 *
	 * @package		OEDW
	 * @subpackage	Classes/OEDW_Frontend
	 * @since		1.0.0
	 * @author		Opal
	 */
	final class OEDW_Frontend {

        /**
		 * OEDW settings object.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
		private $settings;
        
        /**
		 * OEDW settings_data.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
		private $settings_data;

        public function __construct() {
            // Settings object
			$this->settings = oedw()->settings;
            $this->settings_data = $this->settings->oedw_get_settings_data();
            
            // Run in frontend
            $this->oedw_add_filter();
            $this->oedw_add_action();

            // Add shortcode
            add_shortcode( 'oedw', [ $this, 'oedw_shortcode' ] );
        }

        /**
		 *  Call View Fontend Template
		 */
		public static function view($view, $data = array()) {
			extract($data);
			$path_view = apply_filters('oedw_path_view_fontend', OEDW_PLUGIN_DIR . 'views/frontend/' . $view . '.php', $data);
			include($path_view);
		}
        
        /**
		 * OEDW add action hook.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
        private function oedw_add_action() {
            add_action( 'template_redirect', [$this, 'oedw_update_est_cart'] );

            $render_hook = $this->oedw_handle_render_hook_product();
            if ($render_hook) {
                add_action( $render_hook['hook_action'], [$this, 'oedw_render_in_product_page'], absint($render_hook['prioty']) ); 
            }

            // Order details
            add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'oedw_create_order_line_item' ], 10, 3 );
            add_action( 'woocommerce_order_item_meta_start', [ $this, 'oedw_order_item_meta_start' ], 10, 2 );

            // Ajax init
            add_action( 'wp_ajax_nopriv_oedw_update_est_date_cart', [$this, 'oedw_update_est_date_cart'], 20 );
            add_action( 'wp_ajax_oedw_update_est_date_cart', [$this, 'oedw_update_est_date_cart'], 20 );
        }
        
        /**
		 * OEDW add filter hook.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
        private function oedw_add_filter() {
            add_filter( 'woocommerce_get_item_data', [ $this, 'oedw_add_to_item_cart' ], 10, 2 );

            // Order details
            add_filter( 'woocommerce_add_cart_item_data', [ $this, 'oedw_add_cart_item_data' ], 10, 2 );
            
        }

        /**
         * OEDW add shortcode
         *
         * @access  public
         * @since   1.0.0
         */
        public function oedw_shortcode($args) {
            if(isset($args['id'])) {
                $product_id = $args['id'];
            }
            elseif (is_product()) {
                $product_id = get_the_ID();
            }
            else {
                echo esc_html__('Please add a product id into shortcode', 'opal-estimated-delivery-for-woocommerce');
                return;
            }

            $est_date = $this->oedw_get_estimate_date_product($product_id);   
            if (!$est_date) {
                echo esc_html__('There is no estimated delivery date for this product ', 'opal-estimated-delivery-for-woocommerce');
                return;
            }
            if (empty($est_date['min_date']) && empty($est_date['max_date'])) {
                echo esc_html__('There is no estimated delivery date for this product ', 'opal-estimated-delivery-for-woocommerce');
                return;
            }
            
            $est_content = oedw_get_option('est_date_content', '', $this->settings_data);

            do_action('oedw_before_date_esimate_shortcode', $est_date);

            self::view('date-estimate', ['est_date' => $est_date, 'est_content' => $est_content]);    

            do_action('oedw_after_date_esimate_shortcode', $est_date);
        }

        public function oedw_update_est_cart() {
            // exit function if not on front-end
            if ( is_admin() ) {
                return;
            }

            // WC()->session->set('chosen_shipping_methods', array( 'local_pickup:6' ) );

            // $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
            // var_dump($chosen_shipping_methods);

            $settings_updated = get_option('oedw_updated_settings');
            if (!$settings_updated) return;

            global $woocommerce;
			$cart_contents = $woocommerce->cart->cart_contents;
			if (!empty($cart_contents)) {
				foreach ($cart_contents as $cart_item_key => $cart_item) {
					$product_id = $cart_item['product_id'];

                    $est_date = $this->oedw_get_estimate_date_product($product_id);   
                    if (!$est_date) continue;
                    if (empty($est_date['min_date']) && empty($est_date['max_date'])) continue;
                    
                    $est_content = oedw_get_option('est_date_content', '', $this->settings_data);

                    ob_start();
                    self::view('date-estimate', ['est_date' => $est_date, 'est_content' => $est_content]);     
                    $est_data_new = ob_get_clean();
                 
                    $woocommerce->cart->cart_contents[$cart_item_key]['_oedw_est_date'] = $est_data_new;
				}
			}

            // Update cart && checkout
            $woocommerce->cart->set_session(); 

            // Stop run this hook
            update_option('oedw_updated_settings', 0);
        }


        public function oedw_render_in_product_page() {
            global $product;
            
            $est_date = $this->oedw_get_estimate_date_product(get_the_ID());
            if (!$est_date) return false;
            if (empty($est_date['min_date']) && empty($est_date['max_date'])) return false;
            
            $est_content = oedw_get_option('est_date_content', '', $this->settings_data);
            $show_method_detail = oedw_get_option('show_estimate_shipping_method', false, $this->settings_data);
            
            do_action('oedw_before_date_esimate_box_product');

            do_action('oedw_before_date_esimate_product', $est_date);
            
            self::view('date-estimate', [
                'est_date' => $est_date, 
                'est_content' => $est_content,
                'show_method_detail' => $show_method_detail,
            ]);
            
            do_action('oedw_after_date_esimate_product', $est_date);
            
            if ($show_method_detail && is_singular('product')) {
                $zones = WC_Shipping_Zones::get_zones();
                if (is_array($zones) && !empty($zones)) {
                    $est_method_data = [];
                    foreach ($zones as $k => $zone) {
                        $zone_id = $zone['zone_id'];
                        $zone_name = $zone['zone_name'];
                        $shipping_methods = $zone['shipping_methods'];

                        if (is_array($shipping_methods) && !empty($shipping_methods)) {
                            $method_detail = [];
                            foreach ( $shipping_methods as $i => $class ) {
                                if($class->enabled == 'yes') {
                                    $method_id = $class->id.':'.$zone_id;
                                    $method_detail[$method_id]['method_title'] = $class->method_title;
                                    $method_detail[$method_id]['method_est'] = $this->oedw_get_estimate_date_product(get_the_ID(), $method_id);
                                }
                            }
                            $est_method_data[$k]['zone_name'] = $zone_name;
                            $est_method_data[$k]['method_detail'] = $method_detail;
                        }

                    }
                    
                    self::view('shipping-method-estimate', [
                        'est_method_data' => $est_method_data
                    ]);
                }
            }

            do_action('oedw_after_date_esimate_box_product');
        }

        private function oedw_handle_render_hook_product() {
            $settings_data = $this->settings_data;
            $render_hook = oedw_get_option('product_render_position', '', $settings_data);

            if (empty($render_hook)) return false;

            $options = explode( '-', $render_hook ) ;
            $hook_action = isset($options[0]) ? $options[0] : 'woocommerce_before_add_to_cart_button';
            $prioty = isset($options[1]) ? $options[1] : 100;

            $render_prioty = oedw_get_option('render_position_prioty', false, $settings_data);
            if ($render_prioty && $render_prioty != '') {
                $prioty = $render_prioty;
            }

            return [
                'hook_action' => $hook_action,
                'prioty' => $prioty
            ];
        }

        private function oedw_get_estimate_date_product($product_id, $chosen_shipping_methods = false) {
            $rules_data = oedw_get_option('rule_apply_data', false, $this->settings_data);
            if (!$rules_data || !is_array($rules_data)) return false;
            
            $product = wc_get_product($product_id);
			// if ( is_a($product, 'WC_Product_Variable') ) {
            //     $product_variations = $product->get_available_variations();
            // }

            if( !$product instanceof WC_Product ) return false;

            $stock_status = $product->get_stock_status();
            $min_day = 0;
            $max_day = 0;
            $exclude_weekdays = [];
            $special_days = [];
            $holiday_days = oedw_get_option('holidays', [], $this->settings_data);
            $exclude_holiday = false;
            $cutoff_time = [];

            foreach ($rules_data as $i => $rule) {
                $pass = false;
                if (!empty($rule['rule_apply_for'])) {
                    $apply_select = (!empty($rule['rule_apply_select_val'])) ? $rule['rule_apply_select_val'] : [];
                    switch ($rule['rule_apply_for']) {
                        case 'all':
                            $pass = true;
                            break;
                        case 'instock':
                            $pass = $stock_status == 'instock';
                            break;
                        case 'outofstock':
                            $pass = $stock_status == 'outofstock';
                            break;
                        case 'onbackorder':
                            $pass = $stock_status == 'onbackorder';
                            break;
                        case 'product':
                            if (in_array($product_id, $apply_select)) {
                                $pass = true;
                            }
                            break;
                        case 'category':
                            if (oedw_is_product_in_taxs($product_id, $apply_select, 'product_cat')) {
                                $pass = true;
                            }
                            break;
                        case 'tag':
                            if (oedw_is_product_in_taxs($product_id, $apply_select, 'product_tag')) {
                                $pass = true;
                            }
                            break;
                        case 'shipping_class':
                            if (oedw_is_product_in_taxs($product_id, $apply_select, 'product_shipping_class')) {
                                $pass = true;
                            }
                            break;
                        default:
                            if (in_array($product->get_type(), $apply_select)) {
                                $pass = true;
                            }
                            break;
                    }
                }

                if ($chosen_shipping_methods && $pass) {
                    $shipping_method = (!empty($rule['shipping_method'])) ? $rule['shipping_method'] : 'default';
                    if ($shipping_method != 'default' && $shipping_method != $chosen_shipping_methods) {
                        $pass = false;
                    }
                }

                if ($pass) {
                    if (!empty($rule['shipping_min_day'])) {
                        $rule_minday = absint($rule['shipping_min_day']);
                        if ($min_day <= 0 || $min_day > $rule['shipping_min_day']) {
                            $min_day = $rule_minday;
                        }
                    }
                    if (!empty($rule['shipping_max_day'])) {
                        $rule_maxday = absint($rule['shipping_max_day']);
                        if ($max_day <= $rule['shipping_max_day']) {
                            $max_day = $rule_maxday;
                        }
                    }
                    if (!empty($rule['detail_delivery']) && $rule['detail_delivery']) {
                        if (!empty($rule['shipping_cutoff_time'])) {
                            $cutoff_time[] = $rule['shipping_cutoff_time'];
                        }    
                        if (!empty($rule['exclude_delivery_on'])) {
                            if (!$exclude_holiday && in_array('holiday', $rule['exclude_delivery_on'])) {
                                $exclude_holiday = $holiday_days;
                            }
                            $exclude_weekdays = array_unique(array_merge($exclude_weekdays, array_diff($rule['exclude_delivery_on'], ['holiday'])));
                        }    
                        if (!empty($rule['exclude_delivery_day'])) {
                            $exclude_delivery_day = array_map('trim', explode(',', $rule['exclude_delivery_day']));
                            if (is_array($exclude_delivery_day)) {
                                $special_days = array_unique(array_merge($special_days, $exclude_delivery_day));
                            }
                        }    
                    }
                }
            }

            if (!empty($exclude_weekdays)) {
                $exclude_weekdays = oedw_convert_weekday_to_iso($exclude_weekdays);
            }
            
            if ($min_day >= $max_day) {
                $max_day = 0;
            }

            $date_format = oedw_get_option('date_format', 'Y-m-d', $this->settings_data);

            $min_date = self::oedw_calculate_dates($min_day, $exclude_weekdays, $special_days, $exclude_holiday, $cutoff_time, $date_format);
            $max_date = self::oedw_calculate_dates($max_day, $exclude_weekdays, $special_days, $exclude_holiday, $cutoff_time, $date_format);

            $est_date = apply_filters('oedw_est_date_product', [
                'min_date' => $min_date,
                'max_date' => $max_date,
            ]);

            return $est_date;
        }

        public static function oedw_calculate_dates($days, $weekdays, $special_days, $holiday_days, $cutoff_time, $date_format) {
            if ($days <= 0) return '';

            $from = new DateTime();
            $dates = $from->format('Y-m-d');
            while ($days) {
                $from->modify('+1 day');
        
                if (in_array($from->format('N'), $weekdays)) continue;
                if (in_array($from->format('Y-m-d'), $special_days)) continue;
                if (is_array($holiday_days) && in_array($from->format('*-m-d'), $holiday_days)) continue;
        
                $dates = $from->format($date_format);
                $days--;
            }

            if (is_array($cutoff_time)) {
                $currentTime = current_time('timestamp');
                $max_skip_time = 0;
                foreach ($cutoff_time as $time) {
                    $skip_time = strtotime(current_datetime()->format('Y-m-d').' '.$time);
                    if ($skip_time > $max_skip_time) {
                        $max_skip_time = $skip_time;
                    }
                }
                if ($currentTime >= $max_skip_time) {
                    $from->modify('+1 day');
                    $dates = $from->format($date_format);
                }
            }
            return $dates;
        }

        public function oedw_create_order_line_item( $order_item, $cart_item_key, $values ) {
            if ( isset( $values['_oedw_est_date'] ) ) {
                $order_item->update_meta_data( '_oedw_est_date', $values['_oedw_est_date'] );
            }
        }

        public function oedw_order_item_meta_start( $order_item_id, $order_item ) {
            $show_in_order = oedw_get_option('show_in_order', 0, $this->settings_data);
            if ($show_in_order && ( $date = $order_item->get_meta( '_oedw_est_date' ) ) ) {
                echo wp_kses_post(apply_filters( 'oedw_order_item_date', $date, $order_item_id, $order_item ));
            }
        }

        public function oedw_add_cart_item_data( $cart_item_data, $product_id ) {
            $est_date = $this->oedw_get_estimate_date_product( $product_id );
            if (!$est_date) return false;
            if (empty($est_date['min_date']) && empty($est_date['max_date'])) return false;
            
            $est_content = oedw_get_option('est_date_content', '', $this->settings_data);
            
            ob_start();
            self::view('date-estimate', ['est_date' => $est_date, 'est_content' => $est_content]);
            $est_date_str = ob_get_clean();

            $cart_item_data['_oedw_est_date'] = $est_date_str;

            return $cart_item_data;
        }

        public function oedw_add_to_item_cart($data, $cart_item) {
            $show_in_cart_item = oedw_get_option('show_in_cart_item', 0, $this->settings_data);
            if(!$show_in_cart_item) return $data;

            if ( ! empty( $cart_item['_oedw_est_date'] ) ) {
				$data['oedw_date'] = apply_filters( 'oedw_cart_item_meta', [
					'key'     => apply_filters( 'oedw_cart_item_meta_key', __('Shipping', 'opal-estimated-delivery-for-woocommerce'), $cart_item ),
					'value'   => apply_filters( 'oedw_cart_item_meta_value', $cart_item['_oedw_est_date'], $cart_item ),
					'display' => apply_filters( 'oedw_cart_item_meta_display', $cart_item['_oedw_est_date'], $cart_item ),
				], $cart_item );
			}

            return $data;
        }

        public function oedw_update_est_date_cart() {
            check_ajax_referer( 'oedw-nonce-ajax', 'ajax_nonce_parameter' );

            $shipping_method = sanitize_text_field($_POST['shipping_method']);

            echo esc_html('This feature is being developed =]]');
        }
    }
endif;