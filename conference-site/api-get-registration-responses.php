<?php
/**
 * API Endpoint: Get Registration Responses from Google Forms
 * 
 * This file retrieves registration form responses from Google Sheets
 * connected to the Google Form and returns them as JSON.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include Google Sheets configuration
require_once 'config/sheets-config.php';

try {
    // Check if using Google Sheets integration
    if (defined('USE_GOOGLE_SHEETS') && USE_GOOGLE_SHEETS && file_exists('vendor/autoload.php')) {
        // Use Google Sheets API
        require_once 'vendor/autoload.php';
        
        $client = new Google_Client();
        $client->setApplicationName('NC-SDESS 2026');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig('config/gsheets-service.json');
        
        $service = new Google_Service_Sheets($client);
        
        // Spreadsheet ID from the Google Form responses sheet
        // Extract from URL: https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $range = 'Form Responses 1!A:K'; // Adjust range based on your form columns
        
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        
        if (empty($values)) {
            echo json_encode([
                'success' => false,
                'message' => 'No data found in the spreadsheet.'
            ]);
            exit;
        }
        
        // First row contains headers
        $headers = array_shift($values);
        
        // Process rows into structured data
        $responses = [];
        foreach ($values as $index => $row) {
            // Ensure the row has enough columns
            while (count($row) < count($headers)) {
                $row[] = '';
            }
            
            $response = [
                'id' => $index + 1,
                'timestamp' => $row[0] ?? '',
                'name' => $row[1] ?? '',
                'email' => $row[2] ?? '',
                'phone' => $row[3] ?? '',
                'institution' => $row[4] ?? '',
                'category' => $row[5] ?? '',
                'participationType' => $row[6] ?? '',
                'address' => $row[7] ?? '',
                'city' => $row[8] ?? '',
                'state' => $row[9] ?? '',
                'status' => $row[10] ?? 'pending' // Status column (add this to your sheet manually)
            ];
            
            $responses[] = $response;
        }
        
        echo json_encode([
            'success' => true,
            'count' => count($responses),
            'responses' => $responses
        ]);
        
    } else {
        // Fallback to database if Google Sheets not configured
        require_once 'db-config.php';
        
        $query = "SELECT * FROM registrations ORDER BY created_at DESC";
        $result = $conn->query($query);
        
        $responses = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $responses[] = [
                    'id' => $row['id'],
                    'timestamp' => $row['created_at'],
                    'name' => $row['full_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'institution' => $row['institution'],
                    'category' => $row['category'],
                    'participationType' => $row['participation_type'],
                    'address' => $row['address'] ?? '',
                    'city' => $row['city'] ?? '',
                    'state' => $row['state'] ?? '',
                    'status' => $row['status'] ?? 'pending'
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'count' => count($responses),
            'responses' => $responses
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
