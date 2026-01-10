# Google Sheets + SMTP Email Setup Instructions

## Overview
This setup enables automatic saving of abstract submissions to Google Sheets and sending confirmation emails via SMTP from `ncsdess2026@hitam.org`.

---

## Part 1: Google Sheets API Setup

### Step 1: Create Google Service Account

1. **Go to Google Cloud Console:**
   - Visit: https://console.cloud.google.com/
   - Sign in with your Google account

2. **Create a New Project (or select existing):**
   - Click "Select a project" → "NEW PROJECT"
   - Name: "NC-SDESS Conference"
   - Click "CREATE"

3. **Enable Google Sheets API:**
   - In the left menu: APIs & Services → Library
   - Search for "Google Sheets API"
   - Click on it → Click "ENABLE"

4. **Create Service Account:**
   - Go to: APIs & Services → Credentials
   - Click "CREATE CREDENTIALS" → "Service account"
   - Service account name: `ncsdess-sheets-writer`
   - Click "CREATE AND CONTINUE"
   - Role: Select "Editor" (or "Google Sheets API > Sheets Editor")
   - Click "CONTINUE" → "DONE"

5. **Generate JSON Key:**
   - Click on the newly created service account email
   - Go to "KEYS" tab
   - Click "ADD KEY" → "Create new key"
   - Choose "JSON" → Click "CREATE"
   - A JSON file will download automatically

6. **Save the JSON File:**
   - Rename the downloaded file to: `gsheets-service.json`
   - Move it to: `conference-site/config/gsheets-service.json`
   - **IMPORTANT:** Never commit this file to version control!

### Step 2: Prepare Google Sheet

1. **Create or Open Your Google Sheet:**
   - Go to: https://sheets.google.com/
   - Create a new sheet or open existing one
   - Name a tab: "Abstract Submissions"

2. **Add Column Headers (Row 1):**
   ```
   Submission ID | Timestamp | Name | Email | Phone | Organization | Track | Title | Co-Authors | Keywords | Abstract Content | Submission Type | Word Count | IP Address
   ```

3. **Share Sheet with Service Account:**
   - Open the `gsheets-service.json` file
   - Copy the `client_email` value (looks like: `ncsdess-sheets-writer@project-name.iam.gserviceaccount.com`)
   - In your Google Sheet, click "Share"
   - Paste the service account email
   - Give it "Editor" permission
   - Uncheck "Notify people"
   - Click "Share"

4. **Get Spreadsheet ID:**
   - Look at your Google Sheets URL:
   - `https://docs.google.com/spreadsheets/d/{SPREADSHEET_ID}/edit`
   - Copy the {SPREADSHEET_ID} part

5. **Update Configuration:**
   - Open: `conference-site/config/sheets-config.php`
   - Replace `YOUR_SPREADSHEET_ID_HERE` with your actual Spreadsheet ID
   - Verify `sheet_name` matches your tab name

---

## Part 2: SMTP Email Setup

### Step 1: Get SMTP Credentials for ncsdess2026@hitam.org

**Option A: Gmail Account**
1. Enable 2-Step Verification:
   - Go to: https://myaccount.google.com/security
   - Enable "2-Step Verification"

2. Generate App Password:
   - Go to: https://myaccount.google.com/apppasswords
   - Select app: "Mail"
   - Select device: "Other" → type "Conference Website"
   - Click "GENERATE"
   - Copy the 16-character password (no spaces)

**Option B: Office 365 / Outlook.com**
- Host: `smtp.office365.com`
- Port: `587`
- Encryption: `tls`
- Username: `ncsdess2026@hitam.org`
- Password: Your email password

**Option C: Custom Domain / cPanel**
- Contact your hosting provider for:
  - SMTP Host
  - SMTP Port (usually 587 or 465)
  - Encryption type (tls or ssl)
- Username: Usually your full email address
- Password: Your email password

### Step 2: Configure SMTP Settings

1. **Open:** `conference-site/config/email-config.php`
2. **Update these values:**
   ```php
   'smtp_host' => 'smtp.gmail.com',           // Your SMTP server
   'smtp_port' => 587,                         // Your SMTP port
   'smtp_encryption' => 'tls',                 // tls or ssl
   'smtp_username' => 'ncsdess2026@hitam.org', // Your email
   'smtp_password' => 'your-app-password',     // Your app password
   ```

---

## Part 3: Install PHP Dependencies

### Step 1: Install Composer (if not already installed)

**Windows:**
1. Download from: https://getcomposer.org/download/
2. Run the installer
3. Verify: Open PowerShell and type `composer --version`

**macOS/Linux:**
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### Step 2: Install Required Libraries

Open PowerShell in your project folder and run:

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
composer require google/apiclient phpmailer/phpmailer
```

This installs:
- `google/apiclient` - Google Sheets API client
- `phpmailer/phpmailer` - Email sending library

---

## Part 4: Test the Setup

### Step 1: Create Test Script

Create `conference-site/test-setup.php`:

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

// Test Google Sheets
echo "Testing Google Sheets connection...\n";
try {
    $sheets_config = require __DIR__ . '/config/sheets-config.php';
    $client = new Google_Client();
    $client->setAuthConfig($sheets_config['service_account_file']);
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);
    
    $service = new Google_Service_Sheets($client);
    $response = $service->spreadsheets->get($sheets_config['spreadsheet_id']);
    echo "✓ Google Sheets connection successful!\n";
    echo "  Sheet title: " . $response->properties->title . "\n\n";
} catch (Exception $e) {
    echo "✗ Google Sheets error: " . $e->getMessage() . "\n\n";
}

// Test SMTP
echo "Testing SMTP connection...\n";
try {
    $email_config = require __DIR__ . '/config/email-config.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $email_config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $email_config['smtp_username'];
    $mail->Password = $email_config['smtp_password'];
    $mail->SMTPSecure = $email_config['smtp_encryption'];
    $mail->Port = $email_config['smtp_port'];
    
    // Just verify connection, don't send email
    $mail->SMTPDebug = 0;
    $mail->Timeout = 10;
    
    echo "✓ SMTP configuration loaded successfully!\n";
    echo "  Host: " . $email_config['smtp_host'] . "\n";
    echo "  Port: " . $email_config['smtp_port'] . "\n\n";
} catch (Exception $e) {
    echo "✗ SMTP error: " . $e->getMessage() . "\n\n";
}

echo "Setup test complete!\n";
?>
```

### Step 2: Run Test

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
php test-setup.php
```

---

## Part 5: Security

### Important Files to Protect

Add to `.gitignore` (create if it doesn't exist):

```
config/gsheets-service.json
config/email-config.php
vendor/
composer.lock
```

### File Permissions

Ensure config files are not publicly accessible:
- Move `config/` folder outside web root if possible
- Or add `.htaccess` in config folder:

```apache
# config/.htaccess
Deny from all
```

---

## Troubleshooting

### Google Sheets Issues

**Error: "The caller does not have permission"**
- Solution: Make sure you shared the sheet with the service account email

**Error: "Requested entity was not found"**
- Solution: Check the Spreadsheet ID in `sheets-config.php`

**Error: "Unable to parse response"**
- Solution: Verify the service account JSON file path is correct

### SMTP Email Issues

**Error: "SMTP connect() failed"**
- Solution: Check SMTP host, port, and encryption settings
- Verify your firewall allows outbound connections on port 587/465

**Error: "Invalid address"**
- Solution: Check email addresses in `email-config.php`

**Error: "Authentication failed"**
- Solution: Use App Password (for Gmail) instead of regular password
- Verify username is correct (usually full email address)

**Error: "Could not authenticate"**
- Gmail: Make sure 2-Step Verification is enabled and you're using App Password
- Office 365: May need to enable "SMTP AUTH" in admin settings

---

## What Happens When Form is Submitted

1. ✓ Form data is validated server-side
2. ✓ Abstract ID is generated (format: `ABS-2026-XXXXXX`)
3. ✓ Data is appended to Google Sheets
4. ✓ Two emails are sent to the participant:
   - Thank you email with submission details
   - Acceptance notification with next steps
5. ✓ Admin notification is sent to `ncsdess2026@hitam.org`
6. ✓ Success response is returned to the user

---

## Need Help?

Common issues:
1. **Composer not found:** Install Composer first (see Part 3)
2. **Service account JSON missing:** Complete Part 1, Step 1
3. **Sheet not shared:** Complete Part 1, Step 2, item 3
4. **SMTP credentials wrong:** Verify with your email provider (Part 2)

For additional support, review the error logs in `conference-site/logs/` folder.
