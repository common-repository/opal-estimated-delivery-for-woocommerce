<?php
use Automattic\WooCommerce\Admin\Features\Features;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class OEDW_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		OEDW
 * @subpackage	Classes/OEDW_Settings
 * @author		Opal
 * @since		1.0.0
 */
class OEDW_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * Our OEDW_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->plugin_name = OEDW_NAME;
		$plugin = OEDW_PLUGIN_BASE;
		
        add_filter("plugin_action_links_$plugin", array($this, 'add_settings_link'));

		register_activation_hook(OEDW_PLUGIN_FILE, array($this, 'install'));
		register_activation_hook(OEDW_PLUGIN_FILE, array($this, 'oedw_deactive_without_woocommerce'));
		register_deactivation_hook(OEDW_PLUGIN_FILE, array($this, 'deactivation'));

		// add_action(OEDW_CRON_HOOK, array($this, 'oedw_delete_temp_files'));
		add_action('admin_init', array($this, 'oedw_trigger_deactice_addon_without_woocommerce'));
	}

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'OEDW/settings/get_plugin_name', $this->plugin_name );
	}

	public function add_settings_link($links) {
		if ( !oedw_check_woocommerce_active() ) return $links;

        $settings = '<a href="' . admin_url('admin.php?page=oedw-settings') . '">' . esc_html__('Settings', 'opal-estimated-delivery-for-woocommerce') . '</a>';
        array_push($links, $settings);
        
        return $links;
    }

	public function oedw_deactive_without_woocommerce() {
		if (!class_exists('Woocommerce')) {
			add_action( 'admin_notices', array($this, 'oedw_child_plugin_notice') );
			// deactivate_plugins(OEDW_PLUGIN_BASE);
		}
	}
	
	public function oedw_trigger_deactice_addon_without_woocommerce() {
		if (!class_exists('Woocommerce')) {
			add_action( 'admin_notices', array($this, 'oedw_child_plugin_notice') );
		}
	}
	
	public function oedw_child_plugin_notice(){
		$message = __('<strong>Opal Estimated Delivery for Woocommerce</strong> is an addon extention of <strong>Woocommerce Plugin</strong>. Please active <strong>Woocommerce Plugin</strong> to be able to use this extention!', 'opal-estimated-delivery-for-woocommerce');
		?>
		<div class="error"><p><?php echo wp_kses_post($message); ?></p></div>
		<?php
	}

	public function install() {
		$this->oedw_add_default_settings();
	}

	public function deactivation() {
		wp_clear_scheduled_hook(OEDW_CRON_HOOK);
	}

	private function oedw_add_default_settings() {
		$settings_option = get_option(OEDW_SETTINGS_KEY);
		if (!$settings_option) {
			$settings = $this->oedw_get_settings_default();
			update_option(OEDW_SETTINGS_KEY, wp_json_encode($settings));
		}
	}

	public function oedw_get_settings_default() {
		$settings = [
			'product_render_position' => '',
			'render_position_prioty' => '',
			'show_estimate_shipping_method' => 1,
			'show_in_cart_item' => 1,
			'show_in_cart_total' => 0,
			'show_in_order' => 1,
			'date_format' => 'Y/m/d',
			'holidays' => '',
			'rule_apply_data' => [
				[
					'rule_apply_for' => 'all', 
					'rule_apply_select_val' => '', 
					'shipping_method' => '', 
					'shipping_min_day' => '', 
					'shipping_max_day' => '', 
					'shipping_date' => '', 
					'display_on_product' => 0,
					'detail_delivery' => 0,
					'shipping_cutoff_time' => '',
					'exclude_delivery_on' => '',
					'exclude_delivery_day' => '',
				],
			]
		];

		require OEDW_PLUGIN_DIR.'includes/helpers/define.php';
		foreach ($validates_message as $name => $message) {
			$settings[$name] = $message['value'];
		}

		return $settings;
	}

	public function oedw_get_settings_data() {
		$settings = get_option(OEDW_SETTINGS_KEY, wp_json_encode($this->oedw_get_settings_default()));
		return $settings;
	}

	public function prepare_template_export() {
		$settings = $this->oedw_get_settings_data();
		
		$file_data = [
			'name' => 'oedw-data-settings-' . gmdate( 'Y-m-d' ) . '.json',
			'content' =>  $settings,
		];

		return $file_data;
	}
}
