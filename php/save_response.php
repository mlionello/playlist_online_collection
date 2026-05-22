<?php
session_start(); // Start or resume the session

// Ensure the user has a session and an ID
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No user session found.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Check if data was sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST
    $data = $_POST;
    $section = $data["current_section"];

    if (isset($_SESSION['track_index'])) {
        $_SESSION['track_index']=$data["track_index_next"];
    }

    // Define the file name for storing user responses
    $fileName = "../responses/user_" . $userId . ".json"; // Adjust the path as needed

    $directory = dirname($fileName);

    // Check if the directory exists, and create it if it doesn't
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true); // Creates the directory with recursive permissions
    }

    // Initialize an array to hold the response data
    $responses = [];

    // Check if the JSON file already exists
    if (file_exists($fileName)) {
        // Read the existing data
        $existingData = file_get_contents($fileName);
        $responses = json_decode($existingData, true) ?: []; // Decode or use an empty array
    }

    // Save the new response data
    $responses[$section] = $data;

    // Check if the current section is 'welcome' and save the entire session
    if ($section === 'welcome') {
        // Save all session data to the JSON
        $responses['session_data'] = $_SESSION;
    }

    // Save the updated responses back to the JSON file
    if (file_put_contents($fileName, json_encode($responses, JSON_PRETTY_PRINT))) {
        // Respond with a success message
        echo json_encode(['status' => 'success', 'message' => 'Response saved successfully.']);
    } else {
        // Respond with an error if unable to write the file
        echo json_encode(['status' => 'error', 'message' => 'Failed to save response.']);
    }

    // Check if the current section is 'post_experiment'
    if ($section === 'post_experiment' && isset($_SESSION['audio_group'])) {
        // Define the file for appending the audio group information
        $audioGroupFileName = "../responses/audio_group.txt";

        // Append the audio group session data to the file
        $audioGroupData = "User ID: " . $userId . " - Audio Group: " . $_SESSION['audio_group'] . PHP_EOL;

        // Append the data to the file
        if (file_put_contents($audioGroupFileName, $audioGroupData, FILE_APPEND)) {
            // You can log or respond that the data was appended successfully
            echo json_encode(['status' => 'success', 'message' => 'Audio group data appended successfully.']);
        } else {
            // Respond with an error if unable to write the file
            echo json_encode(['status' => 'error', 'message' => 'Failed to append audio group data.']);
        }
    }

} else {
    // Respond with an error if the request method is not POST
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
