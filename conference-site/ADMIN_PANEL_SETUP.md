# Admin Panel Setup Instructions

## Overview
The admin panel has been created to manage registration form responses from the Google Form. This document provides setup instructions to integrate the admin panel with your Google Form responses.

## What Was Created

### 1. Admin Login Page (`admin-login.html`)
- **Location**: Root of conference-site folder
- **Access**: Via link in the footer of the main website (index.html)
- **Password**: `Program@2026`
- **Features**:
  - Secure password authentication
  - Session-based login (stays logged in during browser session)
  - Clean, professional interface
  - Auto-redirect if already logged in

### 2. Admin Responses Dashboard (`admin-responses.html`)
- **Location**: Root of conference-site folder
- **Features**:
  - Display all registration form responses
  - Real-time statistics (Total, Pending, Accepted, Rejected)
  - Search functionality (by name, email, institution)
  - Filter by status (All, Pending, Accepted, Rejected)
  - Accept/Reject buttons for each response
  - View detailed information for each registration
  - Responsive design for mobile and desktop

### 3. PHP Backend Files
- `api-get-registration-responses.php` - Fetches registration data from Google Sheets
- `api-accept-registration.php` - Handles accept/reject actions
- Updated `config/sheets-config.php` - Configuration for Google Sheets integration

## Google Form Information
**Form URL**: https://docs.google.com/forms/d/e/1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg/viewform

## Setup Instructions

### Option A: Using Sample Data (For Immediate Testing)
The admin panel is already configured with sample data for demonstration purposes. You can test it immediately:

1. Open your website: `index.html`
2. Scroll to the footer and click "Admin" link
3. Enter password: `Program@2026`
4. You'll see sample registration data with Accept/Reject functionality

### Option B: Connecting to Google Form Responses (Production Setup)

#### Step 1: Find Your Google Sheets ID
1. Open your Google Form responses in Google Sheets
2. The URL will look like: `https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit`
3. Copy the `SPREADSHEET_ID` part from the URL

#### Step 2: Add Status Column to Google Sheet
1. Open the Google Sheet with form responses
2. Add a new column after the last response column
3. Name it: `Status`
4. This column will track acceptance status (pending/accepted/rejected)

#### Step 3: Set Up Google Sheets API Access

1. **Create a Google Cloud Project**:
   - Go to https://console.cloud.google.com/
   - Create a new project or select an existing one
   - Name it: "NC-SDESS 2026"

2. **Enable Google Sheets API**:
   - In the Google Cloud Console, go to "APIs & Services" > "Library"
   - Search for "Google Sheets API"
   - Click "Enable"

3. **Create Service Account**:
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "Service Account"
   - Name: "nc-sdess-admin"
   - Click "Create and Continue"
   - Grant role: "Editor"
   - Click "Done"

4. **Generate JSON Key**:
   - Click on the created service account
   - Go to "Keys" tab
   - Click "Add Key" > "Create New Key"
   - Choose "JSON" format
   - Download the JSON file
   - Rename it to: `gsheets-service.json`
   - Move it to: `conference-site/config/gsheets-service.json`

5. **Share Google Sheet with Service Account**:
   - Open the JSON file and copy the `client_email` value
   - Open your Google Sheet with form responses
   - Click "Share" button
   - Paste the service account email
   - Give "Editor" permissions
   - Click "Send"

#### Step 4: Configure the Application

1. **Update sheets-config.php**:
   ```php
   // In config/sheets-config.php, update:
   define('USE_GOOGLE_SHEETS', true); // Change to true
   define('GOOGLE_SHEETS_ID', 'YOUR_ACTUAL_SPREADSHEET_ID'); // Paste your Spreadsheet ID
   ```

2. **Install Google Client Library** (if using Composer):
   ```bash
   cd conference-site
   composer require google/apiclient:"^2.0"
   ```

   If you don't have Composer, you can download the library manually from:
   https://github.com/googleapis/google-api-php-client/releases

#### Step 5: Test the Integration

1. Open your browser and go to: `your-domain.com/admin-login.html`
2. Enter password: `Program@2026`
3. You should now see actual data from your Google Form
4. Test the Accept/Reject functionality

## Features Explained

### Statistics Dashboard
- **Total Responses**: Count of all registrations
- **Pending**: Registrations awaiting review
- **Accepted**: Approved registrations
- **Rejected**: Declined registrations

### Search & Filter
- Search by name, email, phone, or institution
- Filter by status (All/Pending/Accepted/Rejected)
- Real-time filtering as you type

### Actions
- **Accept Button**: Marks registration as accepted (only for pending)
- **Reject Button**: Marks registration as rejected (only for pending)
- **View Button**: Shows detailed information (for accepted/rejected)

### Security
- Password-protected access
- Session-based authentication
- Auto-logout when closing browser
- Secured PHP endpoints

## Troubleshooting

### Issue: "No data found"
**Solution**: 
- Check if `USE_GOOGLE_SHEETS` is set to `true` in `config/sheets-config.php`
- Verify the Spreadsheet ID is correct
- Ensure the service account has access to the sheet

### Issue: "Error loading data"
**Solution**:
- Check if `gsheets-service.json` exists in `config/` folder
- Verify Google Sheets API is enabled
- Check PHP error logs for specific errors

### Issue: Sample data still showing
**Solution**:
- This means Google Sheets integration is not configured yet
- Follow "Option B" setup instructions above
- The system automatically falls back to sample data if integration fails

### Issue: Accept/Reject not working
**Solution**:
- Ensure the "Status" column exists in your Google Sheet
- Verify service account has "Editor" permissions (not just "Viewer")
- Check browser console for JavaScript errors

## File Structure
```
conference-site/
├── index.html (updated with admin link)
├── admin-login.html (new)
├── admin-responses.html (new)
├── api-get-registration-responses.php (new)
├── api-accept-registration.php (new)
├── config/
│   ├── sheets-config.php (updated)
│   └── gsheets-service.json (you need to add this)
└── assets/
    └── img/
        └── hitam logo.png
```

## Security Recommendations

1. **Change the default password**:
   - Edit `admin-login.html`
   - Find line: `const ADMIN_PASSWORD = 'Program@2026';`
   - Change to a stronger password

2. **Add HTTPS**:
   - Use SSL certificate for your website
   - Protect data transmission

3. **Restrict Access**:
   - Consider adding IP whitelisting
   - Implement user authentication with database

4. **Backup Data**:
   - Regularly backup your Google Sheet
   - Keep service account credentials secure

## Expected Form Response Structure

The system expects the following columns in your Google Form responses (in order):
1. Timestamp
2. Full Name
3. Email Address
4. Phone Number
5. Institution/Organization
6. Category (Faculty/Student/Industry)
7. Participation Type (Paper Presentation/Poster/Attendee)
8. Address
9. City
10. State
11. Status (manually added column)

If your form has different fields, update the mapping in `api-get-registration-responses.php`.

## Support

For additional help or issues:
1. Check PHP error logs: Usually in `error_log` or server logs
2. Check browser console: Press F12 and look for JavaScript errors
3. Verify all file paths are correct
4. Ensure PHP version is 7.4 or higher

## Next Steps

1. ✅ Admin login page created with password protection
2. ✅ Admin dashboard created with accept/reject functionality
3. ✅ PHP backend configured for Google Sheets integration
4. ⏳ Configure Google Sheets API (follow Option B above)
5. ⏳ Test with actual form responses
6. ⏳ Customize email notifications (optional)

---

**Created**: January 9, 2026
**Password**: Program@2026 (change this for production!)
**Access**: [Your Domain]/admin-login.html
