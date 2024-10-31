<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    

$validates_message = [
    'est_date_content' => [
        'label' => esc_html__('Estimated date content', 'opal-estimated-delivery-for-woocommerce'),
        'value' => 'Estimated delivery dates: %s',
        'placeholder' => esc_html__('Content', 'opal-estimated-delivery-for-woocommerce'),
        /* translators: %s: Estimated dates. */
        'description' => esc_html__('{%s} - To display estimated dates', 'opal-estimated-delivery-for-woocommerce'),
        'desc_tip' => true,
    ],
];