<?php
/**
 * Simple WordPress simulator for testing the Chill Hours Calculator plugin
 */

// Simple form for demonstration purposes
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chill Hours Calculator Demo</title>
    <link rel="stylesheet" href="chill-hours-calculator/public/css/chill-hours-calculator.css">
    <link rel="stylesheet" href="custom.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="chill-hours-calculator">
        <h1>Chill Hours Calculator Demo</h1>
        
        <div class="chill-hours-form">
            <h3>Find Your Chill Hours</h3>
            <p>Enter your ZIP code to calculate your local chill hours using three different methods.</p>
            
            <form id="chill-hours-form" action="direct-form.php" method="get">
                <div class="form-group">
                    <label for="zip-code">ZIP Code:</label>
                    <input type="text" id="zip-code" name="zip_code" maxlength="10" placeholder="Enter your 5-digit ZIP code" pattern="\d{5}(-\d{4})?" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="button">Calculate Chill Hours</button>
                </div>
            </form>
            
            <div class="note">
                <p><strong>Note:</strong> This demo uses sample data for the following ZIP codes: 94040, 90210, 33101, 60601, etc.</p>
            </div>
        </div>
        
        <div class="results-explanation">
            <h3>What are Chill Hours?</h3>
            <p>Chill hours are the number of hours plants are exposed to cold temperatures in winter, 
            which is essential for proper fruit and flower development in many deciduous fruit and nut crops.</p>
            
            <h4>Why Chill Hours Matter</h4>
            <p>Many fruit trees, such as apples, peaches, and cherries, require a certain number of chill hours 
            during their dormant winter period. Without sufficient chill hours, these plants may:</p>
            <ul>
                <li>Bloom irregularly or not at all</li>
                <li>Produce fewer flowers and consequently less fruit</li>
                <li>Have delayed or extended bloom periods</li>
                <li>Experience reduced fruit quality</li>
            </ul>
            
            <h4>Different Calculation Methods</h4>
            <p>There are three main methods to calculate chill hours:</p>
            <ol>
                <li><strong>Utah Model:</strong> Counts hours when temperatures are between 32°F and 45°F</li>
                <li><strong>California Model:</strong> Counts all hours below 45°F</li>
                <li><strong>Dynamic Model:</strong> Uses a weighted approach based on temperature thresholds and duration</li>
            </ol>
            
            <p>Different plants may respond better to different calculation methods based on their genetic background and adaptation to various climates.</p>
        </div>
    </div>
</body>
</html>