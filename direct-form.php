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
    <link rel="stylesheet" href="chill-hours-calculator/public/css/chill-hours-calculator.css">
    <link rel="stylesheet" href="custom.css">
    <style>
        /* Additional styles specific to results page */
        body {
            padding: 20px;
        }
        
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            border-top: 5px solid #3a7d44;
        }
        
        .back-link {
            margin-top: 30px;
            display: inline-block;
            color: #3a7d44;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border: 2px solid #3a7d44;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background-color: #3a7d44;
            color: white;
            transform: translateY(-2px);
        }
        
        .back-link:before {
            content: "←";
            margin-right: 8px;
        }
        
        .results-header {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 2px solid #e8f4ea;
        }
        
        .results-header:before {
            content: "❄";
            margin-right: 10px;
            color: #6eb5ff;
            font-size: 24px;
        }
        
        .results-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px 0;
        }
        
        .error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #721c24;
        }
        
        @media (max-width: 768px) {
            .results-container {
                flex-direction: column;
            }
            
            .page-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <h1 class="results-header">Chill Hours Results for ZIP Code: <?php echo htmlspecialchars($zip_code); ?></h1>
        
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
            <h3>About Chill Hours</h3>
            <p>Chill hours are the number of hours plants are exposed to cold temperatures in winter, 
            which is essential for proper fruit and flower development in many deciduous crops.</p>
            
            <h4>Why Different Methods?</h4>
            <p>Different calculation methods may be preferred depending on your climate zone and the specific 
            plants you're growing. The results above show how your location compares using all three common methods.</p>
            
            <ul>
                <li><strong>Utah Model</strong> is best for traditional cold-climate fruits</li>
                <li><strong>California Model</strong> works well in milder Mediterranean climates</li>
                <li><strong>Dynamic Model</strong> provides the most accurate results across various climates</li>
            </ul>
        </div>
        
        <a href="/" class="back-link">Back to Calculator</a>
    </div>
    
    <script>
        // Add subtle animation to the results cards
        document.addEventListener('DOMContentLoaded', function() {
            const resultMethods = document.querySelectorAll('.result-method');
            resultMethods.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });
    </script>
</body>
</html>