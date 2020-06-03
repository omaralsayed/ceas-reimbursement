<?php
error_reporting(-1);
ini_set('display_errors', '1');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

set_include_path('./includes/');
require_once('mysqli.php');
require_once('check_file.php');
require_once('PHPMailer/Exception.php');
require_once('PHPMailer/PHPMailer.php');
require_once('fpdm/fpdm.php');

DEFINE('RECEIPT_MAX_FILE_SIZE', 6);
DEFINE('ATTENDANCE_MAX_FILE_SIZE', 6);

// Initialize member variables
$name  = '';
$position  = '';
$email = '';
$m_id  = '';

// Initialize expenditure variables
$date = '';
$vendor = '';
$amount = '';
$description = '';
$budgeted = false;
$direct = false;

// Initialize document variables
$receipt = null;
$docs = null;

$name = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['name'])));
$position = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['position'])));
$email = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['email'])));
$m_id = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['mId'])));
$date = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['date'])));
$vendor = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['vendor'])));
$amount = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['amount'])));
$description = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['description'])));
$budgeted = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['budgetedExpenseYes'])));
$direct = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['directDeposit'])));
$officer_name = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['officerName'])));
$officer_position = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['officerPosition'])));
$address = mysqli_real_escape_string($mysqli, strip_tags(trim($_POST['address'])));
$receipt = $_FILES['receipt'];
if (isset($_FILES['docs'])) {
    $docs = $_FILES['docs'];
}

$receipt_mime_types = array(
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'png'  => 'image/png',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg'
);

$docs_mime_types = array(
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'png'  => 'image/png',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg'
);

// Initialize data validation and SQL result
if (!isset($result_data))
    $result_data = new stdClass();
$result_data->status = 'error';
$result_data->message = '';

// Check name
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $name)) {
    $result_data->message = 'Your name is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your name is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check position
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $position)) {
    $result_data->message = 'Your position is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your position is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check email address
if(!preg_match('/^[\w\W]+@[\w\W\d]{1,254}$/', $email)) {
    $result_data->message = 'Your email address is invalid. Please use an email in the following format: <>@<>. '
        . 'Your email is also limited to 254 characters.';
    echo json_encode($result_data);
    die();
}

// Check M#
if (!preg_match("/^M[0-9]{8}$/", $m_id)) {
    $result_data->message = 'Your M# is invalid. Please only use official format (Mxxxxxxxx) with an optional '
        . 'apostrophe or period.';
    echo json_encode($result_data);
    die();
}

// Check date
if(!preg_match('/^(19|20)\d\d([-])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])$/', $date)) {
    $result_data->message = 'Your expenditure date is invalid. Please format it in the following way: YYYY-MM-DD.';
    echo json_encode($result_data);
    die();
}

// Check vendor
if (!preg_match("/^[\w\ \'\.]{1,128}$/", $vendor)) {
    $result_data->message = 'Your vendor name is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your vendor is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check amount
if (!preg_match("/^[0-9]{0,10}.[0-9]{0,10}$/", $amount)) {
    $result_data->message = 'Your amount is invalid. Please only use only numerical characters';
    echo json_encode($result_data);
    die();
}

// Check description
if (!preg_match("/^[\w\ \'\.]{1,500}$/", $description)) {
    $result_data->message = 'Your description is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your description is also limited to 500 characters.';
    echo json_encode($result_data);
    die();
}

// Check receipt file
$receipt_check_result = checkFile($receipt, RECEIPT_MAX_FILE_SIZE, $receipt_mime_types);
if (!$receipt_check_result->file_safe) {
    $result_data->message = $receipt_check_result->message;
    echo json_encode($result_data);
    die();
}

// Check docs file
if ($docs) {
    $docs_check_result = checkFile($docs, ATTENDANCE_MAX_FILE_SIZE, $docs_mime_types);
    if (!$docs_check_result->file_safe) {
        $result_data->message = $docs_check_result->message;
        echo json_encode($result_data);
        die();
    }
}

// Check officer name (if provided)
if ($officer_name && !preg_match("/^[\w\ \'\.]{1,128}$/", $officer_name)) {
    $result_data->message = 'The officer name that you provided is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your officer name is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check officer position (if provided)
if ($officer_position && !preg_match("/^[\w\ \'\.]{1,128}$/", $officer_position)) {
    $result_data->message = 'The officer position that you provided is invalid. Please only use latin characters a-z with an optional '
        . 'apostrophe or period. Your officer position is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Check mailing address (if provided)
if ($address && !preg_match("/^[A-Za-z0-9'\.\-\s\,]{1,128}$/", $address)) {
    $result_data->message = 'The address that you provided is invalid. Please only use latin characters a-z and numbers with optional '
        . 'apostrophes or periods. Your address is also limited to 128 characters.';
    echo json_encode($result_data);
    die();
}

// Get admin data
$admin_name  = '';
$admin_email = '';
$super_email = '';

// Get file names
$receiptText = "reciept-" . $receipt['name'];
$docsText = '';
if ($docs) {
    $docsText = "supporting-docs-" . $docs['name'];
}

$sql = 'SELECT `admin_name`, `admin_email`, `super_email` FROM `reimbursement_admin`';
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

// Insert form data
$sql = 'INSERT INTO `reimbursement_main` (`name`, `position`, `email`, `mid`, `date`, `vendor`, `amount`, `description`, `status`,`type`,
`receipt_name`,`document_name`,`officer_name`,`officer_position`,`address`)'
. "VALUES ('".$name."','".$position."','".$email."','".$m_id."','".$date."','".$vendor."','".$amount."','".$description."','".$budgeted."',
'".$direct."','".$receiptText."','".$docsText."','".$officer_name."','".$officer_position."','".$address."')";

$result = $mysqli->query($sql);

if (!$result) {
    $result_data->message = 'Error occurred while submitting your reimbursement request. Please try again. '
    . 'If the error persists, email the admin in the description.';
    echo json_encode($result_data);
    die();
}

// Fill PDF
$output_pdf_path = '../documents/reimbursement-' . uniqid() . '.pdf';

try {
    $fields = array(
        'name' => $name,
        'title' => $position,
        'date_submitted' => date("m/d/Y"),
        'm_number' => $m_id,
        'expenditure_date' => date("m/d/Y", strtotime($date)),
        'expenditure_vendor' => $vendor,
        'expenditure_amount' => $amount,
        'expenditure_description' => $description,
        'approver_name' => $officer_name,
        'approver_position' => $officer_position,
        'address' => $address,
    );

    if ($budgeted) {
        $fields['checkbox_budgeted_yes'] = true;
    } else {
        $fields['checkbox_budgeted_no'] = true;
    }
    
    if ($address) {
        $fields['checkbox_payment_method_check'] = true;
    } else {
        $fields['checkbox_payment_method_dd'] = true;
    }
    
    $pdf = new FPDM('../documents/reimbursement-unfilled.pdf');
    $pdf->useCheckboxParser = true;
    $pdf->Load($fields, true);
    $pdf->Merge();
    $pdf->Output('F', $output_pdf_path);
} catch (Exception $e) {
    $result_data->message = 'Error occurred while generating the reimbursement PDF. Please email the admin in the description notifying of this error.';
    echo json_encode($result_data);
    die();
}

// Email user
$mail = new PHPMailer(true);

try {
    $mail->Subject = "Reimbursement Request Recieved";

    $email_msg = "Hello " . $name . ", \n \n";
    $email_msg .= "This email is to confirm that we have recieved your reimbursement request. ";
    $email_msg .= "Your transaction will be evaluated and if we require any further information, we will contact you. \n \n";
    $email_msg .= "If you have any questions, feel free to reply back to this email. \n \n";
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
    $mail_admin->Subject = "Reimbursement Request Recieved";

    $email_msg  = "Hello " . $admin_name . ", \n \n";
    $email_msg .= "A reimbursement request has been created with the following information: \n \n";
    $email_msg .= "Name: " . $name . " \n";
    $email_msg .= "Position: " . $position . " \n";
    $email_msg .= "Email: " . $email . " \n";
    $email_msg .= "M#: " . $m_id . " \n";
    $email_msg .= "Date: " . date("m/d/Y", strtotime($date)) . " \n";
    $email_msg .= "Vendor: " . $vendor . " \n";
    $email_msg .= "Amount: " . $amount . " \n";
    $email_msg .= "Description: " . $description . " \n";
    $email_msg .= "Status: " . $budgeted . " \n";
    $email_msg .= "Approved By: " . $officer_name . " \n";
    $email_msg .= "Approver Title: " . $officer_position . " \n";
    $email_msg .= "Delivery Type: " . $direct . " \n";
    $email_msg .= "Delivery Address: " . $address . " \n \n";
    $email_msg .= "Supporting documents are attached to this email. ";
    $email_msg .= "Please review these files to ensure that they have paid the correct amount. \n \n";
    $email_msg .= "Best regards, \n";
    $email_msg .= $super_email;

    $mail_admin->Body = $email_msg;
    $mail_admin->setFrom($super_email);
    $mail_admin->addAddress($admin_email, $admin_name);

    // Attach files
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $receipt['tmp_name']);
    $mail_admin->AddAttachment($receipt['tmp_name'], $receiptText, 'base64', $mime);

    if ($docs) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $docs['tmp_name']);
        $mail_admin->AddAttachment($docs['tmp_name'], $docsText, 'base64', $mime);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $output_pdf_path);
    $mail_admin->AddAttachment($output_pdf_path, 'reimbursement.pdf', 'base64', $mime);

    $mail_admin->send();
} catch (Exception $e) {
    $result_data->message = 'Error occurred while sending the admin the confirmation email. Please email the admin in the description notifying of this error.';
    echo json_encode($result_data);
    die();
}

if (!unlink($output_pdf_path)) {
    $result_data->message = 'Error occurred while trying to delete the temporary reimbursement file. Please email the admin in the description notifying of this error.';
    echo json_encode($result_data);
    die();
}

$result_data->status = 'success';
echo json_encode($result_data);

mysqli_close($mysqli);
?>