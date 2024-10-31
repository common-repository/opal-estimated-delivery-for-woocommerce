<?php
/** 
 * OEDW Estimate block
 * 
 * @uses est_date
 * @uses est_content
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   

$est_data = '';
if (!empty($est_date['min_date'])) {
    $est_data .= $est_date['min_date'];
}
if (!empty($est_date['min_date']) && !empty($est_date['max_date'])) $est_data .= ' ~ ';
if (!empty($est_date['max_date'])) {
    $est_data .= $est_date['max_date'];
}

?>
<div class="oedw oedw-est-box" data-product="<?php echo esc_attr(get_the_ID()) ?>">
    <div class="oedw-est-content">
        <?php
        echo wp_kses_post(sprintf($est_content, apply_filters( 'oedw_est_date_for_product', $est_data )));
        ?>
    </div>
    <?php if(is_product()): ?>
        <a href="#" id="oedw_detail_zone" title="Detail"><?php esc_html_e('Zone detail', 'opal-estimated-delivery-for-woocommerce') ?></a>
    <?php endif; ?>
</div>