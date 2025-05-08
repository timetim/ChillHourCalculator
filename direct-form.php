<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get ZIP code from GET parameter
$zip_code = isset($_GET['zip_code']) ? $_GET['zip_code'] : '';

// Validate ZIP code format
if (!preg_match('/^\d{5}(-\d{4})?$/', $zip_code)) {
    echo "<p class='error'>Invalid ZIP code format. Please enter a valid 5-digit ZIP code.</p>";
    exit;
}

// Get the 5-digit part of the ZIP code
$zip_code = substr($zip_code, 0, 5);

// Load CSV file
$file_path = __DIR__ . '/sample-chill-hours.csv';
if (!file_exists($file_path)) {
    echo "<p class='error'>CSV file not found.</p>";
    exit;
}

$handle = fopen($file_path, 'r');
if (!$handle) {
    echo "<p class='error'>Could not open CSV file.</p>";
    exit;
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
    echo "<p class='error'>CSV file is missing required columns.</p>";
    exit;
}

// Search for the ZIP code
$chill_hours = null;
while (($data = fgetcsv($handle)) !== false) {
    if ($data[$zip_index] == $zip_code) {
        $chill_hours = array(
            'method1' => $data[$method1_index],
            'method2' => $data[$method2_index],
            'method3' => $data[$method3_index]
        );
        break;
    }
}

fclose($handle);

if ($chill_hours === null) {
    echo "<p class='error'>No chill hours data found for ZIP code: $zip_code</p>";
    exit;
}

// Method names and descriptions
$method_names = array(
    'method1' => 'Utah Model',
    'method2' => 'California Model', 
    'method3' => 'Dynamic Model'
);

$method_descriptions = array(
    'method1' => 'Hours between 32°F and 45°F',
    'method2' => 'Hours below 45°F',
    'method3' => 'Weighted approach based on temperature thresholds'
);

// Output the results
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chill Hours Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            max-width: 800px;
        }
        h1 {
            color: #333;
        }
        .results-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        .result-method {
            flex: 1;
            min-width: 200px;
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .result-method h3 {
            margin-top: 0;
            color: #2271b1;
        }
        .method-description {
            font-size: 14px;
            color: #666;
            min-height: 40px;
        }
        .hours-value {
            font-size: 32px;
            font-weight: bold;
            color: #2271b1;
        }
        .hours-label {
            font-size: 14px;
            color: #666;
        }
        .results-explanation {
            background-color: #f0f6fc;
            border-left: 4px solid #2271b1;
            padding: 15px;
            margin-top: 20px;
            border-radius: 0 5px 5px 0;
        }
        .back-link {
            margin-top: 20px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Chill Hours Results for ZIP Code: <?php echo htmlspecialchars($zip_code); ?></h1>
    
    <div class="results-container">
        <?php foreach ($chill_hours as $method => $value): ?>
            <div class="result-method">
                <h3><?php echo htmlspecialchars($method_names[$method]); ?></h3>
                <p class="method-description">
                    <?php echo htmlspecialchars($method_descriptions[$method]); ?>
                </p>
                <div class="hours-value"><?php echo htmlspecialchars($value); ?></div>
                <div class="hours-label">chill hours</div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="results-explanation">
        <p>Chill hours are the number of hours plants are exposed to cold temperatures in winter, 
        which is essential for proper fruit and flower development in many crops.</p>
        <p>Different calculation methods may be preferred depending on your climate and the plants you're growing.</p>
    </div>
    
    <div class="back-link">
        <a href="/">&laquo; Back to Calculator</a>
    </div>
</body>
</html>