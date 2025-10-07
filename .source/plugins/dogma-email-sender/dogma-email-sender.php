<?php

/**
 * Plugin Name: Dogma Email Sender
 * Description: Captures customer emails for specific products and allows sending customizable emails from admin
 * Version: 1.0.0
 * Author: Dogma Store
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-database.php';
require_once plugin_dir_path(__FILE__) . 'hooks/class-woocommerce-hooks.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-ajax-handler.php';

class DogmaEmailSender
{
    const NAME = 'dogma-email-sender';
    const VERSION = '1.0.0';
    const TEXT_DOMAIN = 'dogma-email-sender';

    public static function get_plugin_dir()
    {
        return plugin_dir_path(__FILE__);
    }

    public static function get_plugin_url()
    {
        return plugin_dir_url(__FILE__);
    }

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts($hook)
    {
        if ($hook !== 'toplevel_page_dogma-email-sender') return;

        wp_enqueue_style('dogma-email-sender', plugin_dir_url(__FILE__) . 'assets/css/dogma-email-sender.css', array(), self::VERSION);

        wp_enqueue_editor();
        wp_enqueue_script(
            'dogma-email-sender',
            DogmaEmailSender::get_plugin_url() . 'assets/js/dogma-email-sender.js',
            array('jquery', 'wp-editor'),
            DogmaEmailSender::VERSION,
            true
        );

        wp_localize_script('dogma-email-sender', 'dogmaEmailNonce', wp_create_nonce('dogma_email_nonce'));
    }

    public function init()
    {
        new Dogma_WooCommerce_Hooks();
        new Dogma_Admin_Page();
        new Dogma_Ajax_Handler();
    }
}

new DogmaEmailSender();
