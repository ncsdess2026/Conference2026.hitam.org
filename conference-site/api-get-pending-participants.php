<?php
/**
 * API: Get Pending Participants
 * Returns list of participants needing verification or confirmation
 */

require 'db-config.php';
header('Content-Type: application/json');

try {
    $category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
    $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
    $email = isset($_GET['email']) ? sanitize($_GET['email']) : '';
    
    // Build query
    $query = "SELECT 
        p.participant_id,
        p.first_name,
        p.last_name,
        p.email,
        p.category,
        py.transaction_id,
        py.amount,
        py.payment_status,
        c.payment_verified,
        c.confirmation_sent,
        c.registration_id
    FROM participants p
    LEFT JOIN payments py ON p.participant_id = py.participant_id
    LEFT JOIN confirmations c ON p.participant_id = c.participant_id
    WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if ($category) {
        $query .= " AND p.category = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    if ($email) {
        $query .= " AND p.email LIKE ?";
        $params[] = '%' . $email . '%';
        $types .= 's';
    }
    
    // Filter by status
    if ($status === 'pending') {
        $query .= " AND c.payment_verified = 0";
    } elseif ($status === 'verified') {
        $query .= " AND c.payment_verified = 1 AND c.confirmation_sent = 0";
    } elseif ($status === 'confirmed') {
        $query .= " AND c.confirmation_sent = 1";
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $participants = [];
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'participants' => $participants,
        'total' => count($participants)
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$conn->close();
?>
