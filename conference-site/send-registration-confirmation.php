<?php
// Send Registration Confirmation Email (After Manual Payment Verification)
// Use this script after verifying payment from Google Form responses

// Email Configuration
$CONFERENCE_EMAIL = "ncsdess2026@hitam.org";
$CONFERENCE_NAME = "NC-SDESS 2026 Conference";

// Security check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required fields
$required_fields = ['name', 'email', 'phone', 'organization', 'category', 'participationType', 'transactionId', 'amountPaid'];
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

// Sanitize inputs
$name = sanitize_text($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$phone = sanitize_text($_POST['phone']);
$organization = sanitize_text($_POST['organization']);
$category = sanitize_text($_POST['category']);
$participationType = sanitize_text($_POST['participationType']);
$attendanceMode = sanitize_text($_POST['attendanceMode'] ?? 'Offline');
$abstractId = sanitize_text($_POST['abstractId'] ?? '');
$paperId = sanitize_text($_POST['paperId'] ?? '');
$posterId = sanitize_text($_POST['posterId'] ?? '');
$transactionId = sanitize_text($_POST['transactionId']);
$amountPaid = sanitize_text($_POST['amountPaid']);

// Validate email
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Generate registration confirmation number
$registration_number = 'REG-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

// Prepare confirmation data
$confirmation_data = [
    'registration_number' => $registration_number,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'organization' => $organization,
    'category' => $category,
    'participation_type' => $participationType,
    'attendance_mode' => $attendanceMode,
    'abstract_id' => $abstractId,
    'paper_id' => $paperId,
    'poster_id' => $posterId,
    'transaction_id' => $transactionId,
    'amount_paid' => $amountPaid,
    'confirmed_date' => date('Y-m-d H:i:s'),
    'verified_by' => $_POST['verifiedBy'] ?? 'Admin'
];

// Store confirmation
$confirmations_dir = __DIR__ . '/registrations';
if (!is_dir($confirmations_dir)) {
    mkdir($confirmations_dir, 0755, true);
}

$confirmation_file = $confirmations_dir . '/' . $registration_number . '.json';
file_put_contents($confirmation_file, json_encode($confirmation_data, JSON_PRETTY_PRINT));

// Send confirmation email to participant
$subject = "Registration Confirmed - " . $CONFERENCE_NAME;
$message = generate_confirmation_email($confirmation_data);
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: " . $CONFERENCE_EMAIL . "\r\n";
$headers .= "Reply-To: " . $CONFERENCE_EMAIL . "\r\n";

$email_sent = mail($email, $subject, $message, $headers);

// Send copy to admin
$admin_subject = "Registration Confirmed - " . $registration_number;
$admin_message = generate_admin_confirmation_email($confirmation_data);
mail($CONFERENCE_EMAIL, $admin_subject, $admin_message, $headers);

if ($email_sent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Registration confirmation email sent successfully!',
        'registration_number' => $registration_number
    ]);
} else {
    error_log("Failed to send confirmation email to: " . $email);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send confirmation email. Registration saved but email failed.',
        'registration_number' => $registration_number
    ]);
}

exit;

// Helper Functions
function sanitize_text($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generate_confirmation_email($data) {
    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%); color: white; padding: 25px; text-align: center; border-radius: 8px; }
        .success-badge { background: #4CAF50; color: white; padding: 15px 25px; border-radius: 8px; display: inline-block; margin: 15px 0; font-weight: bold; font-size: 18px; }
        .content { background: #f9f9f9; padding: 25px; margin-top: 20px; border-left: 5px solid #4CAF50; border-radius: 5px; }
        .details { background: white; padding: 20px; margin-top: 20px; border: 2px solid #4CAF50; border-radius: 8px; }
        .detail-row { display: flex; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; width: 180px; color: #2e7d32; }
        .highlight-box { background: #e8f5e9; padding: 15px; margin: 20px 0; border-left: 4px solid #4CAF50; border-radius: 5px; }
        .important-info { background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107; border-radius: 5px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; text-align: center; color: #666; font-size: 13px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1 style='margin: 0;'>🎉 Registration Confirmed!</h1>
            <p style='margin: 10px 0 0 0; font-size: 18px;'>NC-SDESS 2026 Conference</p>
        </div>
        
        <div class='success-badge'>✓ YOUR REGISTRATION IS COMPLETE</div>
        
        <div class='content'>
            <p style='font-size: 16px;'>Dear <strong>{$data['name']}</strong>,</p>
            <p style='font-size: 15px;'>Congratulations! Your registration for the <strong>1st National Conference on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026)</strong> has been successfully confirmed.</p>
            <p style='font-size: 15px;'>Your payment has been verified and your registration is now <strong style='color: #4CAF50;'>ACTIVE</strong>.</p>
            
            <div class='highlight-box'>
                <h3 style='margin-top: 0; color: #2e7d32;'>Your Registration Number</h3>
                <p style='font-size: 24px; font-weight: bold; color: #2e7d32; text-align: center; margin: 10px 0;'>{$data['registration_number']}</p>
                <p style='font-size: 13px; text-align: center; margin: 5px 0;'>Please save this number for conference check-in</p>
            </div>
            
            <div class='details'>
                <h3 style='margin-top: 0; color: #2e7d32;'>Registration Details</h3>
                <div class='detail-row'>
                    <span class='detail-label'>Name:</span>
                    <span>{$data['name']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Email:</span>
                    <span>{$data['email']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Phone:</span>
                    <span>{$data['phone']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Organization:</span>
                    <span>{$data['organization']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Category:</span>
                    <span>{$data['category']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Participation Type:</span>
                    <span>{$data['participation_type']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Attendance Mode:</span>
                    <span>{$data['attendance_mode']}</span>
                </div>
                " . (!empty($data['abstract_id']) ? "<div class='detail-row'><span class='detail-label'>Abstract ID:</span><span>{$data['abstract_id']}</span></div>" : "") . "
                " . (!empty($data['paper_id']) ? "<div class='detail-row'><span class='detail-label'>Paper ID:</span><span>{$data['paper_id']}</span></div>" : "") . "
                " . (!empty($data['poster_id']) ? "<div class='detail-row'><span class='detail-label'>Poster ID:</span><span>{$data['poster_id']}</span></div>" : "") . "
                <div class='detail-row'>
                    <span class='detail-label'>Transaction ID:</span>
                    <span>{$data['transaction_id']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Amount Paid:</span>
                    <span>₹{$data['amount_paid']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Confirmed On:</span>
                    <span>{$data['confirmed_date']}</span>
                </div>
            </div>
            
            <div class='important-info'>
                <h3 style='margin-top: 0; color: #856404;'>Conference Details</h3>
                <p style='margin: 5px 0;'><strong>Date:</strong> January 28-29, 2026</p>
                <p style='margin: 5px 0;'><strong>Venue:</strong> Hyderabad Institute of Technology and Management (HITAM)</p>
                <p style='margin: 5px 0;'><strong>Address:</strong> HITAM Campus, Hyderabad</p>
            </div>
            
            <div style='background: #e3f2fd; padding: 15px; margin: 20px 0; border-left: 4px solid #2196F3; border-radius: 5px;'>
                <h3 style='margin-top: 0; color: #1976d2;'>Important Instructions</h3>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Bring a printed or digital copy of this email for conference check-in</li>
                    <li>Your Registration Number (<strong>{$data['registration_number']}</strong>) will be required at the registration desk</li>
                    <li>Arrive at least 30 minutes before the conference start time</li>
                    <li>Carry a valid ID proof for verification</li>
                    " . ($data['participation_type'] === 'Paper' ? "<li>Be ready for your paper presentation at your scheduled time</li>" : "") . "
                    " . ($data['participation_type'] === 'Poster' ? "<li>Set up your poster at the designated poster session area</li>" : "") . "
                </ul>
            </div>
            
            <p style='font-size: 15px; margin-top: 25px;'>For any queries or assistance, please contact us at <strong>ncsdess2026@hitam.org</strong></p>
            <p style='font-size: 16px; font-weight: bold; color: #2e7d32;'>We look forward to welcoming you at the conference!</p>
        </div>
        
        <div class='footer'>
            <p>This is an official confirmation email from NC-SDESS 2026 Conference.</p>
            <p>&copy; 2026 NC-SDESS Conference. All rights reserved.</p>
            <p>Hyderabad Institute of Technology and Management (HITAM)</p>
        </div>
    </div>
</body>
</html>
    ";
    return $html;
}

function generate_admin_confirmation_email($data) {
    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
</head>
<body>
    <h2>Registration Confirmation Sent</h2>
    <p><strong>Registration Number:</strong> {$data['registration_number']}</p>
    <p><strong>Confirmed On:</strong> {$data['confirmed_date']}</p>
    <p><strong>Verified By:</strong> {$data['verified_by']}</p>
    
    <h3>Participant Details:</h3>
    <ul>
        <li><strong>Name:</strong> {$data['name']}</li>
        <li><strong>Email:</strong> {$data['email']}</li>
        <li><strong>Phone:</strong> {$data['phone']}</li>
        <li><strong>Organization:</strong> {$data['organization']}</li>
        <li><strong>Category:</strong> {$data['category']}</li>
        <li><strong>Participation Type:</strong> {$data['participation_type']}</li>
        <li><strong>Attendance Mode:</strong> {$data['attendance_mode']}</li>
    </ul>
    
    <h3>Submission IDs:</h3>
    <ul>
        " . (!empty($data['abstract_id']) ? "<li><strong>Abstract ID:</strong> {$data['abstract_id']}</li>" : "") . "
        " . (!empty($data['paper_id']) ? "<li><strong>Paper ID:</strong> {$data['paper_id']}</li>" : "") . "
        " . (!empty($data['poster_id']) ? "<li><strong>Poster ID:</strong> {$data['poster_id']}</li>" : "") . "
    </ul>
    
    <h3>Payment Details:</h3>
    <ul>
        <li><strong>Transaction ID:</strong> {$data['transaction_id']}</li>
        <li><strong>Amount Paid:</strong> ₹{$data['amount_paid']}</li>
    </ul>
    
    <p>Confirmation email has been sent to the participant.</p>
</body>
</html>
    ";
    return $html;
}
?>
