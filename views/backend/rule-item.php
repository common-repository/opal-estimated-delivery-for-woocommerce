<?php
/** 
 * OEDW Rule Item Block
 * 
 * @uses rule_item
 * @uses index
 * @uses date_format
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    

$rule_apply_for = !empty($rule_item['rule_apply_for']) ? $rule_item['rule_apply_for'] : '';
$rule_apply_select_val = !empty($rule_item['rule_apply_select_val']) ? $rule_item['rule_apply_select_val'] : '';
$shipping_method = !empty($rule_item['shipping_method']) ? $rule_item['shipping_method'] : '';
$shipping_min_day = !empty($rule_item['shipping_min_day']) ? $rule_item['shipping_min_day'] : '';
$shipping_max_day = !empty($rule_item['shipping_max_day']) ? $rule_item['shipping_max_day'] : '';
$shipping_date = !empty($rule_item['shipping_date']) ? $rule_item['shipping_date'] : '';
$display_on_product = !empty($rule_item['display_on_product']) ? $rule_item['display_on_product'] : '';
$detail_delivery = !empty($rule_item['detail_delivery']) ? $rule_item['detail_delivery'] : '';
$shipping_cutoff_time = !empty($rule_item['shipping_cutoff_time']) ? $rule_item['shipping_cutoff_time'] : '';
$exclude_delivery_on = !empty($rule_item['exclude_delivery_on']) ? $rule_item['exclude_delivery_on'] : '';
$exclude_delivery_day = !empty($rule_item['exclude_delivery_day']) ? $rule_item['exclude_delivery_day'] : '';

$select_val = [];
if (!empty($rule_apply_select_val)) {
    foreach ($rule_apply_select_val as $id) {
        switch ($rule_apply_for) {
            case 'product':
                $select_val[$id] = get_the_title($id);
                break;
            case 'category':
                $term = get_term($id);
                $select_val[$id] = (is_wp_error($term) || !$term) ? $id : $term->name;
                break;
            case 'tag':
                $term = get_term($id);
                $select_val[$id] = (is_wp_error($term) || !$term) ? $id : $term->name;
                break;
            case 'shipping_class':
                $term = get_term($id);
                $select_val[$id] = (is_wp_error($term) || !$term) ? $id : $term->name;
                break;
            default:
                $types = wc_get_product_types();
                if ($types && isset($types[$id])) {
                    $select_val[$id] = $types[$id];
                }
                break;
        }
    }
}
?>

<ul class="oedw_rules_box">
    <li class="option_item oedw_group_settings_mt">
    <?php
        woocommerce_wp_select(
            array(
                'id'          => 'rule_apply_for_'.$index,
                'value'       => $rule_apply_for,
                'label'       => __( 'Rule aplly for:', 'opal-estimated-delivery-for-woocommerce' ),
                'options'     => [
                    'all' => __( 'All product', 'opal-estimated-delivery-for-woocommerce' ),
                    'instock' => __( 'In stock', 'opal-estimated-delivery-for-woocommerce' ),
                    'outofstock' => __( 'Out of stock', 'opal-estimated-delivery-for-woocommerce' ),
                    'onbackorder' => __( 'On backorder', 'opal-estimated-delivery-for-woocommerce' ),
                    'product' => __( 'Some products', 'opal-estimated-delivery-for-woocommerce' ),
                    'category' => __( 'Product category', 'opal-estimated-delivery-for-woocommerce' ),
                    'tag' => __( 'Product tag', 'opal-estimated-delivery-for-woocommerce' ),
                    'type' => __( 'Product type', 'opal-estimated-delivery-for-woocommerce' ),
                    'shipping_class' => __( 'Shipping class', 'opal-estimated-delivery-for-woocommerce' ),
                ],
                'wrapper_class' => 'oedw_setting_form', 
                'class' => 'oedw_setting_field oedw_rule_apply_for',
                'style' => 'width:100%;margin-left:0',
                'custom_attributes' => [
                    'data-pattern-name' => 'rule_apply_for_++',
                    'data-pattern-id' => 'rule_apply_for_++',
                ]
            )
        );

        $wrap_class = 'oedw_setting_form oedw_field_nolabel oedw_wrapper_rules_apply';
        if (in_array($rule_apply_for, ['all', 'instock', 'outstock', 'backorder', ''])) {
            $wrap_class .= ' oedw_hidden';
        }
        woocommerce_wp_select(
            array(
                'id'          => 'rule_apply_select_val_'.$index.'[]',
                'value'       => $rule_apply_select_val,
                'options'     => $select_val,
                'wrapper_class' => $wrap_class, 
                'label' => '',
                'class' => 'oedw_setting_field oedw_rules_apply oedw_init_select2',
                'style' => 'width:95%;margin-left:0',
                'custom_attributes' => [
                    'data-pattern-name' => 'rule_apply_select_val_++[]',
                    'data-pattern-id' => 'rule_apply_select_val_++[]',
                    'multiple' => "multiple",
                    'data-placeholder' => __( 'Typing to select', 'opal-estimated-delivery-for-woocommerce' ),
                ]
            )
        );
    ?>
    </li>
    <li class="option_item oedw_group_settings_mt">
    <?php
        woocommerce_wp_select(
            array(
                'id'          => 'shipping_method_'.$index,
                'value'       => $shipping_method,
                'label'       => __( 'Shipping Method:', 'opal-estimated-delivery-for-woocommerce' ),
                'options'     => oedw_get_available_shipping_methods(),
                'wrapper_class' => 'oedw_setting_form', 
                'class' => 'oedw_setting_field',
                'style' => 'width:100%;margin-left:0',
                'custom_attributes' => [
                    'data-pattern-name' => 'shipping_method_++',
                    'data-pattern-id' => 'shipping_method_++',
                ]
            )
        );
    ?>
    </li>
    <li class="option_item oedw_group_settings_mt">
    <?php
        woocommerce_wp_text_input(
            array(
                'id'          => 'shipping_min_day_'.$index,
                'type'      => 'number',
                'class' => 'oedw_setting_field',
                'wrapper_class' => 'oedw_setting_form', 
                'label'       => esc_html__( 'Minimum (days):', 'opal-estimated-delivery-for-woocommerce' ),
                'placeholder' => '',
                'value'       => $shipping_min_day,
                'custom_attributes' => [
                    'min' => '0',
                    'data-pattern-name' => 'shipping_min_day_++',
                    'data-pattern-id' => 'shipping_min_day_++',
                ]
            )
        );
    ?>
    </li>
    <li class="option_item oedw_group_settings_mt">
    <?php
        woocommerce_wp_text_input(
            array(
                'id'          => 'shipping_max_day_'.$index,
                'type'      => 'number',
                'class' => 'oedw_setting_field',
                'wrapper_class' => 'oedw_setting_form', 
                'label'       => esc_html__( 'Maximum (days):', 'opal-estimated-delivery-for-woocommerce' ),
                'placeholder' => '',
                'value'       => $shipping_max_day,
                'custom_attributes' => [
                    'min' => '0',
                    'data-pattern-name' => 'shipping_max_day_++',
                    'data-pattern-id' => 'shipping_max_day_++',
                ]
            )
        );
    ?>
    </li>
    <li class="option_item oedw_group_settings_mt oedw_hidden">
    <?php
        // woocommerce_wp_text_input(
        //     array(
        //         'id'          => 'shipping_date_'.$index,
        //         'class' => 'oedw_setting_field oedw_datetime_picker',
        //         'type'      => 'date',
        //         'wrapper_class' => 'oedw_setting_form', 
        //         'label'       => esc_html__( 'Scheduled delivery date:', 'opal-estimated-delivery-for-woocommerce' ),
        //         'placeholder' => esc_html__( 'Select a day', 'opal-estimated-delivery-for-woocommerce' ),
        //         'value'       => $shipping_date,
        //         'custom_attributes' => [
        //             'data-pattern-name' => 'shipping_date_++',
        //             'date-format' => $date_format
        //         ]
        //     )
        // );
    ?>
    </li>
    <li class="option_item oedw_group_settings_mt">
    <?php
        // oedw_wp_checkbox( array( 
        //     'wrapper_class' => 'oedw_setting_form oedw_flex_row_reverse oedw_flex_align_items_center', 
        //     'id' => 'display_on_product_'.$index,
        //     'class' => 'oedw_setting_field',
        //     'label' => esc_html__('Display Estimate on Product Page', 'opal-estimated-delivery-for-woocommerce'),
        //     'value' => $display_on_product,
        //     'cbvalue' => 1,
        //     'checkbox_ui' => true,
        //     'custom_attributes' => [
        //         'data-pattern-name' => 'display_on_product_++',
        //         'data-pattern-id' => 'display_on_product_++',
        //     ]
        // ) );
    ?>
    </li>
    <li class="option_item oedw_group_settings_mt">
        <div class="options_group_condition">
            <div class="option_item">
                <?php
                oedw_wp_checkbox( array( 
                    'wrapper_class' => 'oedw_setting_form oedw_flex_row_reverse oedw_flex_align_items_center', 
                    'id' => 'detail_delivery_'.$index,
                    'class' => 'oedw_setting_field',
                    'label' => esc_html__('Detail Delivery', 'opal-estimated-delivery-for-woocommerce'),
                    'description' => esc_html__('Add additional conditions to the rule', 'opal-estimated-delivery-for-woocommerce'),
                    'value' => $detail_delivery,
                    'cbvalue' => 1,
                    'checkbox_ui' => true,
                    'custom_attributes' => [
                        'data-pattern-name' => 'detail_delivery_++',
                        'data-pattern-id' => 'detail_delivery_++',
                    ]
                ) );
                ?>
            </div>
        </div>
        <div class="option_item oedw_group_settings_mt oedw_group_settings_condition toggle_hidden" style="<?php if(!$detail_delivery) echo esc_attr('display: none')  ?>">
            <ul>
                <li>
                <?php
                oedw_wp_text_input(
                    array(
                        'id'          => 'shipping_cutoff_time_'.$index,
                        'class' => 'oedw_setting_field oedw_datetime_picker',
                        'type'      => 'time',
                        'wrapper_class' => 'oedw_setting_form', 
                        'label'       => esc_html__( 'Shipping Cutoff Time:', 'opal-estimated-delivery-for-woocommerce' ),
                        'placeholder'       => esc_html__( 'Choose time', 'opal-estimated-delivery-for-woocommerce' ),
                        'value'       => $shipping_cutoff_time,
                        'custom_attributes' => [
                            'data-pattern-name' => 'shipping_cutoff_time_++',
                            'data-pattern-id' => 'shipping_cutoff_time_++',
                        ]
                    )
                );
                ?>
                </li>
                <li>
                    <h4 class=""><?php esc_html_e('Exclude Shipping on', 'opal-estimated-delivery-for-woocommerce') ?></h4>
                    <div class="oedw-flex oedw_child_settings">
                        <?php
                        $edo = $exclude_delivery_on;
                        $edo = (!$edo) ? array() : $edo;
                        woocommerce_wp_select(
                            array(
                                'id'          => 'exclude_delivery_on_'.$index.'[]',
                                'value'       => $edo,
                                'options'     => [
                                    'monday' => __('Monday', 'opal-estimated-delivery-for-woocommerce'), 
                                    'tuesday' => __('Tuesday', 'opal-estimated-delivery-for-woocommerce'), 
                                    'wednesday' => __('Wednesday', 'opal-estimated-delivery-for-woocommerce'), 
                                    'thursday' => __('Thursday', 'opal-estimated-delivery-for-woocommerce'), 
                                    'friday' => __('Friday', 'opal-estimated-delivery-for-woocommerce'), 
                                    'saturday' => __('Saturday', 'opal-estimated-delivery-for-woocommerce'), 
                                    'sunday' => __('Sunday', 'opal-estimated-delivery-for-woocommerce'),
                                    'holiday' => __('Holiday', 'opal-estimated-delivery-for-woocommerce'),
                                ],
                                'label' => '',
                                'wrapper_class' => 'oedw_setting_form oedw_field_nolabel', 
                                'class' => 'oedw_setting_field oedw_init_select2',
                                'style' => 'width:95%;margin-left:0',
                                'custom_attributes' => [
                                    'data-pattern-name' => 'exclude_delivery_on_++[]',
                                    'data-pattern-id' => 'exclude_delivery_on_++[]',
                                    'multiple' => "multiple",
                                    'data-placeholder' => __( 'Choosing weekdays', 'opal-estimated-delivery-for-woocommerce' ),
                                ]
                            )
                        );
                        ?>
                    </div>
                    <div><?php esc_html_e('Or', 'opal-estimated-delivery-for-woocommerce') ?></div>
                    <div class="oedw_child_settings">
                        <?php
                            woocommerce_wp_text_input(
                                array(
                                    'id'          => 'exclude_delivery_day_'.$index,
                                    'class' => 'oedw_setting_field oedw_datetime_picker',
                                    'type'      => 'date',
                                    'wrapper_class' => 'oedw_setting_form', 
                                    'label'       => esc_html__( 'Custom Exclude Days:', 'opal-estimated-delivery-for-woocommerce' ),
                                    'placeholder' => esc_html__( 'Select some days', 'opal-estimated-delivery-for-woocommerce' ),
                                    'value'       => $exclude_delivery_day,
                                    'custom_attributes' => [
                                        'data-pattern-name' => 'exclude_delivery_day_++',
                                        'data-pattern-id' => 'exclude_delivery_day_++',
                                        'mode' => 'multiple',
                                        'date-format' => $date_format
                                    ]
                                )
                            );
                        ?>
                    </div>
                </li>
            </ul>
        </div>
    </li>
    <div class="rule_action_btn repeater_btn"><a href="javascript:void(0)" class="rpt_btn_remove"><i class="dashicons dashicons-no-alt"></i></a></div>
</ul>