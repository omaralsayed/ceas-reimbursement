<?php
error_reporting(-1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

set_include_path('./includes/');
require_once('mysqli.php');
require_once('check_file.php');
require_once('PHPMailer/Exception.php');
require_once('PHPMailer/PHPMailer.php');

DEFINE('RECEIPT_MAX_FILE_SIZE', 6);
DEFINE('ATTENDANCE_MAX_FILE_SIZE', 6);

$name  = '';
$position  = '';
$email = '';
$mId  = '';

$date = '';
$vendor = '';
$amount = '';
$description = '';
$budgeted = false;
$direct = false;

$receipt = '';
$docs = '';

$name = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['nameText'])));
$position = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['positionText'])));
$email = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['emailText'])));
$mId = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['mIdText'])));
$date = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['dateText'])));
$vendor = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['vendorText'])));
$amount = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['amountText'])));
$description = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['descriptionText'])));
$budgeted = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['budgetedBool'])));
$direct = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['directBool'])));
$receipt = $_FILES['receiptFile'];
$docs = $_FILES['docsFile'];

$receipt_mime_types = array(
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'png'  => 'image/png',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg'
);

$docs_mime_types = array(
    'png'  => 'image/png',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg'
);

// Initialize data validation and SQL result
if (!isset($result_data))
    $result_data = new stdClass();
$result_data->status = 'error';
$result_data->message = '';

// Check receipt file
$receipt_check_result = checkFile($receipt, RECEIPT_MAX_FILE_SIZE, $receipt_mime_types);
if (!$receipt_check_result->file_safe) {
    $result_data->message = $receipt_check_result->message;
    echo json_encode($result_data);
    die();
}

// Check docs file
$docs_check_result = checkFile($docs, ATTENDANCE_MAX_FILE_SIZE, $docs_mime_types);
if (!$docs_check_result->file_safe) {
    $result_data->message = $docs_check_result->message;
    echo json_encode($result_data);
    die();
}

// Check name
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $name)) {
    $result_data->message = 'Your name, ' . htmlentities($name) . ', is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your name is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check position
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $position)) {
    $result_data->message = 'Your position, ' . htmlentities($position) . ', is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your position is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check email
if(!preg_match('/^[\w\W]+@[\w\W\d]{1,128}$/', $email)) {
    $result_data->message = 'Your email, ' . htmlentities($email) . ', is invalid. Please use an email in the following format: <>@<>. '
        . 'Your email is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check mId
if (!preg_match("/^M[0-9]{8}$/", $mId)) {
    $result_data->message = 'Your M#, ' . htmlentities($mId) . ', is invalid. Please only use official format (Mxxxxxxxx) with an optional '
        . 'apostrophe or period.';
    echo json_encode($result_data);
    die();
}

// Check date
if(!preg_match('/^(((0)[0-9])|((1)[0-2]))(\/)([0-2][0-9]|(3)[0-1])(\/)\d{4}$/', $date)) {
    $result_data->message = 'Your date, ' . htmlentities($date) . ', is invalid. Please format it in the following way: mm/dd/yyyy.';
    echo json_encode($result_data);
    die();
}

// Check vendor
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $vendor)) {
    $result_data->message = 'Your vendor, ' . htmlentities($vendor) . ', is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your vendor is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check amount
if (!preg_match("/^[0-9]{0,10}.[0-9]{0,10}$/", $mId)) {
    $result_data->message = 'Your amount, ' . htmlentities($mId) . ', is invalid. Please only use only numerical characters';
    echo json_encode($result_data);
    die();
}

// Check description
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $description)) {
    $result_data->message = 'Your description, ' . htmlentities($description) . ', is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your description is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check budgeted
if (!preg_match("/^{0,1}$/", $budgeted)) {
    $result_data->message = 'Your option, ' . htmlentities($budgeted) . ', is invalid.';
    echo json_encode($result_data);
    die();
}

// Check direct
if (!preg_match("/^{0,1}$/", $direct)) {
    $result_data->message = 'Your option, ' . htmlentities($direct) . ', is invalid.';
    echo json_encode($result_data);
    die();
}

// Get admin name and email
$admin_name  = '';
$admin_email = '';
$super_email = '';
$event_date  = '';

$sql = 'SELECT admin_name, admin_email, super_email FROM reimbursement_requests';
$result = $mysqli->query($sql);

if ($result) {
	while ($row = $result->fetch_assoc()) {
        $admin_name  = $row['admin_name'];
        $admin_email = $row['admin_email'];
        $super_email = $row['super_email'];
    }
}

if ($admin_email === '' || $admin_name === '' || $super_email === '') {
    $result_data->message = 'Error occurred while retrieving admin information. Please try again. '
        . 'If the error persists, email the admin in the description.';
    echo json_encode($result_data);
    die();
}

$name = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['nameText'])));
$position = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['positionText'])));
$email = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['emailText'])));
$mId = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['mIdText'])));
$date = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['dateText'])));
$vendor = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['vendorText'])));
$amount = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['amountText'])));
$description = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['descriptionText'])));
$budgeted = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['budgetedBool'])));
$direct = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['directBool'])));
$receipt = $_FILES['receiptFile'];
$docs = $_FILES['docsFile'];

// Insert form data
$sql = 'INSERT INTO reimbursement_requests (name, position, email, mId, date, vendor, amount, description, budgeted, direct) '
    . "VALUES ('".$name."','".$position."','".$email."','".$mId."','".$date."','".$vendor."','".$amount."','".$description."','".$budgeted."','".$direct."')";

$result = $mysqli->query($sql);

if (!$result) {
    $result_data->message = 'Error occurred while submitting your reimbursement request. Please try again. '
    . 'If the error persists, email the admin in the description.';
    echo json_encode($result_data);
    die();
}

// Email user
$mail = new PHPMailer(true);

try {
    $mail->Subject = "Reimbursement Request Recieved";

    $email_msg = "Hello " . $name . ", \n \n";
    $email_msg .= "This email is to confirm we have recieved your reimbursement request. ";
    $email_msg .= "Your transaction will be evaluated and if we require any further information, we will contact you. \n \n";
    $email_msg .= "If you have any questions, feel free to reply back to this email. Otherwise, mark your calendar and we ";
    $email_msg .= "look forward to seeing you there! \n \n";
    $email_msg .= "Best regards, \n";
    $email_msg .= $admin_name;

    $mail->Body = $email_msg;
    $mail->setFrom($admin_email, $admin_name);
    $mail->addAddress($email, $name);
    $mail->send();
} catch (Exception $e) {
    $result_data->message = 'Error occurred while sending your confirmation email. Please email the admin in the description notifying of this error.';
    echo json_encode($result_data);
    die();
}

// Email admin
$mail_admin = new PHPMailer(true);

try {
    $mail_admin->Subject = "Action Required - Reimbursement Request";

    $email_msg  = "Hello " . $admin_name . ", \n \n";
    $email_msg .= "A reimbursement request has been created with the following information: \n";
    $email_msg .= "Name: " . $name . " \n";
    $email_msg .= "Email: " . $email . " \n";
    $email_msg .= "The form is attached to this email. ";
    $email_msg .= "Please review these files to ensure that they have paid the correct amount. \n \n";
    $email_msg .= "Best regards, \n";
    $email_msg .= $super_email;

    $mail_admin->Body = $email_msg;
    $mail_admin->setFrom($super_email);
    $mail_admin->addAddress($admin_email, $admin_name);

    // Attach files
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $receipt['tmp_name']);
    $mail_admin->AddAttachment($receipt['tmp_name'], $receipt['name'], 'base64', $mime);

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $docs['tmp_name']);
    $mail_admin->AddAttachment($docs['tmp_name'], $docs['name'], 'base64', $mime);

    $mail_admin->send();
} catch (Exception $e) {
    $result_data->message = 'Error occurred while sending the admin the confirmation email. Please email the admin in the description notifying of this error.';
    echo json_encode($result_data);
    die();
}

$result_data->status = 'success';
echo json_encode($result_data);

mysqli_close($mysqli);
?>