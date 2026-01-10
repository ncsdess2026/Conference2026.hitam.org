<?php
// Process Full Paper Submission and Send Automatic Emails
// Configure these settings based on your email setup

// Email Configuration
$CONFERENCE_EMAIL = "ncsdess2026@hitam.org"; // Conference email ID
$CONFERENCE_NAME = "NC-SDESS 2026 Conference"; // Conference name

// Security and Basic Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required fields
$required_fields = ['name', 'email', 'phone', 'organization', 'track', 'title', 'abstractId', 'submissionType', 'agree'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Missing required field: $field";
    }
}

// Check file upload
if (empty($_FILES['paperFile']) || $_FILES['paperFile']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Full paper file is required and must be uploaded successfully";
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
$track = sanitize_text($_POST['track']);
$title = sanitize_text($_POST['title']);
$coAuthors = sanitize_text($_POST['coAuthors'] ?? '');
$abstractId = sanitize_text($_POST['abstractId']);
$submissionType = sanitize_text($_POST['submissionType']);
$agree = $_POST['agree'] === 'on' ? true : false;

// Validate email
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate agreement
if (!$agree) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You must agree to the terms to submit']);
    exit;
}

// Validate submission type
if (!in_array($submissionType, ['half', 'full'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid submission type. Choose half or full paper.']);
    exit;
}

// Basic check for abstract ID pattern
if (!preg_match('/^ABS-\d{4}-[A-Z0-9]{6}$/', $abstractId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid Abstract Submission ID format. Use ABS-YYYY-XXXXXX']);
    exit;
}

// File validation
$allowed_types = [
    'application/pdf',
    'application/msword', // doc
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // docx
];
$max_file_size = 15 * 1024 * 1024; // 15 MB
$file_mime = mime_content_type($_FILES['paperFile']['tmp_name']);

if (!in_array($file_mime, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, DOC, and DOCX are allowed']);
    exit;
}

if ($_FILES['paperFile']['size'] > $max_file_size) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File size exceeds maximum limit of 15 MB']);
    exit;
}

// Generate unique submission ID
$submission_id = 'PAPER-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

// Create uploads directory
$uploads_dir = __DIR__ . '/uploads/papers';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0755, true);
}

// Generate safe filename
$file_ext = pathinfo($_FILES['paperFile']['name'], PATHINFO_EXTENSION);
$safe_filename = $submission_id . '.' . $file_ext;
$file_path = $uploads_dir . '/' . $safe_filename;

// Move uploaded file
if (!move_uploaded_file($_FILES['paperFile']['tmp_name'], $file_path)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to upload full paper file']);
    exit;
}

// Prepare data for storage
$submission_data = [
    'submission_id' => $submission_id,
    'abstract_id' => $abstractId,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'organization' => $organization,
    'track' => $track,
    'title' => $title,
    'coAuthors' => $coAuthors,
    'submissionType' => $submissionType,
    'file_name' => $safe_filename,
    'original_file_name' => $_FILES['paperFile']['name'],
    'file_size' => $_FILES['paperFile']['size'],
    'submission_date' => date('Y-m-d H:i:s'),
    'ip_address' => $_SERVER['REMOTE_ADDR']
];

// Store submission metadata
$submissions_dir = __DIR__ . '/submissions';
if (!is_dir($submissions_dir)) {
    mkdir($submissions_dir, 0755, true);
}

$submission_file = $submissions_dir . '/' . $submission_id . '.json';
file_put_contents($submission_file, json_encode($submission_data, JSON_PRETTY_PRINT));

// Send Email 1: Thank You Email to Participant
$subject_thank_you = "Thank You for Your Full Paper Submission - " . $CONFERENCE_NAME;
$message_thank_you = generate_thank_you_email($submission_data);
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: " . $CONFERENCE_EMAIL . "\r\n";
$headers .= "Reply-To: " . $CONFERENCE_EMAIL . "\r\n";

$email_sent_1 = mail($email, $subject_thank_you, $message_thank_you, $headers);

// Send Email 2: Confirmation/Acceptance Placeholder
$subject_acceptance = "Full Paper Received - " . $CONFERENCE_NAME;
$message_acceptance = generate_acceptance_email($submission_data);
$email_sent_2 = mail($email, $subject_acceptance, $message_acceptance, $headers);

// Send notification to conference admin
$subject_admin = "New Full Paper Submission - " . $submission_id;
$message_admin = generate_admin_notification_email($submission_data);
mail($CONFERENCE_EMAIL, $subject_admin, $message_admin, $headers);

// Check if both emails were sent
if ($email_sent_1 && $email_sent_2) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Full paper submitted successfully! Check your email for confirmation.',
        'submission_id' => $submission_id
    ]);
} else {
    // Log error but still consider submission successful if form was saved
    error_log("Email sending failed for full paper submission: " . $submission_id);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Full paper submitted successfully! Confirmation emails are being processed.',
        'submission_id' => $submission_id,
        'note' => 'Email delivery may be delayed'
    ]);
}

exit;

// Helper Functions
function sanitize_text($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generate_thank_you_email($data) {
    $track_names = [
        '1' => 'Sustainable Energy Solutions & Clean Power Technologies',
        '2' => 'Smart Electronics & Sensor-Based Solutions for Society',
        '3' => 'Software Systems & Cyber Solutions for Sustainable Development',
        '4' => 'AI & Data-Driven Solutions for Societal Challenges',
        '5' => 'Cyber-Physical Systems & Intelligent Automation for Smart Society',
        '6' => 'Sustainable Materials, Applied Physics & Engineering Innovations',
        '7' => 'Integrated Engineering Solutions for Sustainable Society'
    ];

    $track_name = $track_names[$data['track']] ?? 'Selected Track';
    $type_label = ($data['submissionType'] ?? '') === 'half'
        ? 'Half Paper (Extended Abstract 4-6 pages)'
        : 'Full Paper (8-10 pages)';

    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; border-left: 4px solid #667eea; }
        .details { background: white; padding: 15px; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .detail-row { display: flex; margin: 8px 0; }
        .detail-label { font-weight: bold; width: 150px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Thank You for Your Full Paper!</h2>
            <p>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='content'>
            <p>Dear <strong>{$data['name']}</strong>,</p>
            <p>Thank you for submitting your full paper for the 1st National Conference on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026).</p>
            <p>We have successfully received your full paper. Below are your submission details:</p>
            
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Paper ID:</span>
                    <span><strong>{$data['submission_id']}</strong></span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Abstract ID:</span>
                    <span>{$data['abstract_id']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Track:</span>
                    <span>{$track_name}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submission Type:</span>
                    <span>{$type_label}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Paper Title:</span>
                    <span>{$data['title']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submitted On:</span>
                    <span>{$data['submission_date']}</span>
                </div>
            </div>
            
            <p style='margin-top: 20px;'><strong>What's Next?</strong></p>
            <ul>
                <li>Your full paper is under review by our technical committee.</li>
                <li>You will receive an acceptance/feedback email after review.</li>
                <li>Keep your Paper ID and Abstract ID safe for all correspondence.</li>
            </ul>
            
            <p>If you have any questions, contact us at <strong>ncsdess2026@hitam.org</strong></p>
        </div>
        
        <div class='footer'>
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; 2026 NC-SDESS Conference. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
    ";
    return $html;
}

function generate_acceptance_email($data) {
    $track_names = [
        '1' => 'Sustainable Energy Solutions & Clean Power Technologies',
        '2' => 'Smart Electronics & Sensor-Based Solutions for Society',
        '3' => 'Software Systems & Cyber Solutions for Sustainable Development',
        '4' => 'AI & Data-Driven Solutions for Societal Challenges',
        '5' => 'Cyber-Physical Systems & Intelligent Automation for Smart Society',
        '6' => 'Sustainable Materials, Applied Physics & Engineering Innovations',
        '7' => 'Integrated Engineering Solutions for Sustainable Society'
    ];

    $track_name = $track_names[$data['track']] ?? 'Selected Track';
    $type_label = ($data['submissionType'] ?? '') === 'half'
        ? 'Half Paper (Extended Abstract 4-6 pages)'
        : 'Full Paper (8-10 pages)';

    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .success-badge { background: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; display: inline-block; margin: 10px 0; font-weight: bold; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; border-left: 4px solid #4CAF50; }
        .details { background: white; padding: 15px; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .detail-row { display: flex; margin: 8px 0; }
        .detail-label { font-weight: bold; width: 150px; }
        .next-steps { background: #e8f5e9; padding: 15px; margin-top: 15px; border-radius: 5px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Full Paper Received</h2>
            <p>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='success-badge'>Full Paper Logged</div>
        
        <div class='content'>
            <p>Dear <strong>{$data['name']}</strong>,</p>
            <p>Your full paper has been received for review.</p>
            
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Paper ID:</span>
                    <span><strong>{$data['submission_id']}</strong></span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Abstract ID:</span>
                    <span>{$data['abstract_id']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Track:</span>
                    <span>{$track_name}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submission Type:</span>
                    <span>{$type_label}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Paper Title:</span>
                    <span>{$data['title']}</span>
                </div>
            </div>
            
            <div class='next-steps'>
                <h3 style='color: #2e7d32; margin-top: 0;'>Next Steps:</h3>
                <ol>
                    <li>Technical committee will review your full paper.</li>
                    <li>You will receive acceptance/feedback via email.</li>
                    <li>Keep your Paper ID and Abstract ID for registration and correspondence.</li>
                </ol>
            </div>
            
            <p>For queries, contact <strong>ncsdess2026@hitam.org</strong></p>
        </div>
        
        <div class='footer'>
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; 2026 NC-SDESS Conference. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
    ";
    return $html;
}

function generate_admin_notification_email($data) {
    $track_names = [
        '1' => 'Sustainable Energy Solutions & Clean Power Technologies',
        '2' => 'Smart Electronics & Sensor-Based Solutions for Society',
        '3' => 'Software Systems & Cyber Solutions for Sustainable Development',
        '4' => 'AI & Data-Driven Solutions for Societal Challenges',
        '5' => 'Cyber-Physical Systems & Intelligent Automation for Smart Society',
        '6' => 'Sustainable Materials, Applied Physics & Engineering Innovations',
        '7' => 'Integrated Engineering Solutions for Sustainable Society'
    ];

    $track_name = $track_names[$data['track']] ?? 'Selected Track';
    $type_label = ($data['submissionType'] ?? '') === 'half'
        ? 'Half Paper (Extended Abstract 4-6 pages)'
        : 'Full Paper (8-10 pages)';

    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
</head>
<body>
    <h2>New Full Paper Submission Notification</h2>
    <p><strong>Paper ID:</strong> {$data['submission_id']}</p>
    <p><strong>Abstract ID:</strong> {$data['abstract_id']}</p>
    <p><strong>Submitted On:</strong> {$data['submission_date']}</p>
    
    <h3>Participant Details:</h3>
    <ul>
        <li><strong>Name:</strong> {$data['name']}</li>
        <li><strong>Email:</strong> {$data['email']}</li>
        <li><strong>Phone:</strong> {$data['phone']}</li>
        <li><strong>Organization:</strong> {$data['organization']}</li>
    </ul>
    
    <h3>Submission Details:</h3>
    <ul>
        <li><strong>Title:</strong> {$data['title']}</li>
        <li><strong>Track:</strong> {$track_name}</li>
        <li><strong>Submission Type:</strong> {$type_label}</li>
        <li><strong>Co-Authors:</strong> " . (!empty($data['coAuthors']) ? $data['coAuthors'] : 'None') . "</li>
        <li><strong>File Name:</strong> {$data['original_file_name']} ({$data['file_size']} bytes)</li>
    </ul>
    
    <p><strong>IP Address:</strong> {$data['ip_address']}</p>
</body>
</html>
    ";
    return $html;
}
