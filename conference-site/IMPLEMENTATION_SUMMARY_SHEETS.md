# Google Sheets + SMTP Email Integration - Implementation Summary

## ✅ What Has Been Created

### Configuration Files

1. **`config/email-config.php`** - SMTP email settings
   - You need to add your SMTP credentials here
   - Default configured for Gmail (smtp.gmail.com:587)

2. **`config/sheets-config.php`** - Google Sheets settings
   - You need to add your Spreadsheet ID
   - Defines column headers

3. **`config/gsheets-service.json`** - Service account credentials
   - ⚠️ YOU NEED TO CREATE THIS FILE
   - Download from Google Cloud Console
   - Never commit to version control

4. **`config/.htaccess`** - Security protection
   - Blocks direct access to config files

### Core Files

5. **`process-abstract-new.php`** - New submission handler
   - Integrates Google Sheets API
   - Sends emails via PHPMailer/SMTP
   - Generates unique Abstract IDs
   - Error logging

6. **`composer.json`** - PHP dependency manager
   - Defines required libraries
   - Run `composer install` to download dependencies

7. **`.gitignore`** - Version control exclusions
   - Protects sensitive files from being committed

### Documentation

8. **`SETUP_INSTRUCTIONS.md`** - Complete setup guide (detailed)
   - Step-by-step Google Cloud setup
   - Service account creation
   - SMTP configuration for various providers
   - Troubleshooting guide

9. **`QUICK_START_SHEETS.md`** - Fast setup guide (5 steps)
   - Condensed version for quick implementation

---

## 🔧 What You Need to Do

### Step 1: Install Composer Dependencies

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
composer install
```

This downloads:
- Google API PHP Client
- PHPMailer library

### Step 2: Create Google Service Account

1. Go to: https://console.cloud.google.com/
2. Create new project or select existing
3. Enable "Google Sheets API"
4. Create Service Account
5. Download JSON key
6. Save as: `conference-site/config/gsheets-service.json`

**Full instructions:** See SETUP_INSTRUCTIONS.md Section "Part 1"

### Step 3: Configure Google Sheet

1. Create Google Sheet or open existing
2. Create tab: "Abstract Submissions"
3. Add column headers (Row 1):
   ```
   Submission ID | Timestamp | Name | Email | Phone | Organization | Track | Title | Co-Authors | Keywords | Abstract Content | Submission Type | Word Count | IP Address
   ```
4. Share sheet with service account email (from JSON file)
5. Copy Spreadsheet ID from URL
6. Update `config/sheets-config.php` with your Spreadsheet ID

**Full instructions:** See SETUP_INSTRUCTIONS.md Section "Part 1, Step 2"

### Step 4: Configure SMTP Email

**For Gmail (ncsdess2026@hitam.org):**

1. Enable 2-Step Verification: https://myaccount.google.com/security
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Edit `config/email-config.php`:
   ```php
   'smtp_host' => 'smtp.gmail.com',
   'smtp_port' => 587,
   'smtp_encryption' => 'tls',
   'smtp_username' => 'ncsdess2026@hitam.org',
   'smtp_password' => 'your-16-character-app-password',
   ```

**For Other Email Providers:**
- Contact your email provider for SMTP settings
- Update `config/email-config.php` accordingly

**Full instructions:** See SETUP_INSTRUCTIONS.md Section "Part 2"

### Step 5: Activate New Handler

Replace old handler with new one:

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
Move-Item process-abstract.php process-abstract-old.php -Force
Move-Item process-abstract-new.php process-abstract.php -Force
```

Or manually:
1. Rename `process-abstract.php` → `process-abstract-old.php`
2. Rename `process-abstract-new.php` → `process-abstract.php`

---

## 🎯 How It Works

### User Submits Abstract Form

1. **Validation** - Server validates all fields
2. **ID Generation** - Creates unique ID: `ABS-20260108-ABC123`
3. **Google Sheets** - Appends row to spreadsheet
4. **Email 1** - Sends "Thank You" email with submission details
5. **Email 2** - Sends "Acceptance" email with next steps
6. **Admin Email** - Notifies admin of new submission
7. **Response** - Returns success + Abstract ID to frontend

### If Google Sheets Fails

- Process stops immediately
- No emails are sent
- Error logged to `logs/errors.log`
- User sees error message

### Email Content

**Email 1: Thank You**
- Confirms submission received
- Shows all submission details
- Displays Abstract ID prominently
- Explains next steps

**Email 2: Acceptance**
- Congratulates participant
- Shows Abstract ID + title
- Lists next steps (register, pay fee, prepare paper)
- Important dates and venue info

**Email 3: Admin Notification**
- Full submission details
- Abstract content
- Timestamp and IP address

---

## 🔐 Security Features

1. **Config Protection**
   - `.htaccess` blocks direct access
   - `.gitignore` prevents commits

2. **Input Validation**
   - Sanitizes all user input
   - Validates email format
   - Checks required fields
   - Word count limits

3. **Error Handling**
   - Custom error handler
   - Logs to file (not shown to users)
   - Safe error messages

4. **SMTP Authentication**
   - Encrypted connection (TLS/SSL)
   - Username/password auth
   - From address verification

---

## 🧪 Testing

### Test Command (Optional)

You can create a test script to verify connections:

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
php -r "
require 'vendor/autoload.php';
echo 'Testing Google Sheets...' . PHP_EOL;
try {
    \$client = new Google_Client();
    \$client->setAuthConfig('config/gsheets-service.json');
    echo '✓ Service account loaded' . PHP_EOL;
} catch (Exception \$e) {
    echo '✗ Error: ' . \$e->getMessage() . PHP_EOL;
}
echo 'Testing PHPMailer...' . PHP_EOL;
use PHPMailer\PHPMailer\PHPMailer;
\$mail = new PHPMailer();
echo '✓ PHPMailer loaded' . PHP_EOL;
"
```

### Manual Test

1. Open your website
2. Navigate to Abstract Submission form
3. Fill out with test data
4. Submit
5. Check:
   - ✓ Google Sheet has new row
   - ✓ Received 2 emails
   - ✓ Admin received notification

---

## 📊 Monitoring

### Error Logs

Location: `conference-site/logs/errors.log`

Check for:
- Google Sheets API errors
- SMTP connection failures
- Email sending failures

### Google Sheets

Check your sheet regularly:
- Verify new rows appear
- Check data accuracy
- Monitor submission counts

---

## 🆘 Troubleshooting

### Common Issues

| Error | Solution |
|-------|----------|
| "Class 'Google_Client' not found" | Run `composer install` |
| "Service account JSON not found" | Create `config/gsheets-service.json` |
| "The caller does not have permission" | Share Google Sheet with service account email |
| "SMTP connect() failed" | Check SMTP host, port, credentials |
| "Authentication failed" (Gmail) | Use App Password, not regular password |
| "Could not open input file" | Check file paths are correct |

### Debug Mode

Enable detailed error output (development only):

Edit `process-abstract.php`:
```php
ini_set('display_errors', 1);  // Change 0 to 1
```

**⚠️ IMPORTANT:** Set back to 0 in production!

---

## 📞 Next Steps

1. **Install Dependencies:** Run `composer install`
2. **Setup Google:** Create service account + configure sheet
3. **Setup Email:** Add SMTP credentials
4. **Activate Handler:** Rename files
5. **Test:** Submit test abstract
6. **Monitor:** Check logs and sheet

---

## 📚 Additional Resources

- Google Sheets API: https://developers.google.com/sheets/api
- PHPMailer GitHub: https://github.com/PHPMailer/PHPMailer
- Composer Documentation: https://getcomposer.org/doc/

---

## ✅ Checklist

- [ ] Composer installed
- [ ] Dependencies installed (`composer install`)
- [ ] Service account JSON created and saved
- [ ] Google Sheet created with headers
- [ ] Sheet shared with service account
- [ ] Spreadsheet ID added to config
- [ ] SMTP credentials added to config
- [ ] New handler activated
- [ ] Test submission successful
- [ ] Emails received
- [ ] Data appears in sheet

---

**Created:** January 8, 2026  
**For:** NC-SDESS 2026 Conference  
**Contact:** ncsdess2026@hitam.org
