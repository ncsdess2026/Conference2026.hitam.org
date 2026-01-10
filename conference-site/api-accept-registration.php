<?php
/**
 * API Endpoint: Accept or Reject Registration
 * 
 * This file handles accepting or rejecting registration responses
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !isset($input['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters: id and action'
    ]);
    exit;
}

$id = intval($input['id']);
$action = $input['action']; // 'accept' or 'reject'

if (!in_array($action, ['accept', 'reject'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action. Must be "accept" or "reject"'
    ]);
    exit;
}

try {
    // Include configuration
    require_once 'config/sheets-config.php';
    
    $newStatus = $action === 'accept' ? 'accepted' : 'rejected';
    
    // Check if using Google Sheets integration
    if (defined('USE_GOOGLE_SHEETS') && USE_GOOGLE_SHEETS && file_exists('vendor/autoload.php')) {
        // Use Google Sheets API to update status
        require_once 'vendor/autoload.php';
        
        $client = new Google_Client();
        $client->setApplicationName('NC-SDESS 2026');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig('config/gsheets-service.json');
        
        $service = new Google_Service_Sheets($client);
        $spreadsheetId = GOOGLE_SHEETS_ID;
        
        // Update the status column (column K - adjust if needed)
        $rowNumber = $id + 1; // +1 for header row
        $range = "Form Responses 1!K{$rowNumber}";
        
        $values = [[$newStatus]];
        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
        $params = ['valueInputOption' => 'RAW'];
        
        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
        
        // Send confirmation email
        sendConfirmationEmail($id, $newStatus);
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration ' . $action . 'ed successfully',
            'updatedCells' => $result->getUpdatedCells()
        ]);
        
    } else {
        // Fallback to database
        require_once 'db-config.php';
        
        $stmt = $conn->prepare("UPDATE registrations SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('si', $newStatus, $id);
        
        if ($stmt->execute()) {
            // Send confirmation email
            sendConfirmationEmail($id, $newStatus);
            
            echo json_encode([
                'success' => true,
                'message' => 'Registration ' . $action . 'ed successfully'
            ]);
        } else {
            throw new Exception('Failed to update database');
        }
        
        $stmt->close();
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

/**
 * Send confirmation email to participant
 */
function sendConfirmationEmail($registrationId, $status) {
    // Include email configuration
    require_once 'config/email-config.php';
    
    // Get registration details
    // This is a placeholder - implement based on your data source
    
    if ($status === 'accepted') {
        $subject = 'Registration Accepted - NC-SDESS 2026';
        $message = 'Congratulations! Your registration for NC-SDESS 2026 has been accepted.';
    } else {
        $subject = 'Registration Update - NC-SDESS 2026';
        $message = 'Thank you for your interest in NC-SDESS 2026. Your registration status has been updated.';
    }
    
    // Send email using your preferred method
    // mail($email, $subject, $message, $headers);
}
?>
