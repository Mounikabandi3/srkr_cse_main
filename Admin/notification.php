<?php
require_once 'composer/SpreadsheetReader.php';
require_once 'composer/SpreadsheetReader_XLS.php';
require 'composer/vendor/autoload.php';

// include other necessary files similarly


use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
// Function to check if a registration token is valid
function isValidRegistrationToken($token) {
    // Implement your logic to validate the registration token here
    // This could involve checking if the token exists, is not expired, etc.
    // For example, you could check if the token matches a certain pattern or length
    // Here's a simple example assuming tokens are alphanumeric strings with a specific length
    $tokenLength = 32; // Example token length
    if (preg_match('/^[a-zA-Z0-9]{' . $tokenLength . '}$/', $token)) {
        // Token matches the expected pattern and length
        return true;
    } else {
        // Token does not match the expected pattern and length
        return false;
    }
}


function sendFCM($tokens, $message) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    $apiKey = "AAAAj9vMibY:APA91bGpmUkwO7atQq3OQD9FIyv5x3KI5Y9MLT26IE1qVTuTx1MtYkQE8K5D514WIRjPpxcfWXE4tZnxfC02hzRAU0pZvZ8jCZVRIduIbbzXgvjmAthbr9EXbHFJNgTHwBjQfU3OgRK-";  


    $headers = [
        'Authorization: key=' . $apiKey,
        'Content-Type: application/json'
    ];
    error_log("Sending FCM with tokens: " . json_encode($tokens));

    $fields = [
        'registration_ids' => $tokens,
        'notification' => [
            'title' => 'Notification Title',
            'body' => $message
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['file']) || !isset($_POST['message'])) {
        echo 'Error: Missing necessary parameters.';
    } else {
        $filePath = htmlspecialchars($_POST['file']);
        $message = htmlspecialchars($_POST['message']);

        $reader = new Xlsx();
        try {
            $spreadsheet = $reader->load($filePath);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            exit('Error loading file: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

       // Assuming 'Register_Number' is in column B (second column)
       $tokens = [];
       for ($row = 2; $row <= $highestRow; ++$row) {
           $columnLetter = Coordinate::stringFromColumnIndex(2); // B is the second column
           $cellCoordinate = $columnLetter . $row;
           $token = $sheet->getCell($cellCoordinate)->getValue();
           if (!empty($token)) {
               // Check if the registration token is valid (you may need to adjust this logic)
               if (isValidRegistrationToken($token)) {
                   $tokens[] = $token;
               } else {
                   // Handle invalid registration token (e.g., remove from database, log, etc.)
                   // Example: logInvalidRegistrationToken($token);
               }
           }
       }
       


        if (empty($tokens)) {
            echo 'No tokens available for sending notifications.';
        } else {
            $response = sendFCM($tokens, $message);
            echo "Notification sent. Response: " . $response;
        }
    }
    ini_set('display_errors', 1);
error_reporting(E_ALL);

} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notifications</title>
</head>
<body>
    <h1>Send Notification</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="message">Message:</label><br>
        <input type="text" id="message" name="message" required><br><br>
        
        <label for="file">Choose Excel Sheet:</label><br>
        <select name="file" id="file" required>
            <option value="">Select an Excel file</option>
            <?php
            foreach (glob("uploads/*.xlsx") as $file) {
                echo '<option value="' . htmlspecialchars($file) . '">' . htmlspecialchars(basename($file)) . '</option>';
            }
            ?>
        </select><br><br>
        
        <input type="submit" value="Send Notification">
    </form>
</body>
</html>

<?php
}
?>
