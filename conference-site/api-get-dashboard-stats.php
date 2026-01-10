<?php
/**
 * API: Get Dashboard Statistics
 * Returns total payments, pending verifications, and sent confirmations
 */

require 'db-config.php';
header('Content-Type: application/json');

try {
    // Total verified payments
    $paymentQuery = $conn->query(
        "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'verified'"
    );
    $totalPayments = $paymentQuery->fetch_assoc()['count'];
    
    // Pending verification (payments received but not verified)
    $pendingQuery = $conn->query(
        "SELECT COUNT(*) as count FROM confirmations 
         WHERE payment_verified = 0 AND participant_id IN (
            SELECT participant_id FROM payments WHERE payment_status = 'verified'
         )"
    );
    $pendingVerification = $pendingQuery->fetch_assoc()['count'];
    
    // Confirmations sent
    $sentQuery = $conn->query(
        "SELECT COUNT(*) as count FROM confirmations WHERE confirmation_sent = 1"
    );
    $confirmationsSent = $sentQuery->fetch_assoc()['count'];
    
    echo json_encode([
        'status' => 'success',
        'total_payments' => $totalPayments,
        'pending_verification' => $pendingVerification,
        'confirmations_sent' => $confirmationsSent
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
