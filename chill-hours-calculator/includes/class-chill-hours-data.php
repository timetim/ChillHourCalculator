<?php
/**
 * The class handling chill hours data.
 *
 * @since      1.0.0
 * @package    Chill_Hours_Calculator
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The chill hours data handler class.
 */
class Chill_Hours_Data {

    /**
     * CSV file path.
     *
     * @var string
     */
    private $csv_file;

    /**
     * Initialize the class.
     */
    public function __construct() {
        $settings = get_option('chill_hours_calculator_settings');
        $this->csv_file = isset($settings['csv_file']) ? $settings['csv_file'] : '';
    }

    /**
     * Get chill hours by ZIP code.
     *
     * @param string $zip_code The ZIP code to lookup.
     * @return array|false Chill hours data or false if not found.
     */
    public function get_chill_hours_by_zip($zip_code) {
        if (empty($this->csv_file) || !file_exists($this->csv_file)) {
            return false;
        }

        $handle = fopen($this->csv_file, 'r');
        
        if (!$handle) {
            return false;
        }
        
        $header = fgetcsv($handle);
        
        // Find the column indices
        $zip_index = array_search('zip_code', array_map('strtolower', $header));
        $method1_index = array_search('method1', array_map('strtolower', $header));
        $method2_index = array_search('method2', array_map('strtolower', $header));
        $method3_index = array_search('method3', array_map('strtolower', $header));
        
        // Check if required columns exist
        if ($zip_index === false || $method1_index === false || 
            $method2_index === false || $method3_index === false) {
            fclose($handle);
            return false;
        }
        
        // Search for the ZIP code
        while (($data = fgetcsv($handle)) !== false) {
            if ($data[$zip_index] == $zip_code) {
                fclose($handle);
                return array(
                    'method1' => $data[$method1_index],
                    'method2' => $data[$method2_index],
                    'method3' => $data[$method3_index]
                );
            }
        }
        
        fclose($handle);
        return false;
    }

    /**
     * Load and validate CSV file.
     *
     * @param string $file_path Full path to the CSV file.
     * @return array|WP_Error Validation results or error.
     */
    public function validate_csv($file_path) {
        if (!file_exists($file_path)) {
            return new WP_Error('file_not_found', 'CSV file not found.');
        }
        
        $handle = fopen($file_path, 'r');
        
        if (!$handle) {
            return new WP_Error('file_open_error', 'Could not open CSV file.');
        }
        
        $header = fgetcsv($handle);
        
        // Check required columns
        $required_columns = array('zip_code', 'method1', 'method2', 'method3');
        $missing_columns = array();
        
        foreach ($required_columns as $required) {
            if (!in_array($required, array_map('strtolower', $header))) {
                $missing_columns[] = $required;
            }
        }
        
        if (!empty($missing_columns)) {
            fclose($handle);
            return new WP_Error(
                'missing_columns', 
                'CSV file is missing required columns: ' . implode(', ', $missing_columns)
            );
        }
        
        // Count data rows
        $row_count = 0;
        while (fgetcsv($handle) !== false) {
            $row_count++;
        }
        
        fclose($handle);
        
        return array(
            'columns' => $header,
            'row_count' => $row_count
        );
    }
}
