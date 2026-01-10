<?php
/**
 * API: Bulk Send Confirmations
 * Send confirmation emails to multiple participants
 */

require 'db-config.php';
require 'api-send-confirmation.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $participantIds = $input['participant_ids'] ?? [];
    
    if (empty($participantIds)) {
        throw new Exception('No participant IDs provided');
    }
    
    $sent = 0;
    $failed = 0;
    $details = [];
    
    foreach ($participantIds as $participantId) {
        try {
            $result = sendConfirmationEmail($conn, $participantId);
            $sent++;
            $details[] = [
                'participant_id' => $participantId,
                'status' => 'sent',
                'registration_id' => $result['registration_id']
            ];
        } catch (Exception $e) {
            $failed++;
            $details[] = [
                'participant_id' => $participantId,
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Bulk send completed',
        'sent' => $sent,
        'failed' => $failed,
        'details' => $details
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
