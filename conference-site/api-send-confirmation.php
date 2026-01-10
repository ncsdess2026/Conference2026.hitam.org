<?php
/**
 * API: Send Confirmation Email
 * Generate and send confirmation email to a participant
 */

require 'db-config.php';
header('Content-Type: application/json');

function generateRegistrationId() {
    return 'REG-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

function sendConfirmationEmail($conn, $participantId, $resend = false) {
    // Get participant and confirmation details
    $query = "SELECT 
        p.first_name, p.last_name, p.email, p.phone, p.organization, p.category, p.participation_type,
        c.registration_id, c.payment_verified, c.attendance_mode,
        py.transaction_id, py.amount
    FROM participants p
    LEFT JOIN confirmations c ON p.participant_id = c.participant_id
    LEFT JOIN payments py ON p.participant_id = py.participant_id
    WHERE p.participant_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $participantId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Participant not found");
    }
    
    $data = $result->fetch_assoc();
    $stmt->close();
    
    // Generate registration ID if not exists
    if (!$data['registration_id'] || $resend) {
        $registrationId = generateRegistrationId();
    } else {
        $registrationId = $data['registration_id'];
    }
    
    // Get submissions for this participant
    $submissionQuery = "SELECT submission_type, submission_id FROM submissions WHERE participant_id = ?";
    $submissionStmt = $conn->prepare($submissionQuery);
    $submissionStmt->bind_param("s", $participantId);
    $submissionStmt->execute();
    $submissionResult = $submissionStmt->get_result();
    
    $submissions = [];
    while ($sub = $submissionResult->fetch_assoc()) {
        $submissions[] = $sub;
    }
    $submissionStmt->close();
    
    // Build email
    $to = $data['email'];
    $subject = "Registration Confirmation - NC-SDESS 2026";
    
    $submissionDetails = '';
    if (!empty($submissions)) {
        $submissionDetails = "Your Submissions:<br>";
        foreach ($submissions as $sub) {
            $submissionDetails .= "• " . ucfirst(str_replace('_', ' ', $sub['submission_type'])) . ": " . $sub['submission_id'] . "<br>";
        }
        $submissionDetails .= "<br>";
    }
    
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 10px; }
            .header { background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%); color: white; padding: 20px; border-radius: 5px; text-align: center; }
            .content { background: white; padding: 20px; margin-top: 20px; border-radius: 5px; }
            .details { background: #f0f0f0; padding: 15px; border-left: 4px solid #2e7d32; margin: 15px 0; }
            .button { background: #2e7d32; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
            .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Registration Confirmed</h1>
                <h2>NC-SDESS 2026 Conference</h2>
            </div>
            
            <div class='content'>
                <h3>Dear {$data['first_name']} {$data['last_name']},</h3>
                
                <p>Your payment has been verified and your registration is now confirmed! We're excited to have you at NC-SDESS 2026.</p>
                
                <div class='details'>
                    <strong>Registration Number:</strong> {$registrationId}<br>
                    <strong>Email:</strong> {$data['email']}<br>
                    <strong>Phone:</strong> {$data['phone']}<br>
                    <strong>Organization:</strong> {$data['organization']}<br>
                    <strong>Category:</strong> {$data['category']}<br>
                    <strong>Participation Type:</strong> " . ucfirst(str_replace('_', ' ', $data['participation_type'])) . "<br>
                    <strong>Attendance Mode:</strong> " . ucfirst($data['attendance_mode']) . "<br>
                    <strong>Transaction ID:</strong> {$data['transaction_id']}<br>
                    <strong>Amount Paid:</strong> ₹{$data['amount']}<br>
                </div>
                
                {$submissionDetails}
                
                <p><strong>What's Next?</strong></p>
                <ul>
                    <li>You will receive further instructions about the conference schedule</li>
                    <li>Keep your registration number safe for check-in</li>
                    <li>Visit our website regularly for updates</li>
                </ul>
                
                <p>If you have any questions, please contact us at " . ADMIN_EMAIL . "</p>
            </div>
            
            <div class='footer'>
                <p>NC-SDESS 2026 Conference | All Rights Reserved</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . SENDER_NAME . " <" . SENDER_EMAIL . ">" . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    
    // Send email
    $emailSent = mail($to, $subject, $body, $headers);
    
    if (!$emailSent) {
        throw new Exception("Failed to send email");
    }
    
    // Update confirmation record
    $updateStmt = $conn->prepare(
        "UPDATE confirmations 
         SET registration_id = ?, confirmation_sent = 1, confirmation_sent_date = NOW()
         WHERE participant_id = ?"
    );
    $updateStmt->bind_param("ss", $registrationId, $participantId);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Log email
    $logStmt = $conn->prepare(
        "INSERT INTO email_logs (recipient_email, email_type, related_id, subject, status) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $emailType = 'confirmation_email';
    $status = 'sent';
    $logStmt->bind_param("sssss", $to, $emailType, $registrationId, $subject, $status);
    $logStmt->execute();
    $logStmt->close();
    
    return [
        'registration_id' => $registrationId,
        'email' => $to,
        'sent' => true
    ];
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $participantId = $input['participant_id'] ?? '';
    $resend = isset($input['resend']) ? $input['resend'] : false;
    
    if (empty($participantId)) {
        throw new Exception('Missing participant ID');
    }
    
    $result = sendConfirmationEmail($conn, $participantId, $resend);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Confirmation email sent',
        'data' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
