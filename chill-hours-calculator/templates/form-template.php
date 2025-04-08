<?php
/**
 * Form template for the Chill Hours Calculator.
 *
 * @package    Chill_Hours_Calculator
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get settings
$settings = get_option('chill_hours_calculator_settings', array());
?>

<div class="chill-hours-calculator">
    <div class="chill-hours-form">
        <h3>Find Your Chill Hours</h3>
        <p>Enter your ZIP code to calculate your local chill hours using three different methods.</p>
        
        <form id="chill-hours-form">
            <div class="form-group">
                <label for="zip-code">ZIP Code:</label>
                <input type="text" id="zip-code" name="zip-code" maxlength="10" placeholder="Enter your 5-digit ZIP code" pattern="\d{5}(-\d{4})?" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="button">Calculate Chill Hours</button>
            </div>
        </form>
    </div>
    
    <div id="chill-hours-results" class="chill-hours-results" style="display: none;">
        <h3>Chill Hours Results</h3>
        <p class="zip-code-display">Results for ZIP code: <span id="result-zip-code"></span></p>
        
        <div class="results-container">
            <div class="result-method" id="method1-result">
                <h4 id="method1-name"><?php echo esc_html(isset($settings['method_1_name']) ? $settings['method_1_name'] : 'Utah Model'); ?></h4>
                <p class="method-description" id="method1-description">
                    <?php echo esc_html(isset($settings['method_1_description']) ? $settings['method_1_description'] : 'Hours between 32°F and 45°F'); ?>
                </p>
                <div class="hours-value" id="method1-value">--</div>
                <div class="hours-label">chill hours</div>
            </div>
            
            <div class="result-method" id="method2-result">
                <h4 id="method2-name"><?php echo esc_html(isset($settings['method_2_name']) ? $settings['method_2_name'] : 'California Model'); ?></h4>
                <p class="method-description" id="method2-description">
                    <?php echo esc_html(isset($settings['method_2_description']) ? $settings['method_2_description'] : 'Hours below 45°F'); ?>
                </p>
                <div class="hours-value" id="method2-value">--</div>
                <div class="hours-label">chill hours</div>
            </div>
            
            <div class="result-method" id="method3-result">
                <h4 id="method3-name"><?php echo esc_html(isset($settings['method_3_name']) ? $settings['method_3_name'] : 'Dynamic Model'); ?></h4>
                <p class="method-description" id="method3-description">
                    <?php echo esc_html(isset($settings['method_3_description']) ? $settings['method_3_description'] : 'Weighted approach based on temperature thresholds'); ?>
                </p>
                <div class="hours-value" id="method3-value">--</div>
                <div class="hours-label">chill hours</div>
            </div>
        </div>
        
        <div class="results-explanation">
            <p>Chill hours are the number of hours plants are exposed to cold temperatures in winter, 
            which is essential for proper fruit and flower development in many crops.</p>
            <p>Different calculation methods may be preferred depending on your climate and the plants you're growing.</p>
        </div>
    </div>
    
    <div id="chill-hours-error" class="chill-hours-error" style="display: none;">
        <p id="error-message"></p>
    </div>
    
    <div id="chill-hours-loading" class="chill-hours-loading" style="display: none;">
        <p>Calculating chill hours...</p>
    </div>
</div>
