# Quick Start Guide: Google Sheets + Email Integration

## 🚀 Fast Setup (5 Steps)

### Step 1: Install Dependencies (2 minutes)

Open PowerShell in your project folder:

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
composer require google/apiclient phpmailer/phpmailer
```

If you don't have Composer, download it from: https://getcomposer.org/download/

---

### Step 2: Google Sheets Setup (5 minutes)

1. **Create Service Account:**
   - Go to: https://console.cloud.google.com/
   - Create project → Enable "Google Sheets API"
   - Create Service Account → Download JSON key
   - Save as: `config/gsheets-service.json`

2. **Prepare Google Sheet:**
   - Create new Google Sheet
   - Add tab named: "Abstract Submissions"
   - Add these headers in Row 1:
     ```
     Submission ID | Timestamp | Name | Email | Phone | Organization | Track | Title | Co-Authors | Keywords | Abstract Content | Submission Type | Word Count | IP Address
     ```
   - Share sheet with service account email (from JSON file)

3. **Update Config:**
   - Open: `config/sheets-config.php`
   - Add your Spreadsheet ID (from URL)

---

### Step 3: Email Setup (3 minutes)

**For Gmail:**
1. Enable 2-Step Verification
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Open: `config/email-config.php`
4. Update:
   ```php
   'smtp_host' => 'smtp.gmail.com',
   'smtp_port' => 587,
   'smtp_username' => 'ncsdess2026@hitam.org',
   'smtp_password' => 'your-16-char-app-password',
   ```

**For Other Email Providers:**
- Get SMTP details from your email provider
- Update `email-config.php` accordingly

---

### Step 4: Activate the New Handler (1 minute)

Replace the old handler with the new one:

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
Move-Item process-abstract.php process-abstract-old.php
Move-Item process-abstract-new.php process-abstract.php
```

---

### Step 5: Test It! (2 minutes)

1. Open your website
2. Go to Abstract Submission form
3. Fill out and submit
4. Check:
   - ✓ Google Sheet has new row
   - ✓ Two emails received
   - ✓ Admin notification received

---

## 🔧 Troubleshooting

### "Class 'Google_Client' not found"
→ Run: `composer install` in your project folder

### "Service account JSON not found"
→ Check file exists: `config/gsheets-service.json`

### "The caller does not have permission"
→ Share Google Sheet with service account email

### "SMTP connect() failed"
→ Verify SMTP credentials in `config/email-config.php`

### "Authentication failed" (Gmail)
→ Use App Password, not regular password

---

## 📋 What Happens When Form Submits

1. ✅ Validates all fields
2. ✅ Generates Abstract ID: `ABS-20260108-ABC123`
3. ✅ Saves to Google Sheets
4. ✅ Sends "Thank You" email to participant
5. ✅ Sends "Acceptance" email to participant
6. ✅ Sends notification to admin
7. ✅ Returns success with Abstract ID

---

## 📞 Need Help?

Read the detailed guide: [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)

Common issues solved there:
- Composer installation
- Service account creation
- SMTP configuration for different providers
- Security best practices
