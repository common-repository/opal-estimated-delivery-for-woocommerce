<?php
/** 
 * OEDW Settings Page
 * 
 * @uses settings
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    

$fb_class = '';
?>
<div class="wrap">
    <div class="oedw_header_settings">
        <h2 class="oedw_title_page"><?php esc_html_e('Settings', 'opal-estimated-delivery-for-woocommerce') ?></h2>
        <h3 class="oedw_subtitle_page"><?php esc_html_e('Estimated delivery date', 'opal-estimated-delivery-for-woocommerce') ?></h3>
    </div>
</div>
<div class="wrap oedw_wrap_settings">
    <ul class="oedw_g_set_tabs <?php echo esc_html($fb_class); ?>">
        <li>
            <a href="#oedw_display_settings" class="active">
                <img src="<?php echo esc_url(OEDW_PLUGIN_URL.'/assets/images/display-settings.svg') ?>" width="20" height="20" alt=""><?php esc_html_e('Display Settings', 'opal-estimated-delivery-for-woocommerce'); ?>
            </a>
        </li>
        <li>
            <a href="#oedw_rules_settings">
                <img src="<?php echo esc_url(OEDW_PLUGIN_URL.'/assets/images/other-settings.svg') ?>" width="20" height="20" alt=""><?php esc_html_e('Rules', 'opal-estimated-delivery-for-woocommerce'); ?>
            </a>
        </li>
        <li>
            <a href="#oedw_import_export">
                <img src="<?php echo esc_url(OEDW_PLUGIN_URL.'/assets/images/backup-settings.svg') ?>" width="20" height="20" alt=""><?php esc_html_e('Import/Export Settings', 'opal-estimated-delivery-for-woocommerce'); ?>
            </a>
        </li>
    </ul>
    <div class="oedw_g_set_tabcontents <?php echo esc_html($fb_class); ?>">
        <div class="oedw_wrap_tabcontent">
            <div id="oedw_display_settings" class="oedw_tabcontent">
                <div class="options_group">
                    <h3><?php esc_html_e('Display Settings', 'opal-estimated-delivery-for-woocommerce') ?></h3>
                    <ul>
                        <li class="option_item oedw_group_settings_mt">
                        <?php
                            woocommerce_wp_select(
                                array(
                                    'id'          => 'product_render_position',
                                    'value'       => oedw_get_option('product_render_position', '', $settings),
                                    'label'       => __( 'Position on single product ', 'opal-estimated-delivery-for-woocommerce' ),
                                    'options'     => array(
                                        '' => __( 'Hidden', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_before_add_to_cart_button-10' => __( 'Before "Add to cart" button - 10', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_after_add_to_cart_quantity-10'  => __( 'Before "Add to cart" button - 10', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_after_add_to_cart_button-5'  => __( 'After "Add to cart" button - 5', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_before_add_to_cart_quantity-5'  => __( 'Before "Quantity" - 5', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_after_add_to_cart_quantity-5'  => __( 'After "Quantity" - 5', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_before_variations_form-5'  => __( 'Before "Variation fields" (Only Variable Products) - 5', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_after_variations_table-5'  => __( 'After "Variation fields" (Only Variable Products) - 5', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-4'  => __( 'Before "Title" - 4', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-5'  => __( 'After "Title" - 5', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-19'  => __( 'Before "Excerpt" - 19', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-20'  => __( 'After "Excerpt" - 20', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-9'  => __( 'Before "Price" - 9', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-10'  => __( 'After "Price" - 10', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-29'  => __( 'Before "Add to cart" form - 29', 'opal-estimated-delivery-for-woocommerce' ),
                                        'woocommerce_single_product_summary-30'  => __( 'After "Add to cart" form - 30', 'opal-estimated-delivery-for-woocommerce' ),
                                    ),
                                    'wrapper_class' => 'oedw_setting_form', 
                                    'class' => 'oedw_setting_field',
                                    'style' => 'width:100%;margin-left:0'
                                )
                            );
                        ?>
                        </li>
                        <li>
                        <?php
                            woocommerce_wp_text_input(
                                array(
                                    'id'          => 'render_position_prioty',
                                    'class' => 'oedw_setting_field',
                                    'wrapper_class' => 'oedw_setting_form',
                                    'label'       => esc_html__( 'Prioty: ', 'opal-estimated-delivery-for-woocommerce' ),
                                    'placeholder' => '5',
                                    'value'       => oedw_get_option('render_position_prioty', '', $settings),
                                    'style' => 'display: block'
                                )
                            );
                        ?>
                        </li>
                        <li>
                            <?php
                            oedw_wp_checkbox( array( 
                                'wrapper_class' => 'oedw_setting_form oedw_flex_row_reverse oedw_flex_align_items_center', 
                                'id' => 'show_estimate_shipping_method',
                                'class' => 'oedw_setting_field',
                                'label' => esc_html__('Show for shipping method', 'opal-estimated-delivery-for-woocommerce'),
                                'value' => oedw_get_option('show_estimate_shipping_method', 0, $settings),
                                'description' => esc_html__('Show estimated time for each shipping method on single product', 'opal-estimated-delivery-for-woocommerce'),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                        <li>
                            <?php
                            oedw_wp_checkbox( array( 
                                'wrapper_class' => 'oedw_setting_form oedw_flex_row_reverse oedw_flex_align_items_center', 
                                'id' => 'show_in_cart_item',
                                'class' => 'oedw_setting_field',
                                'label' => esc_html__('Show in cart item', 'opal-estimated-delivery-for-woocommerce'),
                                'value' => oedw_get_option('show_in_cart_item', 0, $settings),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                        <li>
                            <?php
                            // oedw_wp_checkbox( array( 
                            //     'wrapper_class' => 'oedw_setting_form oedw_flex_row_reverse oedw_flex_align_items_center', 
                            //     'id' => 'show_in_cart_total',
                            //     'class' => 'oedw_setting_field',
                            //     'label' => esc_html__('Show in cart total', 'opal-estimated-delivery-for-woocommerce'),
                            //     'value' => oedw_get_option('show_in_cart_total', 0, $settings),
                            //     'cbvalue' => 1,
                            //     'checkbox_ui' => true
                            // ) );
                            ?>
                        </li>
                        <li>
                            <?php
                            oedw_wp_checkbox( array( 
                                'wrapper_class' => 'oedw_setting_form oedw_flex_row_reverse oedw_flex_align_items_center', 
                                'id' => 'show_in_order',
                                'class' => 'oedw_setting_field',
                                'label' => esc_html__('Show in order', 'opal-estimated-delivery-for-woocommerce'),
                                'value' => oedw_get_option('show_in_order', 0, $settings),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                        <li class="option_item oedw_group_settings_mt">
                        <?php
                            $list_format = ['Y/m/d', 'd/m/Y', 'm/d/y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'm-d-y', 'Y.m.d', 'd.m.Y', 'm.d.y', 'F j, Y', 'M j, Y', 'jS \of F', 'jS F', 'j. F', 'l j. F', 'F jS', 'jS M', 'M jS'];
                            $options = [];
                            foreach ($list_format as $format) {
                                $options[$format] = gmdate($format);
                            }
                            $date_format = oedw_get_option('date_format', 'Y/m/d', $settings);
                            woocommerce_wp_select(
                                array(
                                    'id'          => 'date_format',
                                    'value'       => $date_format,
                                    'label'       => __( 'Date format', 'opal-estimated-delivery-for-woocommerce' ),
                                    'options'     => $options,
                                    'wrapper_class' => 'oedw_setting_form', 
                                    'class' => 'oedw_setting_field',
                                    'style' => 'width:100%;margin-left:0'
                                )
                            );
                        ?>
                        </li>
                        <li class="option_item oedw_group_settings_mt">
                        <?php
                            $holiday_format = str_replace('Y', '*', $date_format);
                            $holiday_days = [];
                            for ($i = 0; $i <= 365; $i++) {
                                $timestamp = strtotime("+$i day", strtotime('January 1st'));
                                $date = gmdate('Y-m-d', $timestamp);
            
                                $holiday_days[gmdate('*-m-d', $timestamp)] = gmdate($holiday_format, $timestamp);
                            }
                            woocommerce_wp_select(
                                array(
                                    'id'          => 'holidays[]',
                                    'value'       => oedw_get_option('holidays', '', $settings),
                                    'options'     => $holiday_days,
                                    'label'       => __( 'Holidays', 'opal-estimated-delivery-for-woocommerce' ),
                                    'wrapper_class' => 'oedw_setting_form', 
                                    'class' => 'oedw_setting_field oedw_init_select2',
                                    'style' => 'width:95%;margin-left:0',
                                    'custom_attributes' => [
                                        'multiple' => "multiple",
                                        'data-placeholder' => __( 'Choosing holidays', 'opal-estimated-delivery-for-woocommerce' ),
                                    ]
                                )
                            );
                        ?>
                        </li>
                    </ul>
                </div>
                <div class="options_group">
                    <h3><?php esc_html_e('Shortcode', 'opal-estimated-delivery-for-woocommerce') ?></h3>
                    <p>
                        <?php  
                        echo wp_kses('You can use shortcode <code>[oedw]</code> to show the estimated delivery date for current product.', ['code' => []]);
                        ?>
                    </p>
                    <p>
                        <?php  
                        echo wp_kses('Or you can also use the product id in the shortcode to show the estimated delivery date for a specific product. For example:<code>[oedw id="123"]</code>', ['code' => []]);
                        ?>
                    </p>
                </div>
                <div class="options_group">
                    <h3><?php esc_html_e('Contents/Strings', 'opal-estimated-delivery-for-woocommerce') ?></h3>
                    <ul>
                        <?php
                        require OEDW_PLUGIN_DIR.'includes/helpers/define.php';

                        $validates_message = apply_filters('oedw_validates_message_custom', $validates_message);
                        foreach ($validates_message as $name => $message) {
                            ?>
                            <li>
                            <?php
                            woocommerce_wp_text_input(
                                array(
                                    'id'            => $name,
                                    'class' => 'oedw_setting_field',
                                    'wrapper_class' => 'oedw_setting_form', 
                                    'label'         => isset($message['label']) ? $message['label'] : '',
                                    'value'         => oedw_get_option($name, '', $settings),
                                    'placeholder'   => isset($message['placeholder']) ? $message['placeholder'] : '',
                                    'description'   => isset($message['description']) ? $message['description'] : '',
                                    'desc_tip'      => isset($message['desc_tip']) ? $message['desc_tip'] : false,
                                    'type'          => isset( $message['type'] ) ? $message['type'] : 'text',
                                    'data_type'     => isset( $message['data_type'] ) ? $message['data_type'] : '',
                                )
                            );
                            ?>
                            <li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div id="oedw_rules_settings" class="oedw_tabcontent" style="display: none;">
                <div class="options_group">
                    <h3><?php esc_html_e('Rules', 'opal-estimated-delivery-for-woocommerce') ?></h3>
                    <div class="oedw_wrapper_rules">
                        <?php
                        $rules_data = oedw_get_option('rule_apply_data', [], $settings);
                        if (!empty($rules_data) && is_array($rules_data)) {
                            foreach ($rules_data as $i => $rule_item) {
                                OEDW_Admin::view('rule-item', ['rule_item' => $rule_item, 'index' => $i, 'date_format' => $date_format]);
                            }
                        }
                        else {
                            OEDW_Admin::view('rule-item', ['rule_item' => [], 'index' => 0, 'date_format' => $date_format]);
                        }
                        ?>
                        <nav class="repeater_btn oedw-flex oedw_flex_justify_content_end"><a href="javascript:void(0)" class="button rpt_btn_add"><?php esc_html_e('+ Add Rule', 'opal-estimated-delivery-for-woocommerce') ?></a></nav>
                    </div>
                </div>
            </div>
            <div id="oedw_import_export" class="oedw_tabcontent" style="display: none;">
                <div class="options_group">
                    <div class="oedw_group_option">
                        <img src="<?php echo esc_url(OEDW_PLUGIN_URL.'/assets/images/download-solid.svg') ?>" width="50" alt="">
                        <div>
                            <h3><?php esc_html_e('Export Settings', 'opal-estimated-delivery-for-woocommerce') ?></h3>
                            <p><?php esc_html_e('Download a backup file of your settings', 'opal-estimated-delivery-for-woocommerce') ?></p>
                        </div>
                    </div>
                    <div class="oedw_action_button">
                        <a href="<?php echo esc_url(admin_url( 'admin-ajax.php' ).'?action=oedw_settings_export&ajax_nonce_parameter='.wp_create_nonce( "oedw-nonce-ajax" )); ?>" id="oedw_download_settings" class="button button-primary"><?php esc_html_e('Download settings', 'opal-estimated-delivery-for-woocommerce') ?></a>
                    </div>
                </div>
                <form id="oedw-form-import-settings" class="options_group" method="post" action="<?php echo esc_url(admin_url( 'admin-ajax.php' )) ?>" enctype="multipart/form-data">
                    <div class="oedw_group_option">
                        <img src="<?php echo esc_url(OEDW_PLUGIN_URL.'/assets/images/file-import-solid.svg') ?>" width="50" alt="">
                        <div>
                            <h3><?php esc_html_e('Import Settings', 'opal-estimated-delivery-for-woocommerce') ?></h3>
                            <fieldset id="oedw-import-form-settings">
                                <input type="hidden" name="action" value="oedw_handle_import_settings">
                                <?php wp_nonce_field('oedw-nonce-ajax', 'ajax_nonce_parameter');  ?>
                                <div class="oedw_field_wrap">
                                    <input type="file" name="oedw_setting_import" accept=".json,application/json" required="">
                                </div>
                                <p class="oedw_notice"><?php esc_html_e('*Notice: All existing settings will be overwritten', 'opal-estimated-delivery-for-woocommerce') ?></p>
                            </fieldset>
                        </div>
                    </div>
                    <div class="oedw_action_button">
                        <button id="oedw_import_settings" class="button button-primary"><?php esc_html_e('Upload file and import settings', 'opal-estimated-delivery-for-woocommerce') ?></a>
                    </div>
                </form>
            </div>
        </div>
        <div class="oedw_setting_action mt">
            <input type="hidden" name="action" value="oedw_handle_settings_form">
            <?php wp_nonce_field('oedw-nonce-ajax', 'ajax_nonce_parameter');  ?>
            <button type="button" id="oedw_submit_settings" class="button"><?php esc_html_e('Save settings', 'opal-estimated-delivery-for-woocommerce') ?></button>
        </div>
    </div>
    <div style="clear: both"></div>
</div>
