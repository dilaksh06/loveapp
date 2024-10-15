<?php
header('Content-Type: application/json');

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input to prevent XSS attacks
    $name1 = htmlspecialchars(trim($_POST["name1"]));
    $name2 = htmlspecialchars(trim($_POST["name2"]));

    // Validate inputs to ensure they're not empty
    if (empty($name1) || empty($name2)) {
        $response['status'] = 'error';
        $response['message'] = 'Both names are required!';
        echo json_encode($response);
        exit;
    }

    // Calculate the love score (custom logic can be added here)
    $loveScore = mt_rand(0, 100);

    // Get user's IP address
    $userIP = $_SERVER['REMOTE_ADDR'];

    // Convert IPv6 localhost (::1) to IPv4 localhost (127.0.0.1)
    if ($userIP === '::1') {
        $userIP = '127.0.0.1';
    }

    // Prepare data to save
    $data = [
        'name1' => $name1,
        'name2' => $name2,
        'love_score' => $loveScore,
        'ip_address' => $userIP,
    ];

    $file = 'details.json';

    // Check if the file exists, if not, create it
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([])); // Create an empty array
    }

    // Read existing data from the file
    $existingData = json_decode(file_get_contents($file), true);

    // Append the new data
    $existingData[] = $data;

    // Attempt to save the updated data back to the file
    if (file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT)) === false) {
        $response['status'] = 'error';
        $response['message'] = 'Error: Could not write to file.';
    } else {
        $response['status'] = 'success';
        $response['message'] = "$name1 and $name2's love score is $loveScore%";
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
