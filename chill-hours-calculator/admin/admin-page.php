<?php
/**
 * Admin page for Chill Hours Calculator.
 *
 * @package    Chill_Hours_Calculator
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Admin page callback function.
 */
function chill_hours_calculator_admin_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Save settings
    if (isset($_POST['submit_chill_hours_settings'])) {
        if (check_admin_referer('chill_hours_calculator_settings', 'chill_hours_nonce')) {
            $settings = get_option('chill_hours_calculator_settings', array());
            
            // Update method names and descriptions
            $settings['method_1_name'] = sanitize_text_field($_POST['method_1_name']);
            $settings['method_1_description'] = sanitize_text_field($_POST['method_1_description']);
            $settings['method_2_name'] = sanitize_text_field($_POST['method_2_name']);
            $settings['method_2_description'] = sanitize_text_field($_POST['method_2_description']);
            $settings['method_3_name'] = sanitize_text_field($_POST['method_3_name']);
            $settings['method_3_description'] = sanitize_text_field($_POST['method_3_description']);
            
            update_option('chill_hours_calculator_settings', $settings);
            
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
        }
    }
    
    // Handle CSV upload
    if (isset($_POST['upload_csv'])) {
        if (check_admin_referer('chill_hours_calculator_csv_upload', 'chill_hours_csv_nonce')) {
            if (!empty($_FILES['csv_file']['tmp_name'])) {
                $upload_dir = wp_upload_dir();
                $target_dir = $upload_dir['basedir'] . '/chill-hours/';
                
                // Create directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    wp_mkdir_p($target_dir);
                }
                
                $target_file = $target_dir . basename($_FILES['csv_file']['name']);
                
                // Check file type
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                if ($file_type != 'csv') {
                    echo '<div class="notice notice-error is-dismissible"><p>Only CSV files are allowed.</p></div>';
                } else {
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $target_file)) {
                        // Validate CSV file
                        $data_handler = new Chill_Hours_Data();
                        $validation = $data_handler->validate_csv($target_file);
                        
                        if (is_wp_error($validation)) {
                            echo '<div class="notice notice-error is-dismissible"><p>CSV Validation Error: ' . esc_html($validation->get_error_message()) . '</p></div>';
                            // Delete the invalid file
                            @unlink($target_file);
                        } else {
                            // Update settings with new file path
                            $settings = get_option('chill_hours_calculator_settings', array());
                            $settings['csv_file'] = $target_file;
                            update_option('chill_hours_calculator_settings', $settings);
                            
                            echo '<div class="notice notice-success is-dismissible">';
                            echo '<p>CSV file uploaded successfully. ';
                            echo 'Detected ' . esc_html($validation['row_count']) . ' data rows.</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="notice notice-error is-dismissible"><p>Failed to upload file. Please check directory permissions.</p></div>';
                    }
                }
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Please select a CSV file to upload.</p></div>';
            }
        }
    }
    
    // Get current settings
    $settings = get_option('chill_hours_calculator_settings', array());
    
    // Set default values if empty
    $method_1_name = isset($settings['method_1_name']) ? $settings['method_1_name'] : 'Utah Model';
    $method_1_description = isset($settings['method_1_description']) ? $settings['method_1_description'] : 'Hours between 32°F and 45°F';
    $method_2_name = isset($settings['method_2_name']) ? $settings['method_2_name'] : 'California Model';
    $method_2_description = isset($settings['method_2_description']) ? $settings['method_2_description'] : 'Hours below 45°F';
    $method_3_name = isset($settings['method_3_name']) ? $settings['method_3_name'] : 'Dynamic Model';
    $method_3_description = isset($settings['method_3_description']) ? $settings['method_3_description'] : 'Weighted approach based on temperature thresholds';
    
    // Get CSV file path
    $csv_file = isset($settings['csv_file']) ? $settings['csv_file'] : '';
    $csv_file_name = !empty($csv_file) ? basename($csv_file) : 'No file uploaded';
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="notice notice-info">
            <p>Use the shortcode <code>[chill_hours_calculator]</code> to display the chill hours calculator on any page or post.</p>
        </div>
        
        <div class="card">
            <h2>CSV Data File</h2>
            <p>Upload a CSV file with ZIP codes and chill hours data. The CSV must have columns named: <code>zip_code</code>, <code>method1</code>, <code>method2</code>, and <code>method3</code>.</p>
            
            <p><strong>Current file:</strong> <?php echo esc_html($csv_file_name); ?></p>
            
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('chill_hours_calculator_csv_upload', 'chill_hours_csv_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="csv_file">CSV File</label></th>
                        <td>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv">
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Upload CSV', 'primary', 'upload_csv'); ?>
            </form>
        </div>
        
        <div class="card">
            <h2>Calculation Methods</h2>
            <p>Customize the names and descriptions of the three chill hours calculation methods.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('chill_hours_calculator_settings', 'chill_hours_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="method_1_name">Method 1 Name</label></th>
                        <td>
                            <input type="text" name="method_1_name" id="method_1_name" class="regular-text" value="<?php echo esc_attr($method_1_name); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="method_1_description">Method 1 Description</label></th>
                        <td>
                            <input type="text" name="method_1_description" id="method_1_description" class="regular-text" value="<?php echo esc_attr($method_1_description); ?>">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="method_2_name">Method 2 Name</label></th>
                        <td>
                            <input type="text" name="method_2_name" id="method_2_name" class="regular-text" value="<?php echo esc_attr($method_2_name); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="method_2_description">Method 2 Description</label></th>
                        <td>
                            <input type="text" name="method_2_description" id="method_2_description" class="regular-text" value="<?php echo esc_attr($method_2_description); ?>">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="method_3_name">Method 3 Name</label></th>
                        <td>
                            <input type="text" name="method_3_name" id="method_3_name" class="regular-text" value="<?php echo esc_attr($method_3_name); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="method_3_description">Method 3 Description</label></th>
                        <td>
                            <input type="text" name="method_3_description" id="method_3_description" class="regular-text" value="<?php echo esc_attr($method_3_description); ?>">
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Save Settings', 'primary', 'submit_chill_hours_settings'); ?>
            </form>
        </div>
        
        <div class="card">
            <h2>Usage Instructions</h2>
            <ol>
                <li>Upload a properly formatted CSV file with chill hours data by ZIP code.</li>
                <li>Customize the names and descriptions of the calculation methods if needed.</li>
                <li>Add the shortcode <code>[chill_hours_calculator]</code> to any page or post.</li>
                <li>Users can enter their ZIP code to see chill hours calculated with all three methods.</li>
            </ol>
        </div>
    </div>
    <?php
}
