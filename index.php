<?php
/**
 * Simple WordPress simulator for testing the Chill Hours Calculator plugin
 */

// Define the required WordPress constants
define('WPINC', 'wp-includes');
define('WP_CONTENT_URL', 'http://localhost:5000/wp-content');
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
define('ABSPATH', __DIR__ . '/');

// Create wp-content directory if it doesn't exist
if (!file_exists(__DIR__ . '/wp-content')) {
    mkdir(__DIR__ . '/wp-content', 0777, true);
}

// Create wp-content/uploads directory if it doesn't exist
if (!file_exists(__DIR__ . '/wp-content/uploads')) {
    mkdir(__DIR__ . '/wp-content/uploads', 0777, true);
}

// Create wp-content/uploads/chill-hours directory if it doesn't exist
if (!file_exists(__DIR__ . '/wp-content/uploads/chill-hours')) {
    mkdir(__DIR__ . '/wp-content/uploads/chill-hours', 0777, true);
}

// Setup wp_upload_dir function
function wp_upload_dir() {
    return array(
        'basedir' => __DIR__ . '/wp-content/uploads',
        'baseurl' => 'http://localhost:5000/wp-content/uploads',
        'path' => __DIR__ . '/wp-content/uploads',
        'url' => 'http://localhost:5000/wp-content/uploads'
    );
}

// Create a mock for admin_url function
function admin_url($path = '') {
    return 'http://localhost:5000/wp-admin/' . ltrim($path, '/');
}

// Define plugin_dir_path function
function plugin_dir_path($file) {
    return trailingslashit(dirname($file));
}

// Define plugin_dir_url function
function plugin_dir_url($file) {
    return 'http://localhost:5000/' . trailingslashit(dirname(str_replace(__DIR__, '', $file)));
}

// Define trailingslashit function
function trailingslashit($string) {
    return rtrim($string, '/') . '/';
}

// Define sanitize_text_field function
function sanitize_text_field($str) {
    return htmlspecialchars(strip_tags(trim($str)));
}

// Define wp_create_nonce function
function wp_create_nonce($action = -1) {
    return 'nonce_' . md5($action);
}

// Define check_ajax_referer function
function check_ajax_referer($action = -1, $query_arg = false) {
    return true; // Always pass for testing
}

// Define get_option function
function get_option($option, $default = false) {
    static $options = array();
    
    // Check if we need to initialize options
    if (empty($options)) {
        $options['chill_hours_calculator_settings'] = array(
            'csv_file' => __DIR__ . '/sample-chill-hours.csv',
            'method_1_name' => 'Utah Model',
            'method_1_description' => 'Hours between 32°F and 45°F',
            'method_2_name' => 'California Model',
            'method_2_description' => 'Hours below 45°F',
            'method_3_name' => 'Dynamic Model',
            'method_3_description' => 'Weighted approach based on temperature thresholds'
        );
    }
    
    if (isset($options[$option])) {
        return $options[$option];
    }
    
    return $default;
}

// Define update_option function
function update_option($option, $value) {
    static $options = array();
    $options[$option] = $value;
    return true;
}

// Define add_action and add_filter functions (do nothing for testing)
function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
    // No-op for testing
}

function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
    // No-op for testing
}

// Define register_activation_hook and register_deactivation_hook functions
function register_activation_hook($file, $callback) {
    // Call activation function
    call_user_func($callback);
}

function register_deactivation_hook($file, $callback) {
    // No-op for testing
}

// Define add_shortcode function
function add_shortcode($tag, $callback) {
    // No-op for testing
}

// Define add_options_page function
function add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback) {
    // No-op for testing
}

// Define wp_enqueue_style and wp_enqueue_script functions
function wp_enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all') {
    echo '<link rel="stylesheet" href="' . $src . '">' . PHP_EOL;
}

function wp_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false) {
    echo '<script src="' . $src . '"></script>' . PHP_EOL;
}

// Define wp_localize_script function
function wp_localize_script($handle, $object_name, $l10n) {
    echo '<script>' . PHP_EOL;
    echo 'var ' . $object_name . ' = ' . json_encode($l10n) . ';' . PHP_EOL;
    echo '</script>' . PHP_EOL;
}

// Define register_setting function
function register_setting($option_group, $option_name, $args = array()) {
    // No-op for testing
}

// Define submit_button function
function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) {
    echo '<input type="submit" name="' . $name . '" id="' . $name . '" class="button button-' . $type . '" value="' . ($text ? $text : 'Save Changes') . '">';
}

// Define wp_mkdir_p function
function wp_mkdir_p($dir) {
    return mkdir($dir, 0777, true);
}

// Define wp_nonce_field function
function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true) {
    $name = esc_attr($name);
    $nonce_field = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';
    
    if ($referer) {
        $nonce_field .= '<input type="hidden" name="_wp_http_referer" value="' . esc_attr($_SERVER['REQUEST_URI']) . '" />';
    }
    
    if ($echo) {
        echo $nonce_field;
    }
    
    return $nonce_field;
}

// Define esc_attr and esc_html functions
function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Define wp_send_json_success and wp_send_json_error functions
function wp_send_json_success($data = null) {
    $response = array('success' => true);
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function wp_send_json_error($data = null) {
    $response = array('success' => false);
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Define current_user_can function
function current_user_can($capability) {
    return true; // Always allow for testing
}

// Define is_wp_error function
function is_wp_error($thing) {
    return is_object($thing) && get_class($thing) === 'WP_Error';
}

// Define WP_Error class
class WP_Error {
    public $errors = array();
    public $error_data = array();
    
    public function __construct($code = '', $message = '', $data = '') {
        $this->errors[$code][] = $message;
        if ($data) {
            $this->error_data[$code] = $data;
        }
    }
    
    public function get_error_message($code = '') {
        if (empty($code)) {
            $code = array_keys($this->errors)[0];
        }
        
        return isset($this->errors[$code][0]) ? $this->errors[$code][0] : '';
    }
}

// Create a request handler to simulate AJAX functionality
if (isset($_POST['action']) && $_POST['action'] === 'get_chill_hours') {
    // Include plugin files
    require_once 'chill-hours-calculator/chill-hours-calculator.php';
    
    // Create plugin instance
    $plugin = new Chill_Hours_Calculator();
    
    // Call AJAX handler
    $plugin->get_chill_hours_ajax();
    exit;
}

// Include jQuery for the form to work
echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>' . PHP_EOL;

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_chill_hours') {
    require_once 'chill-hours-calculator/includes/class-chill-hours-data.php';
    $data_handler = new Chill_Hours_Data();
    $chill_hours = $data_handler->get_chill_hours_by_zip($_POST['zip_code']);
    
    if ($chill_hours === false) {
        echo json_encode(['success' => false, 'data' => ['message' => 'No data found for this ZIP code.']]);
    } else {
        $settings = get_option('chill_hours_calculator_settings', []);
        echo json_encode([
            'success' => true,
            'data' => [
                'zip_code' => $_POST['zip_code'],
                'method1' => [
                    'name' => isset($settings['method_1_name']) ? $settings['method_1_name'] : 'Utah Model',
                    'description' => isset($settings['method_1_description']) ? $settings['method_1_description'] : 'Hours between 32°F and 45°F',
                    'value' => $chill_hours['method1']
                ],
                'method2' => [
                    'name' => isset($settings['method_2_name']) ? $settings['method_2_name'] : 'California Model',
                    'description' => isset($settings['method_2_description']) ? $settings['method_2_description'] : 'Hours below 45°F',
                    'value' => $chill_hours['method2']
                ],
                'method3' => [
                    'name' => isset($settings['method_3_name']) ? $settings['method_3_name'] : 'Dynamic Model',
                    'description' => isset($settings['method_3_description']) ? $settings['method_3_description'] : 'Weighted approach based on temperature thresholds',
                    'value' => $chill_hours['method3']
                ]
            ]
        ]);
    }
    exit;
}

// Include the plugin
require_once 'chill-hours-calculator/chill-hours-calculator.php';

// Output the shortcode content
echo '<h1>Chill Hours Calculator Demo</h1>';
$plugin = new Chill_Hours_Calculator();
echo $plugin->shortcode_chill_hours_calculator(array());