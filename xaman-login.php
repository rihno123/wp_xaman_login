<?php
/**
 * Plugin Name: Xaman login
 * Plugin URI:  
 * Description: Provides Xaman wallet login functionality, send/receive functions and list of all tokens owned.
 * Version:     1.0
 * Author:      Lein
 */
require_once __DIR__ . '/vendor/autoload.php';

if(!defined('ABSPATH'))
{
    die('Nice try!');
}

if (!function_exists('add_action')) {
    echo 'This is a plugin for WordPress and cannot be called directly.';
    exit;
}


class XamanLoginPlugin {

    public function __construct() {
        $this->initHooks();
    }

    private function initHooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueueCustomScripts'));
        require_once plugin_dir_path(__FILE__) . 'includes/jQuery-handler.php';
        require_once plugin_dir_path(__FILE__) . 'includes/Settings.php';
        require_once plugin_dir_path(__FILE__) . 'includes/Form.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-login-handler.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-Rest-api.php';
    }

    public function enqueueCustomScripts() {
        wp_enqueue_script('jquery');
    }

}

new XamanLoginPlugin();
