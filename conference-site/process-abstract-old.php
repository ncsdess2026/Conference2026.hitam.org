<?php
// Process Abstract Submission and Send Automatic Emails
// Configure these settings based on your email setup

// Enable error reporting for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
header('Content-Type: application/json');

// Set error handler to catch any errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $errstr]);
    exit;
});

// Helper function to sanitize text
function sanitize_text($text) {
    return trim(strip_tags(htmlspecialchars($text)));
}

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
$required_fields = ['name', 'email', 'phone', 'organization', 'submissionType', 'agree', 'track', 'title', 'content'];
$submissionType = sanitize_text($_POST['submissionType'] ?? '');

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
$track = sanitize_text($_POST['track'] ?? '');
$title = sanitize_text($_POST['title'] ?? '');
$coAuthors = sanitize_text($_POST['coAuthors'] ?? '');
$keywords = sanitize_text($_POST['keywords'] ?? '');
$content = sanitize_text($_POST['content'] ?? '');
$agree = $_POST['agree'] === 'on' ? true : false;

// Validate email
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate submission type
if (!in_array($submissionType, ['paper', 'poster'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid submission type']);
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
$submission_id = 'ABS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

// Prepare data for storage (you can also store in database)
$submission_data = [
    'submission_id' => $submission_id,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'organization' => $organization,
    'track' => $track,
    'title' => $title,
    'coAuthors' => $coAuthors,
    'keywords' => $keywords,
    'content' => $content,
    'submissionType' => $submissionType,
    'submission_date' => date('Y-m-d H:i:s'),
    'ip_address' => $_SERVER['REMOTE_ADDR']
];

// Store submission (file-based - for production, use database)
$submissions_dir = __DIR__ . '/submissions';
if (!is_dir($submissions_dir)) {
    mkdir($submissions_dir, 0755, true);
}

$submission_file = $submissions_dir . '/' . $submission_id . '.json';
file_put_contents($submission_file, json_encode($submission_data, JSON_PRETTY_PRINT));

// Send Email 1: Thank You Email to Participant
$subject_thank_you = "Thank You for Your Abstract Submission - " . $CONFERENCE_NAME;
$message_thank_you = generate_thank_you_email($submission_data);
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: " . $CONFERENCE_EMAIL . "\r\n";
$headers .= "Reply-To: " . $CONFERENCE_EMAIL . "\r\n";

$email_sent_1 = mail($email, $subject_thank_you, $message_thank_you, $headers);

// Send Email 2: Acceptance Notification Email to Participant
$subject_acceptance = "Your Abstract Submission Accepted - " . $CONFERENCE_NAME;
$message_acceptance = generate_acceptance_email($submission_data);
$email_sent_2 = mail($email, $subject_acceptance, $message_acceptance, $headers);

// Send notification to conference admin
$subject_admin = "New Abstract Submission - " . $submission_id;
$message_admin = generate_admin_notification_email($submission_data);
mail($CONFERENCE_EMAIL, $subject_admin, $message_admin, $headers);

// Always return success if form was saved
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Abstract submitted successfully! Check your email for confirmation and acceptance notification.',
    'submission_id' => $submission_id
]);
exit;
// Helper Functions for Email Generation
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
    $submission_type = ucfirst($data['submissionType']);

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
            <h2>Thank You for Your Submission!</h2>
            <p>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='content'>
            <p>Dear <strong>{$data['name']}</strong>,</p>
            <p>Thank you for submitting your abstract to the 1st National Conference on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026).</p>
            <p>We have successfully received your {$submission_type} submission. Below are your submission details:</p>
            
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Submission ID:</span>
                    <span><strong>{$data['submission_id']}</strong></span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Name:</span>
                    <span>{$data['name']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Email:</span>
                    <span>{$data['email']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Organization:</span>
                    <span>{$data['organization']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Track:</span>
                    <span>{$track_name}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submission Type:</span>
                    <span>{$submission_type}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Paper/Poster Title:</span>
                    <span>{$data['title']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submitted On:</span>
                    <span>{$data['submission_date']}</span>
                </div>
            </div>
            
            <p style='margin-top: 20px;'><strong>What's Next?</strong></p>
            <ul>
                <li>Your abstract is under review by our technical committee.</li>
                <li>You will receive an acceptance notification email shortly.</li>
                <li>If submitting a full paper, please prepare according to the guidelines provided.</li>
                <li>Keep your Submission ID safe - you'll need it for all correspondence.</li>
            </ul>
            
            <p>If you have any questions or concerns, please contact us at <strong>ncsdess2026@hitam.org</strong></p>
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
    $submission_type = ucfirst($data['submissionType']);

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
            <h2>🎉 Abstract Accepted!</h2>
            <p>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='success-badge'>✓ YOUR ABSTRACT HAS BEEN ACCEPTED</div>
        
        <div class='content'>
            <p>Dear <strong>{$data['name']}</strong>,</p>
            <p>Congratulations! We are pleased to inform you that your {$submission_type} abstract has been <strong>ACCEPTED</strong> for the 1st National Conference on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026).</p>
            
            <p>Your submission details are as follows:</p>
            
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Submission ID:</span>
                    <span><strong>{$data['submission_id']}</strong></span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Paper/Poster Title:</span>
                    <span>{$data['title']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Track:</span>
                    <span>{$track_name}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Submission Type:</span>
                    <span>{$submission_type}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Authors:</span>
                    <span>" . (!empty($data['coAuthors']) ? $data['name'] . ", " . $data['coAuthors'] : $data['name']) . "</span>
                </div>
            </div>
            
            <div class='next-steps'>
                <h3 style='color: #2e7d32; margin-top: 0;'>Next Steps:</h3>
                <ol>
                    <li><strong>Register for the Conference:</strong> Complete your registration at our website using this Submission ID.</li>
                    <li><strong>Pay Registration Fee:</strong> Follow the payment instructions provided on the registration page.</li>
                    <li><strong>Prepare Full Paper/Poster:</strong> Use the provided template to prepare your full paper or poster presentation.</li>
                    <li><strong>Submit Camera-Ready Manuscript:</strong> Upload your final manuscript before the deadline.</li>
                    <li><strong>Attend the Conference:</strong> Present your work on " . date('j F Y', strtotime('2026-01-28')) . ".</li>
                </ol>
            </div>
            
            <p><strong>Important Dates:</strong></p>
            <ul>
                <li>Abstract Submission Deadline: January 10, 2026</li>
                <li>Full Paper Submission: January 20, 2026</li>
                <li>Conference Date: January 28-29, 2026</li>
            </ul>
            
            <p>For queries or further information, please contact us at <strong>ncsdess2026@hitam.org</strong></p>
            <p>We look forward to your presence at the conference!</p>
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
    $submission_type = ucfirst($data['submissionType']);

    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
</head>
<body>
    <h2>New Abstract Submission Notification</h2>
    <p><strong>Submission ID:</strong> {$data['submission_id']}</p>
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
        <li><strong>Submission Type:</strong> {$submission_type}</li>
        <li><strong>Keywords:</strong> {$data['keywords']}</li>
        <li><strong>Co-Authors:</strong> " . (!empty($data['coAuthors']) ? $data['coAuthors'] : 'None') . "</li>
    </ul>
    
    <h3>Abstract:</h3>
    <p>{$data['content']}</p>
    
    <p><strong>IP Address:</strong> {$data['ip_address']}</p>
</body>
</html>
    ";
    return $html;
}
?>
