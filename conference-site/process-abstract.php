<?php
/**
 * Abstract Submission Handler with Google Sheets Integration
 * NC-SDESS 2026 Conference
 * 
 * Features:
 * - Validates abstract submissions
 * - Saves to Google Sheets
 * - Sends confirmation emails via SMTP
 * - Generates unique Abstract IDs
 */

// Load dependencies
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// Load configuration
$email_config = require __DIR__ . '/config/email-config.php';
$sheets_config = require __DIR__ . '/config/sheets-config.php';

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/errors.log');
header('Content-Type: application/json');

// Ensure logs directory exists
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error occurred. Please try again later.']);
    exit;
});

// Helper function to sanitize text
function sanitize_text($text) {
    return trim(strip_tags(htmlspecialchars($text, ENT_QUOTES, 'UTF-8')));
}

// Security and Basic Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required fields
$required_fields = ['name', 'email', 'phone', 'organization', 'submissionType', 'agree', 'track', 'title', 'content'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Missing required field: $field";
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Sanitize and validate inputs
$name = sanitize_text($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$phone = sanitize_text($_POST['phone']);
$organization = sanitize_text($_POST['organization']);
$submissionType = sanitize_text($_POST['submissionType']);
$track = sanitize_text($_POST['track']);
$title = sanitize_text($_POST['title']);
$coAuthors = sanitize_text($_POST['coAuthors'] ?? '');
$keywords = sanitize_text($_POST['keywords'] ?? '');
$content = sanitize_text($_POST['content']);
$agree = isset($_POST['agree']) && ($_POST['agree'] === 'on' || $_POST['agree'] === '1');

// Validate email
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate submission type
if (!in_array($submissionType, ['paper', 'poster'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid submission type. Must be paper or poster.']);
    exit;
}

// Word count validation
$word_count = str_word_count($content);
if ($word_count < 150 || $word_count > 400) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Abstract must be between 250-300 words (approximately 150-400 words)']);
    exit;
}

// Validate agreement
if (!$agree) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You must agree to the terms to submit']);
    exit;
}

// Generate unique submission ID
$submission_id = 'ABS-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
$submission_date = date('Y-m-d H:i:s');
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Track names mapping
$track_names = [
    '1' => 'Sustainable Energy Solutions & Clean Power Technologies',
    '2' => 'Smart Electronics & Sensor-Based Solutions for Society',
    '3' => 'Software Systems & Cyber Solutions for Sustainable Development',
    '4' => 'AI & Data-Driven Solutions for Societal Challenges',
    '5' => 'Cyber-Physical Systems & Intelligent Automation for Smart Society',
    '6' => 'Sustainable Materials, Applied Physics & Engineering Innovations',
    '7' => 'Integrated Engineering Solutions for Sustainable Society'
];

$track_name = $track_names[$track] ?? 'Unknown Track';

// Prepare submission data
$submission_data = [
    'submission_id' => $submission_id,
    'timestamp' => $submission_date,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'organization' => $organization,
    'track' => $track_name,
    'title' => $title,
    'coAuthors' => $coAuthors,
    'keywords' => $keywords,
    'content' => $content,
    'submissionType' => ucfirst($submissionType),
    'word_count' => $word_count,
    'ip_address' => $ip_address
];

// ============================================
// STEP 1: Append to Google Sheets
// ============================================

try {
    // Initialize Google Client
    $client = new Google_Client();
    $client->setAuthConfig($sheets_config['service_account_file']);
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);
    $client->setApplicationName('NC-SDESS 2026 Conference');
    
    $service = new Google_Service_Sheets($client);
    
    // Prepare row data (must match column order in sheets-config.php)
    $row_data = [
        $submission_data['submission_id'],
        $submission_data['timestamp'],
        $submission_data['name'],
        $submission_data['email'],
        $submission_data['phone'],
        $submission_data['organization'],
        $submission_data['track'],
        $submission_data['title'],
        $submission_data['coAuthors'],
        $submission_data['keywords'],
        $submission_data['content'],
        $submission_data['submissionType'],
        $submission_data['word_count'],
        $submission_data['ip_address']
    ];
    
    // Append to sheet
    $range = $sheets_config['sheet_name'] . '!A:N'; // A to N columns
    $values = [$row_data];
    $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
    $params = ['valueInputOption' => 'RAW'];
    
    $result = $service->spreadsheets_values->append(
        $sheets_config['spreadsheet_id'],
        $range,
        $body,
        $params
    );
    
    error_log("Successfully saved to Google Sheets: $submission_id");
    
} catch (Exception $e) {
    error_log("Google Sheets error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save submission. Please try again later or contact support.'
    ]);
    exit;
}

// ============================================
// STEP 2: Send Emails via SMTP
// ============================================

// Initialize PHPMailer
function send_email($to, $subject, $html_body, $email_config) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $email_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['smtp_username'];
        $mail->Password = $email_config['smtp_password'];
        $mail->SMTPSecure = $email_config['smtp_encryption'];
        $mail->Port = $email_config['smtp_port'];
        
        // Recipients
        $mail->setFrom($email_config['from_email'], $email_config['from_name']);
        $mail->addAddress($to);
        $mail->addReplyTo($email_config['reply_to'], $email_config['from_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $html_body;
        
        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        error_log("PHPMailer error: " . $e->getMessage());
        return false;
    }
}

// Generate Email 1: Thank You Email
$thank_you_subject = "Thank You for Your Abstract Submission - NC-SDESS 2026";
$thank_you_body = generate_thank_you_email($submission_data, $track_name);

// Generate Email 2: Acceptance Notification
$acceptance_subject = "Your Abstract Submission Accepted - NC-SDESS 2026";
$acceptance_body = generate_acceptance_email($submission_data, $track_name);

// Generate Admin Notification
$admin_subject = "New Abstract Submission - $submission_id";
$admin_body = generate_admin_notification_email($submission_data, $track_name);

// Send emails
$email_1_sent = send_email($email, $thank_you_subject, $thank_you_body, $email_config);
$email_2_sent = send_email($email, $acceptance_subject, $acceptance_body, $email_config);
$admin_email_sent = send_email($email_config['admin_email'], $admin_subject, $admin_body, $email_config);

// Log email status
if ($email_1_sent && $email_2_sent) {
    error_log("Emails sent successfully to: $email");
} else {
    error_log("Failed to send some emails to: $email");
}

// ============================================
// STEP 3: Return Success Response
// ============================================

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Abstract submitted successfully! Check your email for confirmation and acceptance notification.',
    'submission_id' => $submission_id,
    'emails_sent' => $email_1_sent && $email_2_sent
]);
exit;

// ============================================
// Email Template Functions
// ============================================

function generate_thank_you_email($data, $track_name) {
    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; border-left: 4px solid #667eea; }
        .details { background: white; padding: 15px; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .detail-row { margin: 8px 0; }
        .detail-label { font-weight: bold; color: #555; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
        .highlight { background: #fff3cd; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Thank You for Your Submission!</h2>
            <p>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='content'>
            <p>Dear <strong>{$data['name']}</strong>,</p>
            <p>Thank you for submitting your abstract to the 1st National Conference on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026).</p>
            <p>We have successfully received your {$data['submissionType']} submission. Below are your submission details:</p>
            
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Submission ID:</span>
                    <span class='highlight'>{$data['submission_id']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Name:</span> {$data['name']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Email:</span> {$data['email']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Organization:</span> {$data['organization']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Track:</span> {$track_name}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submission Type:</span> {$data['submissionType']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Paper/Poster Title:</span> {$data['title']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submitted On:</span> {$data['timestamp']}
                </div>
            </div>
            
            <p style='margin-top: 20px;'><strong>What's Next?</strong></p>
            <ul>
                <li>Your abstract is under review by our technical committee.</li>
                <li>You will receive an acceptance notification email shortly.</li>
                <li>Please keep your Submission ID safe - you'll need it for all correspondence and registration.</li>
                <li>If submitting a full paper, please prepare according to the guidelines provided.</li>
            </ul>
            
            <p><strong>Important:</strong> Use your Submission ID (<span class='highlight'>{$data['submission_id']}</span>) when registering for the conference.</p>
            
            <p>If you have any questions or concerns, please contact us at <strong>ncsdess2026@hitam.org</strong></p>
        </div>
        
        <div class='footer'>
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; 2026 NC-SDESS Conference | HITAM, Hyderabad</p>
        </div>
    </div>
</body>
</html>
    ";
    return $html;
}

function generate_acceptance_email($data, $track_name) {
    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #2e7d32 100%); color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .success-badge { background: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; display: inline-block; margin: 10px 0; font-weight: bold; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; border-left: 4px solid #4CAF50; }
        .details { background: white; padding: 15px; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .detail-row { margin: 8px 0; }
        .detail-label { font-weight: bold; color: #555; }
        .next-steps { background: #e8f5e9; padding: 15px; margin-top: 15px; border-radius: 5px; border-left: 4px solid #4CAF50; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
        .highlight { background: #fff3cd; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>🎉 Abstract Accepted!</h2>
            <p>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='success-badge'>✓ YOUR ABSTRACT HAS BEEN ACCEPTED</div>
        
        <div style='background: #1B5E20; color: white; padding: 20px; margin-top: 15px; border-radius: 5px; text-align: center; border: 3px solid #FFA500;'>
            <p style='margin: 0 0 10px 0; font-size: 14px; font-weight: bold;'>YOUR ABSTRACT ID:</p>
            <p style='margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 2px; font-family: monospace;'>{$data['submission_id']}</p>
            <p style='margin: 10px 0 0 0; font-size: 12px;'>Save this ID for registration and all correspondence</p>
        </div>
        
        <div class='content'>
            <p>Dear <strong>{$data['name']}</strong>,</p>
            <p>Congratulations! We are pleased to inform you that your {$data['submissionType']} abstract has been <strong>ACCEPTED</strong> for presentation at the 1st National Conference on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026).</p>
            
            <p>Your submission details are as follows:</p>
            
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Submission ID:</span>
                    <span class='highlight'>{$data['submission_id']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Paper/Poster Title:</span> {$data['title']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Track:</span> {$track_name}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submission Type:</span> {$data['submissionType']}
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Authors:</span> " . (!empty($data['coAuthors']) ? $data['name'] . ", " . $data['coAuthors'] : $data['name']) . "
                </div>
            </div>
            
            <div class='next-steps'>
                <h3 style='color: #2e7d32; margin-top: 0;'>📋 Next Steps:</h3>
                <ol>
                    <li><strong>Register for the Conference:</strong> Complete your registration using Submission ID: <span class='highlight'>{$data['submission_id']}</span></li>
                    <li><strong>Pay Registration Fee:</strong> Follow the payment instructions on the registration page.</li>
                    <li><strong>Prepare Full {$data['submissionType']}:</strong> Use the provided template to prepare your presentation.</li>
                    <li><strong>Submit Camera-Ready Manuscript:</strong> Upload your final manuscript before the deadline.</li>
                    <li><strong>Attend the Conference:</strong> Present your work on January 28-29, 2026.</li>
                </ol>
            </div>
            
            <p><strong>📅 Important Dates:</strong></p>
            <ul>
                <li>Abstract Submission Deadline: January 10, 2026</li>
                <li>Full Paper Submission: January 20, 2026</li>
                <li>Conference Dates: January 28-29, 2026</li>
            </ul>
            
            <p style='margin-top: 20px;'><strong>Venue:</strong> HITAM, Gowdavelly, Hyderabad, Telangana</p>
            
            <p>For queries or further information, please contact us at <strong>ncsdess2026@hitam.org</strong></p>
            <p>We look forward to your presence at the conference!</p>
        </div>
        
        <div class='footer'>
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; 2026 NC-SDESS Conference | HITAM, Hyderabad</p>
        </div>
    </div>
</body>
</html>
    ";
    return $html;
}

function generate_admin_notification_email($data, $track_name) {
    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        h2 { color: #667eea; }
        .section { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #667eea; }
        .highlight { background: #fff3cd; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>🔔 New Abstract Submission Received</h2>
        
        <div class='section'>
            <h3>Submission Details</h3>
            <p><strong>Submission ID:</strong> <span class='highlight'>{$data['submission_id']}</span></p>
            <p><strong>Submitted On:</strong> {$data['timestamp']}</p>
            <p><strong>IP Address:</strong> {$data['ip_address']}</p>
        </div>
        
        <div class='section'>
            <h3>Participant Information</h3>
            <p><strong>Name:</strong> {$data['name']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Phone:</strong> {$data['phone']}</p>
            <p><strong>Organization:</strong> {$data['organization']}</p>
        </div>
        
        <div class='section'>
            <h3>Submission Content</h3>
            <p><strong>Title:</strong> {$data['title']}</p>
            <p><strong>Track:</strong> {$track_name}</p>
            <p><strong>Submission Type:</strong> {$data['submissionType']}</p>
            <p><strong>Keywords:</strong> {$data['keywords']}</p>
            <p><strong>Co-Authors:</strong> " . (!empty($data['coAuthors']) ? $data['coAuthors'] : 'None') . "</p>
            <p><strong>Word Count:</strong> {$data['word_count']}</p>
        </div>
        
        <div class='section'>
            <h3>Abstract Content</h3>
            <p>{$data['content']}</p>
        </div>
        
        <p style='margin-top: 20px;'><em>This submission has been automatically saved to Google Sheets.</em></p>
    </div>
</body>
</html>
    ";
    return $html;
}
?>
