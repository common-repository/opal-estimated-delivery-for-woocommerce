<?php
/** 
 * OEDW Estimate For Each Shipping Method
 * 
 * @uses est_method_data
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   

// oedw_p($est_method_data);
?>
<div class="oedw-est-shipping-method" style="display: none;">
    <ul class="oedw-list-zones">
        <?php
        foreach ($est_method_data as $zone) {
            ?>
            <li class="oedw-item-zone">
                <h4 class="oedw-zone-name"><strong><?php __('Zone:', 'opal-estimated-delivery-for-woocommerce') ?></strong><?php echo esc_html($zone['zone_name']) ?></h4>
                <ol class="oedw-list-methods">
                <?php
                foreach ($zone['method_detail'] as $method) {
                    $est_date = $method['method_est'];
                    $est_data = '';
                    if (!empty($est_date['min_date'])) {
                        $est_data .= $est_date['min_date'];
                    }
                    if (!empty($est_date['min_date']) && !empty($est_date['max_date'])) $est_data .= ' ~ ';
                    if (!empty($est_date['max_date'])) {
                        $est_data .= $est_date['max_date'];
                    }
                    
                    if (empty($est_data)) {
                        $est_data = apply_filters('oedw_no_est_date_for_method', __('There is no estimated delivery date for this method!', 'opal-estimated-delivery-for-woocommerce'));
                    }
                    ?>
                    <li><?php echo wp_kses_post(apply_filters('oedw_est_date_for_method', sprintf('%1s: %2s', $method['method_title'], $est_data), $est_data)) ?></li>
                    <?php
                }
                ?>
                </ol>
            </li>
            <?php
        }
        ?>
    </ul>
</div>