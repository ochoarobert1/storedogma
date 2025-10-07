<?php
if (!defined('ABSPATH')) {
    exit;
}

class Dogma_Ajax_Handler
{

    public function __construct()
    {
        add_action('wp_ajax_get_customers_for_product', array($this, 'get_customers_for_product'));
        add_action('wp_ajax_get_customers_emails', array($this, 'get_customers_emails'));
        add_action('wp_ajax_send_custom_email', array($this, 'send_custom_email'));
    }

    public function get_customers_emails()
    {
        $product_id = intval($_POST['product_id']);

        $emails = $this->database_get_product_customers($product_id);

        if ($emails) {
            echo implode(', ', $emails);
        }
        wp_die();
    }

    public function get_customers_for_product()
    {
        $product_id = intval($_POST['product_id']);

        $emails = $this->database_get_product_customers($product_id);

        if (empty($emails)) {
            echo '<p>No customers found for this product.</p>';
            wp_die();
        }

        echo '<h3>Recipients (' . count($emails) . '):</h3>';
        echo '<ul>';
        foreach ($emails as $email) {
            echo '<li>' . esc_html($email) . '</li>';
        }
        echo '</ul>';

        wp_die();
    }

    public function database_get_product_customers($product_id)
    {
        global $wpdb;
        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses = array_map(
            function ($status) {
                return "wc-$status";
            },
            $statuses
        );

        $customer_emails = $wpdb->get_col("
SELECT DISTINCT o.billing_email
FROM {$wpdb->prefix}wc_order_product_lookup AS lookup
INNER JOIN {$wpdb->prefix}wc_orders AS o ON lookup.order_id = o.id
WHERE o.status IN ( '" . implode("','", $statuses) . "' )
AND ( lookup.product_id = $product_id OR lookup.variation_id = $product_id )
   ");

        if ($customer_emails) return $customer_emails;
        return false;
    }

    public function send_custom_email()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        if (!wp_verify_nonce($_POST['nonce'], 'dogma_email_nonce')) {
            wp_die('Security check failed');
        }

        $product_id = intval($_POST['product_id']);
        $emails = $this->database_get_product_customers($product_id);



        $title = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']);

        ob_start();
        $logo = DogmaEmailSender::get_plugin_url() . 'assets/img/logo-email.png';
        require_once DogmaEmailSender::get_plugin_dir() . 'includes/template-contact-email.php';
        $body = ob_get_clean();
        $body = str_replace([
            '{title}',
            '{message}',
            '{logo}'
        ], [
            $title,
            $message,
            $logo
        ], $body);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . esc_html(get_bloginfo('name')) . ' <noreply@' . strtolower($_SERVER['SERVER_NAME']) . '>'
        );

        $sent_count = 0;
        $batch_size = 10;
        $delay = 2;

        foreach (array_chunk($emails, $batch_size) as $batch) {
            foreach ($batch as $email) {
                if (wp_mail($email, $title, $body, $headers)) {
                    $sent_count++;
                }
                usleep(200000); // 0.2 second delay between emails
            }
            if ($sent_count < count($emails)) {
                sleep($delay); // 2 second delay between batches
            }
        }

        echo '<div class="notice notice-success"><p>Successfully sent ' . $sent_count . ' emails out of ' . count($emails) . ' recipients.</p></div>';

        wp_die();
    }
}
