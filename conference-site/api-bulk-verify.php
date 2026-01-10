<?php
/**
 * API: Bulk Verify Payments
 * Mark multiple participants' payments as verified
 */

require 'db-config.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $participantIds = $input['participant_ids'] ?? [];
    
    if (empty($participantIds)) {
        throw new Exception('No participant IDs provided');
    }
    
    $verifiedBy = $_SERVER['REMOTE_ADDR'] ?? 'admin';
    $verified = 0;
    $failed = 0;
    
    foreach ($participantIds as $participantId) {
        $stmt = $conn->prepare(
            "UPDATE confirmations 
             SET payment_verified = 1, verified_by = ?, verified_date = NOW()
             WHERE participant_id = ?"
        );
        $stmt->bind_param("ss", $verifiedBy, $participantId);
        
        if ($stmt->execute()) {
            $verified++;
        } else {
            $failed++;
        }
        $stmt->close();
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Bulk verification completed',
        'verified' => $verified,
        'failed' => $failed
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
