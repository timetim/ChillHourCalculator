<?php
/**
 * Check if a ZIP code exists in our database
 */

// Get ZIP code from GET parameter
$zip_code = isset($_GET['zip_code']) ? $_GET['zip_code'] : '';

// Validate ZIP code format
if (!preg_match('/^\d{5}(-\d{4})?$/', $zip_code)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid ZIP code format. Please enter a valid 5-digit ZIP code.'
    ]);
    exit;
}

// Get the 5-digit part of the ZIP code
$zip_code = substr($zip_code, 0, 5);

// Load CSV file
$file_path = __DIR__ . '/sample-chill-hours.csv';
if (!file_exists($file_path)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'CSV file not found.'
    ]);
    exit;
}

$handle = fopen($file_path, 'r');
if (!$handle) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Could not open CSV file.'
    ]);
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
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'CSV file is missing required columns.'
    ]);
    exit;
}

// Search for the ZIP code
$chill_hours = null;
while (($data = fgetcsv($handle)) !== false) {
    if ($data[$zip_index] == $zip_code) {
        $chill_hours = [
            'method1' => $data[$method1_index],
            'method2' => $data[$method2_index],
            'method3' => $data[$method3_index]
        ];
        break;
    }
}

fclose($handle);

if ($chill_hours === null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, we don\'t have chill hours data for ZIP code ' . $zip_code . '. Please try a different ZIP code.'
    ]);
    exit;
}

// Method names and descriptions
$method_names = [
    'method1' => 'Utah Model',
    'method2' => 'California Model', 
    'method3' => 'Dynamic Model'
];

$method_descriptions = [
    'method1' => 'Hours between 32°F and 45°F',
    'method2' => 'Hours below 45°F',
    'method3' => 'Weighted approach based on temperature thresholds'
];

// Format the response data
$response_data = [
    'zip_code' => $zip_code,
    'method1' => [
        'name' => $method_names['method1'],
        'description' => $method_descriptions['method1'],
        'value' => $chill_hours['method1']
    ],
    'method2' => [
        'name' => $method_names['method2'],
        'description' => $method_descriptions['method2'],
        'value' => $chill_hours['method2']
    ],
    'method3' => [
        'name' => $method_names['method3'],
        'description' => $method_descriptions['method3'],
        'value' => $chill_hours['method3']
    ]
];

// Send success response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => $response_data
]);
exit;