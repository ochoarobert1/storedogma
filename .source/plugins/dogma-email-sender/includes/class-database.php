<?php
if (!defined('ABSPATH')) {
    exit;
}

class Dogma_Database
{

    public static function get_customers_for_product($product_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'dogma_customer_emails';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT DISTINCT customer_email FROM $table_name WHERE product_id = %d",
            $product_id
        ));
    }

    public static function save_customer_email($customer_email, $product_id, $order_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'dogma_customer_emails';

        return $wpdb->replace(
            $table_name,
            array(
                'customer_email' => $customer_email,
                'product_id' => $product_id,
                'order_id' => $order_id
            ),
            array('%s', '%d', '%d')
        );
    }
}
