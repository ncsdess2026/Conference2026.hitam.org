# Setup Checklist - Google Sheets + Email Integration

Print this checklist and check off each item as you complete it.

---

## Prerequisites

- [ ] I have access to `ncsdess2026@hitam.org` email account
- [ ] I have Google account access
- [ ] I can run PowerShell commands
- [ ] PHP is installed on my server (check with: `php -v`)

---

## Part 1: Install Dependencies

### 1.1 Install Composer (if needed)

- [ ] Download Composer from: https://getcomposer.org/download/
- [ ] Run installer
- [ ] Verify installation: `composer --version`

### 1.2 Install PHP Libraries

- [ ] Open PowerShell in project folder
- [ ] Run: `cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"`
- [ ] Run: `composer install`
- [ ] Verify `vendor/` folder created
- [ ] Check for `vendor/google/` folder
- [ ] Check for `vendor/phpmailer/` folder

**If errors occur:**
- [ ] Check PHP version: `php -v` (need >= 7.4)
- [ ] Check internet connection
- [ ] Try: `composer require google/apiclient phpmailer/phpmailer`

---

## Part 2: Google Cloud Setup

### 2.1 Create Google Cloud Project

- [ ] Go to: https://console.cloud.google.com/
- [ ] Sign in with Google account
- [ ] Click "Select a project" dropdown
- [ ] Click "NEW PROJECT"
- [ ] Project name: `NC-SDESS-Conference` (or your choice)
- [ ] Click "CREATE"
- [ ] Wait for project creation (check notification bell)
- [ ] Note your Project ID: ______________________

### 2.2 Enable Google Sheets API

- [ ] In Google Cloud Console, click hamburger menu (≡)
- [ ] Go to: "APIs & Services" > "Library"
- [ ] Search for: "Google Sheets API"
- [ ] Click on "Google Sheets API"
- [ ] Click "ENABLE" button
- [ ] Wait for confirmation

### 2.3 Create Service Account

- [ ] Go to: "APIs & Services" > "Credentials"
- [ ] Click "CREATE CREDENTIALS" button
- [ ] Select "Service account"
- [ ] Service account name: `ncsdess-sheets-writer`
- [ ] Service account ID: (auto-filled, leave as-is)
- [ ] Click "CREATE AND CONTINUE"
- [ ] Role: Select "Editor" or "Google Sheets API > Sheets Editor"
- [ ] Click "CONTINUE"
- [ ] Click "DONE"

### 2.4 Generate Service Account Key

- [ ] Click on the service account you just created
- [ ] Click "KEYS" tab
- [ ] Click "ADD KEY" > "Create new key"
- [ ] Select "JSON" format
- [ ] Click "CREATE"
- [ ] JSON file downloads automatically
- [ ] Note the service account email (looks like: `ncsdess-sheets-writer@project-id.iam.gserviceaccount.com`)
- [ ] Service account email: ______________________________________

### 2.5 Save Service Account JSON

- [ ] Locate downloaded JSON file (usually in Downloads folder)
- [ ] Rename file to: `gsheets-service.json`
- [ ] Move to: `conference-site/config/gsheets-service.json`
- [ ] Verify file exists at correct location
- [ ] **IMPORTANT:** Never share this file or commit to Git!

---

## Part 3: Google Sheets Setup

### 3.1 Create/Open Google Sheet

- [ ] Go to: https://sheets.google.com/
- [ ] Create new sheet OR open existing sheet
- [ ] Sheet name: (your choice, e.g., "NC-SDESS Submissions")
- [ ] Google Sheet URL: _________________________________________

### 3.2 Create Tab for Submissions

- [ ] Create/rename a tab to: `Abstract Submissions`
- [ ] Tab name must exactly match: "Abstract Submissions"

### 3.3 Add Column Headers

- [ ] In Row 1, add these 14 headers (copy/paste recommended):

```
Submission ID	Timestamp	Name	Email	Phone	Organization	Track	Title	Co-Authors	Keywords	Abstract Content	Submission Type	Word Count	IP Address
```

**Column Layout (Row 1):**
- [ ] A1: Submission ID
- [ ] B1: Timestamp
- [ ] C1: Name
- [ ] D1: Email
- [ ] E1: Phone
- [ ] F1: Organization
- [ ] G1: Track
- [ ] H1: Title
- [ ] I1: Co-Authors
- [ ] J1: Keywords
- [ ] K1: Abstract Content
- [ ] L1: Submission Type
- [ ] M1: Word Count
- [ ] N1: IP Address

### 3.4 Share Sheet with Service Account

- [ ] Click "Share" button (top right)
- [ ] Paste service account email: `ncsdess-sheets-writer@project-id...`
- [ ] Permission: "Editor"
- [ ] Uncheck "Notify people"
- [ ] Click "Share" or "Send"
- [ ] Confirm sharing successful

### 3.5 Get Spreadsheet ID

- [ ] Look at Google Sheets URL
- [ ] Format: `https://docs.google.com/spreadsheets/d/{SPREADSHEET_ID}/edit`
- [ ] Copy the {SPREADSHEET_ID} part (between `/d/` and `/edit`)
- [ ] Spreadsheet ID: _________________________________________

### 3.6 Update Configuration

- [ ] Open file: `conference-site/config/sheets-config.php`
- [ ] Find line: `'spreadsheet_id' => 'YOUR_SPREADSHEET_ID_HERE'`
- [ ] Replace `YOUR_SPREADSHEET_ID_HERE` with your actual ID
- [ ] Verify `'sheet_name' => 'Abstract Submissions'` matches your tab name
- [ ] Save file

---

## Part 4: Email SMTP Setup

### 4.1 Gmail Setup (if using Gmail)

**Step 1: Enable 2-Step Verification**
- [ ] Go to: https://myaccount.google.com/security
- [ ] Sign in as: `ncsdess2026@hitam.org`
- [ ] Find "2-Step Verification"
- [ ] Click "Get Started" or "Turn On"
- [ ] Follow prompts to enable
- [ ] Confirm 2-Step Verification is enabled

**Step 2: Generate App Password**
- [ ] Go to: https://myaccount.google.com/apppasswords
- [ ] Select app: "Mail"
- [ ] Select device: "Other" > type "Conference Website"
- [ ] Click "GENERATE"
- [ ] Copy the 16-character password (no spaces)
- [ ] App Password: ____ ____ ____ ____ (keep secret!)

**Step 3: Update Email Config**
- [ ] Open file: `conference-site/config/email-config.php`
- [ ] Set: `'smtp_host' => 'smtp.gmail.com'`
- [ ] Set: `'smtp_port' => 587`
- [ ] Set: `'smtp_encryption' => 'tls'`
- [ ] Set: `'smtp_username' => 'ncsdess2026@hitam.org'`
- [ ] Set: `'smtp_password' => 'your-16-char-app-password'`
- [ ] Save file

### 4.2 Office 365 Setup (if using Office 365)

- [ ] Contact IT for SMTP settings
- [ ] SMTP Host: `smtp.office365.com`
- [ ] SMTP Port: `587`
- [ ] Encryption: `tls`
- [ ] Username: `ncsdess2026@hitam.org`
- [ ] Password: (your email password)
- [ ] Update `config/email-config.php` with these settings

### 4.3 Other Email Provider

- [ ] Contact email provider for SMTP settings
- [ ] Get: Host, Port, Encryption, Username, Password
- [ ] Update `config/email-config.php`

---

## Part 5: Activate New Handler

### 5.1 Backup Old Handler

- [ ] Open PowerShell in project folder
- [ ] Run: `cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"`
- [ ] Run: `Move-Item process-abstract.php process-abstract-old.php -Force`
- [ ] Verify backup created: `process-abstract-old.php`

### 5.2 Activate New Handler

- [ ] Run: `Move-Item process-abstract-new.php process-abstract.php -Force`
- [ ] Verify new handler is now: `process-abstract.php`
- [ ] File should have Google Sheets + PHPMailer code

---

## Part 6: Testing

### 6.1 Test Connections (Optional)

Create test file: `test-connection.php`
```php
<?php
require 'vendor/autoload.php';

// Test Sheets
$config = require 'config/sheets-config.php';
$client = new Google_Client();
$client->setAuthConfig($config['service_account_file']);
echo "✓ Service account loaded\n";

// Test PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
$mail = new PHPMailer();
echo "✓ PHPMailer loaded\n";
?>
```

- [ ] Run: `php test-connection.php`
- [ ] Check for "✓" success messages
- [ ] If errors, review configuration

### 6.2 Test Full Submission

- [ ] Open website in browser
- [ ] Navigate to Abstract Submission form
- [ ] Fill out form with test data:
  - [ ] Name: Test User
  - [ ] Email: (your test email)
  - [ ] Phone: Valid phone
  - [ ] Organization: Test Org
  - [ ] Track: Select any
  - [ ] Submission Type: Paper or Poster
  - [ ] Title: Test Submission
  - [ ] Keywords: test, keywords
  - [ ] Abstract: (250-300 words of test content)
  - [ ] Check agreement box
- [ ] Click "Submit Abstract"
- [ ] Wait for response

### 6.3 Verify Success

**Frontend:**
- [ ] Success message displayed
- [ ] Abstract ID shown (format: ABS-YYYYMMDD-XXXXXX)
- [ ] No error messages

**Google Sheets:**
- [ ] Open your Google Sheet
- [ ] Check "Abstract Submissions" tab
- [ ] Verify new row added (Row 2)
- [ ] Check all columns populated correctly
- [ ] Verify Abstract ID matches frontend

**Participant Emails:**
- [ ] Check test email inbox
- [ ] Received "Thank You" email
- [ ] Received "Acceptance" email
- [ ] Both emails display correctly
- [ ] Abstract ID visible in both

**Admin Email:**
- [ ] Check `ncsdess2026@hitam.org` inbox
- [ ] Received admin notification
- [ ] Full submission details shown
- [ ] Abstract ID in subject

### 6.4 Check Logs

- [ ] Check folder: `conference-site/logs/`
- [ ] Open `errors.log` (if exists)
- [ ] Review any error messages
- [ ] If no critical errors, you're good!

---

## Part 7: Troubleshooting (If Needed)

### If "Class 'Google_Client' not found"

- [ ] Run: `composer install` again
- [ ] Check `vendor/google/` folder exists
- [ ] Try: `composer require google/apiclient`

### If "Service account JSON not found"

- [ ] Check file exists: `config/gsheets-service.json`
- [ ] Verify file path in `config/sheets-config.php`
- [ ] Check file permissions (readable)

### If "The caller does not have permission"

- [ ] Re-share Google Sheet with service account email
- [ ] Verify service account has "Editor" role
- [ ] Check Spreadsheet ID is correct

### If "SMTP connect() failed"

- [ ] Verify SMTP host and port in `config/email-config.php`
- [ ] Check internet connection
- [ ] Try telnet: `telnet smtp.gmail.com 587`
- [ ] Check firewall allows outbound connections

### If "Authentication failed"

**Gmail:**
- [ ] Verify 2-Step Verification enabled
- [ ] Regenerate App Password
- [ ] Use App Password, not regular password

**Office 365:**
- [ ] Check SMTP AUTH enabled
- [ ] Verify username is full email address
- [ ] Try regular password first

### If emails not received

- [ ] Check spam/junk folder
- [ ] Verify email address correct
- [ ] Check `logs/errors.log` for email errors
- [ ] Test SMTP settings separately

---

## Part 8: Security Verification

- [ ] `.gitignore` file exists
- [ ] `config/gsheets-service.json` in `.gitignore`
- [ ] `config/email-config.php` in `.gitignore`
- [ ] `config/.htaccess` exists (blocks direct access)
- [ ] Never committed sensitive files to Git
- [ ] File permissions appropriate (644 for PHP files)

---

## Part 9: Production Readiness

- [ ] All tests passed
- [ ] Error handling working
- [ ] Emails sending correctly
- [ ] Google Sheets updating
- [ ] Error logging configured
- [ ] Security measures in place
- [ ] Documentation reviewed
- [ ] Backup of old handler saved

---

## Part 10: Final Verification

- [ ] Submit 3 test abstracts
- [ ] All 3 appear in Google Sheets
- [ ] All 3 send emails correctly
- [ ] Abstract IDs are unique
- [ ] Timestamps are accurate
- [ ] All data fields populated
- [ ] No errors in logs

---

## Completed! 🎉

**Setup completed on:** _______________
**Tested by:** _______________
**Status:** ☐ Ready for production

**Notes:**
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

---

## Support Contacts

- **Technical Issues:** Review `SETUP_INSTRUCTIONS.md`
- **Google Sheets API:** https://developers.google.com/sheets/api
- **PHPMailer Docs:** https://github.com/PHPMailer/PHPMailer
- **Conference Email:** ncsdess2026@hitam.org

---

**Keep this checklist for future reference and troubleshooting!**
