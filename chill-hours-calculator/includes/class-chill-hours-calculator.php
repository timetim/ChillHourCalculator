<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Chill_Hours_Calculator
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The core plugin class.
 */
class Chill_Hours_Calculator {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions = array();

    /**
     * The filters registered with WordPress.
     *
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters = array();

    /**
     * Initialize the plugin.
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        // Include data handler class
        require_once CHILL_HOURS_CALCULATOR_PLUGIN_DIR . 'includes/class-chill-hours-data.php';
        
        // Include admin class
        require_once CHILL_HOURS_CALCULATOR_PLUGIN_DIR . 'admin/admin-page.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        // Admin menu
        $this->add_action('admin_menu', $this, 'add_admin_menu');
        
        // Admin scripts and styles
        $this->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_scripts');
        
        // Register settings
        $this->add_action('admin_init', $this, 'register_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        // Register shortcode
        $this->add_action('init', $this, 'register_shortcodes');
        
        // Enqueue public scripts and styles
        $this->add_action('wp_enqueue_scripts', $this, 'enqueue_public_scripts');
        
        // AJAX handler for form submission
        $this->add_action('wp_ajax_get_chill_hours', $this, 'get_chill_hours_ajax');
        $this->add_action('wp_ajax_nopriv_get_chill_hours', $this, 'get_chill_hours_ajax');
    }

    /**
     * Register shortcodes.
     */
    public function register_shortcodes() {
        add_shortcode('chill_hours_calculator', array($this, 'shortcode_chill_hours_calculator'));
    }

    /**
     * Shortcode callback for [chill_hours_calculator].
     */
    public function shortcode_chill_hours_calculator($atts) {
        // Start output buffering
        ob_start();
        
        // Include form template
        require CHILL_HOURS_CALCULATOR_PLUGIN_DIR . 'templates/form-template.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Register the admin menu.
     */
    public function add_admin_menu() {
        add_options_page(
            'Chill Hours Calculator', 
            'Chill Hours Calculator', 
            'manage_options', 
            'chill-hours-calculator', 
            'chill_hours_calculator_admin_page'
        );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        register_setting('chill_hours_calculator_settings_group', 'chill_hours_calculator_settings');
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_chill-hours-calculator' !== $hook) {
            return;
        }
        
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-dialog');
    }

    /**
     * Enqueue public scripts and styles.
     */
    public function enqueue_public_scripts() {
        wp_enqueue_style('chill-hours-calculator-css', CHILL_HOURS_CALCULATOR_PLUGIN_URL . 'public/css/chill-hours-calculator.css', array(), CHILL_HOURS_CALCULATOR_VERSION);
        
        wp_enqueue_script('chill-hours-calculator-js', CHILL_HOURS_CALCULATOR_PLUGIN_URL . 'public/js/chill-hours-calculator.js', array('jquery'), CHILL_HOURS_CALCULATOR_VERSION, true);
        
        wp_localize_script('chill-hours-calculator-js', 'chillHoursAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('chill_hours_nonce')
        ));
    }

    /**
     * AJAX handler for getting chill hours.
     */
    public function get_chill_hours_ajax() {
        check_ajax_referer('chill_hours_nonce', 'security');
        
        $zip_code = isset($_POST['zip_code']) ? sanitize_text_field($_POST['zip_code']) : '';
        
        if (empty($zip_code)) {
            wp_send_json_error(array('message' => 'ZIP code is required.'));
        }
        
        // Validate ZIP code format
        if (!preg_match('/^\d{5}(-\d{4})?$/', $zip_code)) {
            wp_send_json_error(array('message' => 'Invalid ZIP code format. Please enter a valid 5-digit ZIP code.'));
        }
        
        // Get the 5-digit part of the ZIP code
        $zip_code = substr($zip_code, 0, 5);
        
        $data_handler = new Chill_Hours_Data();
        $chill_hours = $data_handler->get_chill_hours_by_zip($zip_code);
        
        if ($chill_hours === false) {
            wp_send_json_error(array('message' => 'No chill hours data found for this ZIP code.'));
        }
        
        // Get the settings
        $settings = get_option('chill_hours_calculator_settings');
        
        $response = array(
            'zip_code' => $zip_code,
            'method1' => array(
                'name' => $settings['method_1_name'],
                'description' => $settings['method_1_description'],
                'value' => $chill_hours['method1']
            ),
            'method2' => array(
                'name' => $settings['method_2_name'],
                'description' => $settings['method_2_description'],
                'value' => $chill_hours['method2']
            ),
            'method3' => array(
                'name' => $settings['method_3_name'],
                'description' => $settings['method_3_description'],
                'value' => $chill_hours['method3']
            ),
        );
        
        wp_send_json_success($response);
    }

    /**
     * Register an action with WordPress.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add_hook($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Register a filter with WordPress.
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add_hook($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     */
    private function add_hook($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );
        
        return $hooks;
    }

    /**
     * Run the plugin.
     */
    public function run() {
        // Register all actions
        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
        
        // Register all filters
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }
}
