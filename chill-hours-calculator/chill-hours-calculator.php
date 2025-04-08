<?php
/**
 * Plugin Name: Chill Hours Calculator
 * Plugin URI: 
 * Description: A WordPress plugin that calculates chill hours based on ZIP codes using three different methods.
 * Version: 1.0.0
 * Author: 
 * Text Domain: chill-hours-calculator
 * Domain Path: /languages
 * 
 * @package ChillHoursCalculator
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('CHILL_HOURS_CALCULATOR_VERSION', '1.0.0');
define('CHILL_HOURS_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CHILL_HOURS_CALCULATOR_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The core plugin class.
 */
require_once CHILL_HOURS_CALCULATOR_PLUGIN_DIR . 'includes/class-chill-hours-calculator.php';

/**
 * Begins execution of the plugin.
 */
function run_chill_hours_calculator() {
    $plugin = new Chill_Hours_Calculator();
    $plugin->run();
}
run_chill_hours_calculator();

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'activate_chill_hours_calculator');
function activate_chill_hours_calculator() {
    // Create necessary folders if they don't exist
    $upload_dir = wp_upload_dir();
    $chill_hours_dir = $upload_dir['basedir'] . '/chill-hours';
    
    if (!file_exists($chill_hours_dir)) {
        wp_mkdir_p($chill_hours_dir);
    }
    
    // Set default options
    if (!get_option('chill_hours_calculator_settings')) {
        $default_settings = array(
            'csv_file' => '',
            'method_1_name' => 'Utah Model',
            'method_1_description' => 'Hours between 32°F and 45°F',
            'method_2_name' => 'California Model',
            'method_2_description' => 'Hours below 45°F',
            'method_3_name' => 'Dynamic Model',
            'method_3_description' => 'Weighted approach based on temperature thresholds'
        );
        update_option('chill_hours_calculator_settings', $default_settings);
    }
}

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, 'deactivate_chill_hours_calculator');
function deactivate_chill_hours_calculator() {
    // Nothing specific to do on deactivation
}
