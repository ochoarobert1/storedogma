<?php
if (!defined('ABSPATH')) {
    exit;
}

class Dogma_WooCommerce_Hooks {
    
    public function __construct() {
        add_action('woocommerce_order_status_completed', array($this, 'capture_customer_email'));
    }
    
    public function capture_customer_email($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        $customer_email = $order->get_billing_email();
        
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            
            Dogma_Database::save_customer_email($customer_email, $product_id, $order_id);
        }
    }
}