<?php
// Database Initialization Script
// Run this once to set up all required tables

require 'db-config.php';

$tables = array(
    // Students/Participants Table
    "CREATE TABLE IF NOT EXISTS participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        participant_id VARCHAR(50) UNIQUE NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone VARCHAR(20) NOT NULL,
        organization VARCHAR(200) NOT NULL,
        category VARCHAR(50) NOT NULL,
        participation_type VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_participant_id (participant_id)
    )",
    
    // Payment Information Table
    "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        participant_id VARCHAR(50) NOT NULL,
        transaction_id VARCHAR(100) UNIQUE NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        payment_status VARCHAR(50) DEFAULT 'pending',
        payment_method VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (participant_id) REFERENCES participants(participant_id) ON DELETE CASCADE,
        INDEX idx_transaction_id (transaction_id),
        INDEX idx_participant_id (participant_id),
        INDEX idx_payment_status (payment_status)
    )",
    
    // Submissions Table (Abstract, Paper, Poster)
    "CREATE TABLE IF NOT EXISTS submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        participant_id VARCHAR(50) NOT NULL,
        submission_type VARCHAR(50) NOT NULL,
        submission_id VARCHAR(100) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        abstract TEXT,
        submission_file_path VARCHAR(255),
        submission_details JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (participant_id) REFERENCES participants(participant_id) ON DELETE CASCADE,
        INDEX idx_submission_id (submission_id),
        INDEX idx_participant_id (participant_id),
        INDEX idx_submission_type (submission_type)
    )",
    
    // Registration Confirmations Table
    "CREATE TABLE IF NOT EXISTS confirmations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        registration_id VARCHAR(50) UNIQUE NOT NULL,
        participant_id VARCHAR(50) NOT NULL,
        payment_verified BOOLEAN DEFAULT FALSE,
        verified_by VARCHAR(100),
        verified_date DATETIME,
        confirmation_sent BOOLEAN DEFAULT FALSE,
        confirmation_sent_date DATETIME,
        attendance_mode VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (participant_id) REFERENCES participants(participant_id) ON DELETE CASCADE,
        INDEX idx_registration_id (registration_id),
        INDEX idx_participant_id (participant_id),
        INDEX idx_payment_verified (payment_verified),
        INDEX idx_confirmation_sent (confirmation_sent)
    )",
    
    // Email Log Table
    "CREATE TABLE IF NOT EXISTS email_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient_email VARCHAR(150) NOT NULL,
        email_type VARCHAR(100) NOT NULL,
        related_id VARCHAR(100),
        subject VARCHAR(255),
        body LONGTEXT,
        status VARCHAR(50) DEFAULT 'sent',
        error_message TEXT,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_recipient (recipient_email),
        INDEX idx_email_type (email_type),
        INDEX idx_related_id (related_id)
    )"
);

$created = 0;
$errors = 0;

foreach ($tables as $table) {
    if ($conn->query($table) === TRUE) {
        $created++;
    } else {
        echo "Error creating table: " . $conn->error . "\n";
        $errors++;
    }
}

echo "Database initialization complete.\n";
echo "Tables created/verified: " . $created . "\n";
if ($errors > 0) {
    echo "Errors: " . $errors . "\n";
}

$conn->close();
?>
