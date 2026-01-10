<?php
/**
 * Google Sheets Configuration for NC-SDESS 2026
 * IMPORTANT: Replace these values with your actual Google Sheets details
 */

// Enable/Disable Google Sheets Integration
define('USE_GOOGLE_SHEETS', false); // Set to true when Google Sheets is configured

// Google Sheets ID from the Registration Form
// Extract from URL: https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit
// For the form: https://docs.google.com/forms/d/e/1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg/viewform
// The responses are stored in a linked Google Sheet
define('GOOGLE_SHEETS_ID', 'YOUR_SPREADSHEET_ID_HERE');

// Registration Form Configuration
return [
    // Google Sheets Details
    'spreadsheet_id' => 'YOUR_SPREADSHEET_ID_HERE',  // Get this from your Google Sheets URL
    'sheet_name' => 'Form Responses 1',              // Default sheet name for Google Forms
    
    // Service Account JSON Path
    'service_account_file' => __DIR__ . '/gsheets-service.json',  // Path to your service account JSON file
    
    // Registration Form Column Headers (must match the order in your Google Sheet)
    'registration_columns' => [
        'Timestamp',
        'Full Name',
        'Email Address',
        'Phone Number',
        'Institution/Organization',
        'Category',
        'Participation Type',
        'Address',
        'City',
        'State',
        'Status' // Add this column manually to your sheet for tracking status
    ],
    
    // Abstract Submission Column Headers
    'abstract_columns' => [
        'Submission ID',
        'Timestamp',
        'Name',
        'Email',
        'Phone',
        'Organization',
        'Track',
        'Title',
        'Co-Authors',
        'Keywords',
        'Abstract Content',
        'Submission Type',
        'Word Count',
        'IP Address'
    ]
];
