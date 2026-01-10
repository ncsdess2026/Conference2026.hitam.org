<?php
/**
 * API: Verify Payment
 * Mark a participant's payment as verified
 */

require 'db-config.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $participantId = $input['participant_id'] ?? '';
    
    if (empty($participantId)) {
        throw new Exception('Missing participant ID');
    }
    
    $verifiedBy = $_SERVER['REMOTE_ADDR'] ?? 'admin'; // IP address of verifier
    
    // Update confirmation status
    $stmt = $conn->prepare(
        "UPDATE confirmations 
         SET payment_verified = 1, verified_by = ?, verified_date = NOW()
         WHERE participant_id = ?"
    );
    $stmt->bind_param("ss", $verifiedBy, $participantId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Payment verified',
            'participant_id' => $participantId
        ]);
    } else {
        throw new Exception('Failed to verify payment: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
