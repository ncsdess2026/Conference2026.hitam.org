<?php
/**
 * Webhook Receiver for Payment Data
 * This endpoint receives payment notifications from Google Forms or payment gateway
 * and automatically stores them in the database
 */

require 'db-config.php';
header('Content-Type: application/json');

// Log all incoming requests for debugging
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verify the webhook is legitimate (add security checks as needed)
if (!isset($data) || empty($data)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No data provided']);
    exit;
}

// Generate unique participant ID if not provided
function generateParticipantId() {
    return 'PART-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

// Generate registration ID for confirmation
function generateRegistrationId() {
    return 'REG-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

try {
    // Extract payment data from webhook
    $firstName = sanitize($data['first_name'] ?? '');
    $lastName = sanitize($data['last_name'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $phone = sanitize($data['phone'] ?? '');
    $organization = sanitize($data['organization'] ?? '');
    $category = sanitize($data['category'] ?? ''); // Student, Faculty, Professional, etc.
    $participationType = sanitize($data['participation_type'] ?? ''); // Paper, Poster, Abstract, etc.
    $transactionId = sanitize($data['transaction_id'] ?? '');
    $amountPaid = floatval($data['amount_paid'] ?? 0);
    $attendanceMode = sanitize($data['attendance_mode'] ?? 'Online'); // Online, In-person, Hybrid
    
    // Validate required fields
    $required = ['first_name', 'last_name', 'email', 'phone', 'organization', 'category', 'transaction_id', 'amount_paid'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Check if participant already exists
    $checkStmt = $conn->prepare("SELECT participant_id FROM participants WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $participantId = $row['participant_id'];
    } else {
        $participantId = generateParticipantId();
        
        // Insert new participant
        $insertParticipant = $conn->prepare(
            "INSERT INTO participants (participant_id, first_name, last_name, email, phone, organization, category, participation_type) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insertParticipant->bind_param("ssssssss", $participantId, $firstName, $lastName, $email, $phone, $organization, $category, $participationType);
        if (!$insertParticipant->execute()) {
            throw new Exception("Failed to insert participant: " . $insertParticipant->error);
        }
        $insertParticipant->close();
    }
    $checkStmt->close();
    
    // Check if payment already recorded
    $paymentCheck = $conn->prepare("SELECT id FROM payments WHERE transaction_id = ?");
    $paymentCheck->bind_param("s", $transactionId);
    $paymentCheck->execute();
    $paymentResult = $paymentCheck->get_result();
    
    if ($paymentResult->num_rows === 0) {
        // Insert payment record
        $paymentStatus = 'verified'; // Auto-verify webhook payments
        $insertPayment = $conn->prepare(
            "INSERT INTO payments (participant_id, transaction_id, amount, payment_status, payment_method) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $paymentMethod = $data['payment_method'] ?? 'Online';
        $insertPayment->bind_param("ssdss", $participantId, $transactionId, $amountPaid, $paymentStatus, $paymentMethod);
        if (!$insertPayment->execute()) {
            throw new Exception("Failed to insert payment: " . $insertPayment->error);
        }
        $insertPayment->close();
    }
    $paymentCheck->close();
    
    // Create or update confirmation record
    $registrationId = generateRegistrationId();
    $confirmCheck = $conn->prepare("SELECT registration_id FROM confirmations WHERE participant_id = ?");
    $confirmCheck->bind_param("s", $participantId);
    $confirmCheck->execute();
    $confirmResult = $confirmCheck->get_result();
    
    if ($confirmResult->num_rows === 0) {
        $paymentVerified = true;
        $insertConfirmation = $conn->prepare(
            "INSERT INTO confirmations (registration_id, participant_id, payment_verified, attendance_mode) 
             VALUES (?, ?, ?, ?)"
        );
        $insertConfirmation->bind_param("ssis", $registrationId, $participantId, $paymentVerified, $attendanceMode);
        if (!$insertConfirmation->execute()) {
            throw new Exception("Failed to insert confirmation: " . $insertConfirmation->error);
        }
        $insertConfirmation->close();
    }
    $confirmCheck->close();
    
    // Log successful webhook
    logEmail($conn, $email, 'payment_webhook_received', $transactionId, 'Payment received via webhook', '', 'success');
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Payment data received and processed',
        'participant_id' => $participantId,
        'transaction_id' => $transactionId
    ]);
    
} catch (Exception $e) {
    // Log error
    logEmail($conn, $email ?? 'unknown', 'payment_webhook_error', $transactionId ?? 'unknown', 'Webhook Error', $e->getMessage(), 'error');
    
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Utility function to sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Log email/events
function logEmail($conn, $email, $type, $relatedId, $subject, $body, $status) {
    $stmt = $conn->prepare("INSERT INTO email_logs (recipient_email, email_type, related_id, subject, body, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $email, $type, $relatedId, $subject, $body, $status);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
