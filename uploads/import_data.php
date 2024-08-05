<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/db.php';

function importUsers($filePath) {
    global $db;
    $realPath = realpath($filePath);
    if ($realPath === false || !file_exists($realPath)) {
        die("File not found: $filePath");
    }

    if (($handle = fopen($realPath, 'r')) !== FALSE) {
        fgetcsv($handle); // Skip the header row
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, room_number, group_name, week_date) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt->execute([$data[0], $data[1], $data[2], $data[3], $data[4]])) {
                // Log the error message
                error_log("Error inserting user: " . implode(", ", $stmt->errorInfo()));
            }
        }
        fclose($handle);
    } else {
        die("Cannot open file: $filePath");
    }
}

function importProducts($filePath) {
    global $db;
    $realPath = realpath($filePath);
    if ($realPath === false || !file_exists($realPath)) {
        die("File not found: $filePath");
    }

    if (($handle = fopen($realPath, 'r')) !== FALSE) {
        fgetcsv($handle); // Skip the header row
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $stmt = $db->prepare("INSERT INTO products (name, type, price) VALUES (?, ?, ?)");
            if (!$stmt->execute([$data[0], $data[1], $data[2]])) {
                // Log the error message
                error_log("Error inserting product: " . implode(", ", $stmt->errorInfo()));
            }
        }
        fclose($handle);
    } else {
        die("Cannot open file: $filePath");
    }
}

// Define the paths to the CSV files
$usersFilePath = '../uploads/users.csv';
$productsFilePath = '../uploads/products.csv';

// Import the data
importUsers($usersFilePath);
importProducts($productsFilePath);

echo "Data import completed successfully.";
?>
