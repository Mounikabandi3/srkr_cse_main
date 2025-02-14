<?php
$activeSection = 'createaccount';
include '../_dbconnect.php';

$count = 0;

if($_SESSION["user_type"] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}


if($_SESSION["user_type"] === 'student') {
    header("Location: ../");
    exit();
}

$count = 0;


require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Function to encrypt the email
function encryptEmail($email) {
    // You can use any encryption algorithm here
    // For example, using base64 encoding
    return base64_encode($email);
}


// function generateVerificationCode()
// {
//     return bin2hex(random_bytes(25));
// }

// $verification_code = generateVerificationCode();

if (isset($_POST["submit"])) {

    $stmt = $conn->prepare("INSERT INTO student_tb (Name, Register_Number, Email, Password, Year, Branch, Section,  is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, FALSE)");
    $stmt->bind_param("sssssss", $name, $register_number, $email, $password, $year, $branch, $section);

    foreach ($_SESSION["student_data"] as $student) {

        // Extract data from the session array
        $name = $student['name'];
        $register_number = $student['register_number'];
        $email = isset($student['email']) ? $student['email'] : ''; // Check if email exists
        $password = "123456789"; // Assuming temporary_password is the actual password
        $year = isset($student['year']) ? $student['year'] : ''; // Check if year exists
        $branch = isset($student['branch']) ? $student['branch'] : ''; // Check if branch exists
        $section = isset($student['section']) ? $student['section'] : ''; // Check if section exists

        // Check if required fields are not empty before insertion
        if (!empty($name) && !empty($register_number) && !empty($email) && !empty($year) && !empty($branch) && !empty($section)) {

            Verification($email, $register_number, $name);
            $stmt->execute();
            $count++;
        }
    }

    echo '<script>alert("Verification emails have been successfully sent to ' . $count . ' students.");</script>';


    $stmt->close();
    unset($_SESSION["student_data"]); // Clear session data after inserting into the database
}

function Verification($email, $register_number, $name)
{
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable verbose debug output
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true; // Enable SMTP authentication
        $mail->Username   = 'srinivasnani005@gmail.com'; // SMTP username (replace with your Gmail email)
        $mail->Password   = 'flkv lvmw pavy edsc'; // SMTP password (replace with your Gmail password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587; // TCP port to connect to

        //Recipients
        $mail->setFrom('srinivasnani005@gmail.com', 'Srinivas Nani');
        $mail->addAddress($email, $name);

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Email Verification';

        // Email body
        $mail->Body = '
            <html>
            <head>
                <style>
                    /* Global styles */
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f5f5f5;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: auto;
                        background-color: #ffffff;
                        border-radius: 10px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        background-color: #4caf50;
                        color: #ffffff;
                        padding: 10px;
                        text-align: center;
                        border-top-left-radius: 10px;
                        border-top-right-radius: 10px;
                    }
                    .content {
                        padding: 30px;
                        text-align: center;
                    }
                    .footer {
                        background-color: #4caf50;
                        color: #ffffff;
                        padding: 10px;
                        text-align: center;
                        border-bottom-left-radius: 10px;
                        border-bottom-right-radius: 10px;
                    }
                    .button {
                        display: inline-block;
                        background-color: #4caf50;
                        color: #ffffff;
                        padding: 10px 20px;
                        text-decoration: none;
                        border-radius: 5px;
                        transition: background-color 0.3s ease;
                    }
                    .button:hover {
                        background-color: #388e3c; /* Darker shade of green on hover */
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Email Verification</h2>
                    </div>
                    <div class="content">
                        <p>Dear ' . $name . ',</p>
                        <p>Welcome to SRKR Engineering College! To complete your registration, please click the button below to verify your email address:</p>
                        <a href="http://srkr.me/Student/verify.php?id=' . encryptEmail($email) . '" class="button">Verify Email</a>
                        <p style="margin-top: 20px;">Your Register Number: ' . $register_number . '</p>
                    </div>
                    <div class="footer">
                        <p>This email was sent from SRKR Engineering College. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ';

        // Plain text alternative for email clients that don't support HTML
        $mail->AltBody = 'Dear ' . $name . ', Welcome to SRKR Engineering College. Please click the following link to verify your email address: http://example.com/verify.php?email=' . $email . '. Your Register Number: ' . $register_number;

        $mail->send();
    } catch (Exception $e) {
        echo '<script>alert("Verification Emails are not sent to Register Number ' . $register_number . '");</script>'; // Display popup message if email sending fails
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../css/temp.css">
    <link rel="stylesheet" href="../css/style.css">

    <script src="../js/script.js" defer></script>
    <title>Dashboard</title>
    <style>
        .container {
            max-width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px 10px 0 0;
        }

        .header h2 {
            margin: 0;
        }

        .logout-btn {
            background-color: #fff;
            color: #4caf50;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #f2f2f2;
        }

        .body {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .upload-form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .label {
            margin-right: 10px;
            font-weight: bold;
            width: 60px;
        }

        .upload-input {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100px;
            margin-right: 10px;
            font-size: 12px;
        }

        .upload-input[type="file"] {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
            margin-right: 10px;
            font-size: 12px;
        }

        .upload-input:last-child {
            margin-right: 0;
        }

        .upload-btn {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .upload-btn:hover {
            background-color: #388e3c;
        }

        .footer {
            text-align: right;
            margin-top: 20px;
        }

        .submit-btn {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #388e3c;
        }

        .button-group {
            margin-bottom: 20px;
            text-align: right;
        }

    </style>

</head>

<body>

    <?php include '_side.php'; ?>
    <?php include '_nav.php'; ?>

    <main>

        <div class="container">
            <div class="body">
                <h2>Upload Excel File</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="upload-form">
                        <label class="label" for="year">Year:</label>
                        <select class="upload-input" id="year" name="year">
                            <?php
                            $currentYear = date("Y");
                            for ($i = $currentYear; $i >= $currentYear - 4; $i--) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>

                        <label class="label" for="branch">Branch:</label>
                        <select class="upload-input" id="branch" name="branch">
                            <?php
                        $branches = array("CSE", "ECE", "EEE", "CIVIL", "AIDS", "CSD", "MECH", "AIML", "IT");
                            foreach ($branches as $branch) {
                                echo '<option value="' . $branch . '">' . $branch . '</option>';
                            }
                            ?>
                        </select>

                        <label class="label" for="section">Section:</label>
                        <select class="upload-input" id="section" name="section">

                        <?php
                        $sections = array("A", "B", "C", "D", "E");
                        foreach ($sections as $section) {
                            echo '<option value="' . $section . '">' . $section . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- File upload -->
                <input class="upload-input" type="file" name="excel" required>
                <button class="upload-btn" type="submit">Check</button> <!-- Changed name="import" to name="submit" -->
            </form>
            <hr>
            <?php
            if(isset($_FILES["excel"]["name"])){
                // Display the uploaded file data in a table format
                require 'composer/excel_reader2.php';
                require 'composer/SpreadsheetReader.php';

                $fileName = $_FILES["excel"]["name"];
                $targetDirectory = "uploads/" . $fileName;
                move_uploaded_file($_FILES['excel']['tmp_name'], $targetDirectory);

                $reader = new SpreadsheetReader($targetDirectory);
                $_SESSION["student_data"] = [];

                foreach ($reader as $row) {
                    $name = isset($row[0]) ? $conn->real_escape_string($row[0]) : '';
                    $register_number = isset($row[1]) ? $conn->real_escape_string($row[1]) : '';
                    $email = isset($row[2]) ? $conn->real_escape_string($row[2]) : ''; // Assuming email is fetched from the excel data
                    $year = $_POST['year']; // Use selected year
                    $branch = $_POST['branch']; // Use selected branch
                    $section = $_POST['section']; // Use selected section

                    $_SESSION["student_data"][] = array(
                        'name' => $name,
                        'register_number' => $register_number,
                        'email' => $email,
                        'year' => $year,
                        'branch' => $branch,
                        'section' => $section
                    );
                }
            ?>
            <h2>Uploaded File Data</h2>
            <div style="overflow-x:auto;">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Register Number</th>
                        <th>Email</th>
                        <th>Year</th>
                        <th>Branch</th>
                        <th>Section</th>
                    </tr>
                    <?php
                    foreach ($_SESSION["student_data"] as $student) {
                        $count+=1;
                    ?>
                    <tr>
                        <td><?php echo $student['name']; ?></td>
                        <td><?php echo $student['register_number']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo $student['year']; ?></td>
                        <td><?php echo $student['branch']; ?></td>
                        <td><?php echo $student['section']; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>

            <!-- Display submit button after checking -->
            <div class="button-group">
                <form action="" method="post">
                    <button class="submit-btn" type="submit" name="submit">Submit</button>
                </form>
            </div>
            <?php } ?>

            <div id="successMessage" style="display: none; color: green; margin-top: 10px;">Data submitted successfully.</div>

        </div>
        <!-- Footer -->
        <div class="footer">
            <!-- Footer content, if any -->
        </div>

        <script>
            // Function to show success message and hide after specified duration
            function showSuccessMessage() {
                var successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 5000); // 5000 milliseconds = 5 seconds
            }

            // Event listener for form submission
            document.getElementById('submitForm').addEventListener('submit', function(event) {
                // Show success message
                showSuccessMessage();
            });
        </script>
    </div>


            
    </main>

 

    </section>

    <script>
        


    </script>
</body>

</html>



















