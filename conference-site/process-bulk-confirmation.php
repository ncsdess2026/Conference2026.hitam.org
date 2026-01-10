<?php
// Bulk Registration Confirmation Email Processing
// Reads CSV file and sends confirmation emails to multiple students

// Email Configuration
$CONFERENCE_EMAIL = "ncsdess2026@hitam.org";
$CONFERENCE_NAME = "NC-SDESS 2026 Conference";

// Security check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check file upload
if (empty($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'CSV file is required']);
    exit;
}

// Check file type
$file_type = mime_content_type($_FILES['csvFile']['tmp_name']);
if ($file_type !== 'text/plain' && $file_type !== 'text/csv' && 
    !str_ends_with($_FILES['csvFile']['name'], '.csv')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Only CSV files are allowed']);
    exit;
}

// Read CSV file
$csv_file = fopen($_FILES['csvFile']['tmp_name'], 'r');
if (!$csv_file) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Failed to read CSV file']);
    exit;
}

// Get CSV headers
$headers = fgetcsv($csv_file);
if (!$headers) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'CSV file is empty or invalid']);
    exit;
}

// Map CSV columns
$column_map = array_flip($headers);
$required_columns = ['name', 'email', 'phone', 'organization', 'category', 'participationType', 'transactionId', 'amountPaid'];
foreach ($required_columns as $col) {
    if (!isset($column_map[$col])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required column: $col"]);
        exit;
    }
}

// Process each row
$results = [
    'total' => 0,
    'sent' => 0,
    'failed' => 0,
    'details' => []
];

// Create registrations directory
$registrations_dir = __DIR__ . '/registrations';
if (!is_dir($registrations_dir)) {
    mkdir($registrations_dir, 0755, true);
}

while (($row = fgetcsv($csv_file)) !== FALSE) {
    $results['total']++;
    
    // Skip empty rows
    if (empty(array_filter($row))) {
        continue;
    }

    // Extract data
    $data = [];
    foreach ($headers as $idx => $header) {
        $data[$header] = isset($row[$idx]) ? trim($row[$idx]) : '';
    }

    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $results['failed']++;
        $results['details'][] = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 'failed',
            'reason' => 'Invalid email address'
        ];
        continue;
    }

    // Sanitize inputs
    $data = array_map('sanitize_text', $data);

    // Generate registration number
    $registration_number = 'REG-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    // Prepare confirmation data
    $confirmation_data = [
        'registration_number' => $registration_number,
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'organization' => $data['organization'],
        'category' => $data['category'],
        'participation_type' => $data['participationType'],
        'attendance_mode' => $data['attendanceMode'] ?? 'Offline',
        'abstract_id' => $data['abstractId'] ?? '',
        'paper_id' => $data['paperId'] ?? '',
        'poster_id' => $data['posterId'] ?? '',
        'transaction_id' => $data['transactionId'],
        'amount_paid' => $data['amountPaid'],
        'confirmed_date' => date('Y-m-d H:i:s'),
        'verified_by' => $data['verifiedBy'] ?? 'Bulk Upload'
    ];

    // Store confirmation
    $confirmation_file = $registrations_dir . '/' . $registration_number . '.json';
    file_put_contents($confirmation_file, json_encode($confirmation_data, JSON_PRETTY_PRINT));

    // Send email
    $subject = "Registration Confirmed - " . $CONFERENCE_NAME;
    $message = generate_confirmation_email($confirmation_data);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . $CONFERENCE_EMAIL . "\r\n";
    $headers .= "Reply-To: " . $CONFERENCE_EMAIL . "\r\n";

    $email_sent = @mail($data['email'], $subject, $message, $headers);

    if ($email_sent) {
        $results['sent']++;
        $results['details'][] = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 'sent',
            'registration_number' => $registration_number
        ];
    } else {
        $results['failed']++;
        $results['details'][] = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 'failed',
            'reason' => 'Email sending failed',
            'registration_number' => $registration_number
        ];
    }
}

fclose($csv_file);

// Send admin notification
$admin_subject = "Bulk Registration Confirmation - " . $results['sent'] . " sent, " . $results['failed'] . " failed";
$admin_message = generate_admin_bulk_report($results);
mail($CONFERENCE_EMAIL, $admin_subject, $admin_message, $headers);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => "Bulk confirmation process completed. {$results['sent']} emails sent, {$results['failed']} failed.",
    'results' => $results
]);

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
            </div>
            
            <div class='important-info'>
                <h3 style='margin-top: 0; color: #856404;'>Conference Details</h3>
                <p style='margin: 5px 0;'><strong>Date:</strong> January 28-29, 2026</p>
                <p style='margin: 5px 0;'><strong>Venue:</strong> Hyderabad Institute of Technology and Management (HITAM)</p>
            </div>
            
            <p style='font-size: 15px; margin-top: 25px;'>For any queries, contact us at <strong>ncsdess2026@hitam.org</strong></p>
            <p style='font-size: 16px; font-weight: bold; color: #2e7d32;'>We look forward to welcoming you at the conference!</p>
        </div>
        
        <div class='footer'>
            <p>This is an official confirmation email from NC-SDESS 2026 Conference.</p>
            <p>&copy; 2026 NC-SDESS Conference. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
    ";
    return $html;
}

function generate_admin_bulk_report($results) {
    $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
</head>
<body>
    <h2>Bulk Registration Confirmation Report</h2>
    <p><strong>Total Processed:</strong> {$results['total']}</p>
    <p><strong>Successfully Sent:</strong> {$results['sent']}</p>
    <p><strong>Failed:</strong> {$results['failed']}</p>
    <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
    
    <h3>Details:</h3>
    <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
        <tr style='background-color: #f0f0f0;'>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Registration Number / Reason</th>
        </tr>";
    
    foreach ($results['details'] as $detail) {
        $status_color = $detail['status'] === 'sent' ? '#4CAF50' : '#f44336';
        $info = isset($detail['registration_number']) ? $detail['registration_number'] : $detail['reason'];
        $html .= "<tr>
            <td>{$detail['name']}</td>
            <td>{$detail['email']}</td>
            <td style='color: {$status_color}; font-weight: bold;'>" . strtoupper($detail['status']) . "</td>
            <td>{$info}</td>
        </tr>";
    }
    
    $html .= "</table>
</body>
</html>
    ";
    return $html;
}
?>
