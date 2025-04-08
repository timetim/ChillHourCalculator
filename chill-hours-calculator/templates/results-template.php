<?php
/**
 * Results template for the Chill Hours Calculator.
 * This is dynamically loaded via AJAX, but kept here for reference.
 *
 * @package    Chill_Hours_Calculator
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<h3>Chill Hours Results</h3>
<p class="zip-code-display">Results for ZIP code: <span>{zip_code}</span></p>

<div class="results-container">
    <div class="result-method">
        <h4>{method1_name}</h4>
        <p class="method-description">{method1_description}</p>
        <div class="hours-value">{method1_value}</div>
        <div class="hours-label">chill hours</div>
    </div>
    
    <div class="result-method">
        <h4>{method2_name}</h4>
        <p class="method-description">{method2_description}</p>
        <div class="hours-value">{method2_value}</div>
        <div class="hours-label">chill hours</div>
    </div>
    
    <div class="result-method">
        <h4>{method3_name}</h4>
        <p class="method-description">{method3_description}</p>
        <div class="hours-value">{method3_value}</div>
        <div class="hours-label">chill hours</div>
    </div>
</div>

<div class="results-explanation">
    <p>Chill hours are the number of hours plants are exposed to cold temperatures in winter, 
    which is essential for proper fruit and flower development in many crops.</p>
    <p>Different calculation methods may be preferred depending on your climate and the plants you're growing.</p>
</div>
