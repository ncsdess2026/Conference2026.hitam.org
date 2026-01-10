# Abstract Submission System - Google Sheets + Email Integration

**Conference:** NC-SDESS 2026  
**Institution:** HITAM, Hyderabad  
**Implementation Date:** January 8, 2026

---

## 🎯 What This Does

When a user submits an abstract through your website form:

1. ✅ **Validates** all input data
2. ✅ **Generates** unique Abstract ID (e.g., `ABS-20260108-A3F91C`)
3. ✅ **Saves** submission to Google Sheets automatically
4. ✅ **Sends** two emails to participant:
   - "Thank You" email with submission details
   - "Acceptance" email with next steps
5. ✅ **Notifies** admin at `ncsdess2026@hitam.org`
6. ✅ **Returns** success message with Abstract ID

---

## 📁 What Was Created

### Configuration Files
- `config/email-config.php` - SMTP email settings
- `config/sheets-config.php` - Google Sheets configuration
- `config/gsheets-service.json.example` - Template for service account
- `config/.htaccess` - Security protection

### Core Files
- `process-abstract-new.php` - New submission handler (to be activated)
- `composer.json` - PHP dependency manager
- `.gitignore` - Protects sensitive files

### Documentation
- **[SETUP_CHECKLIST.md](SETUP_CHECKLIST.md)** ⭐ **START HERE!**
- **[QUICK_START_SHEETS.md](QUICK_START_SHEETS.md)** - Fast 5-step guide
- **[SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)** - Detailed setup guide
- **[IMPLEMENTATION_SUMMARY_SHEETS.md](IMPLEMENTATION_SUMMARY_SHEETS.md)** - Complete overview
- **[FLOW_DIAGRAM.md](FLOW_DIAGRAM.md)** - Visual process flow
- `README_SHEETS_INTEGRATION.md` - This file

---

## 🚀 Quick Start (5 Steps)

### Step 1: Install Dependencies (2 min)
```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
composer install
```

### Step 2: Setup Google (5 min)
1. Create Google Cloud project
2. Enable Google Sheets API
3. Create service account → download JSON
4. Save as `config/gsheets-service.json`
5. Share Google Sheet with service account email

### Step 3: Setup Email (3 min)
1. Enable 2-Step Verification for `ncsdess2026@hitam.org`
2. Generate App Password
3. Update `config/email-config.php` with credentials

### Step 4: Activate Handler (1 min)
```powershell
Move-Item process-abstract.php process-abstract-old.php
Move-Item process-abstract-new.php process-abstract.php
```

### Step 5: Test (2 min)
1. Submit test abstract
2. Check Google Sheets for new row
3. Check email inbox for confirmations

---

## 📚 Documentation Guide

**Which guide should I read?**

| Your Situation | Read This |
|----------------|-----------|
| Just want to get it working fast | [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md) |
| Need quick overview of 5 steps | [QUICK_START_SHEETS.md](QUICK_START_SHEETS.md) |
| Want detailed explanations | [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md) |
| Need to understand the system | [IMPLEMENTATION_SUMMARY_SHEETS.md](IMPLEMENTATION_SUMMARY_SHEETS.md) |
| Visual learner | [FLOW_DIAGRAM.md](FLOW_DIAGRAM.md) |
| Already set up, need reference | This file |

---

## ⚙️ System Requirements

- ✅ PHP 7.4 or higher
- ✅ Composer (dependency manager)
- ✅ Access to `ncsdess2026@hitam.org` email
- ✅ Google account with Sheets access
- ✅ Internet connection for API calls

---

## 🔑 What You Need to Provide

### Google Sheets
1. **Service Account JSON** - Download from Google Cloud Console
2. **Spreadsheet ID** - From your Google Sheets URL
3. **Sheet Name** - Tab name (default: "Abstract Submissions")

### Email (SMTP)
1. **Email Address** - `ncsdess2026@hitam.org`
2. **SMTP Host** - e.g., `smtp.gmail.com`
3. **SMTP Port** - Usually `587` (TLS) or `465` (SSL)
4. **Password** - App Password for Gmail, regular password for others

---

## 📊 Google Sheet Format

Your sheet needs these 14 columns (Row 1):

| Column | Header |
|--------|--------|
| A | Submission ID |
| B | Timestamp |
| C | Name |
| D | Email |
| E | Phone |
| F | Organization |
| G | Track |
| H | Title |
| I | Co-Authors |
| J | Keywords |
| K | Abstract Content |
| L | Submission Type |
| M | Word Count |
| N | IP Address |

---

## 📧 Email Templates

### Email 1: Thank You
- **To:** Participant
- **Subject:** "Thank You for Your Abstract Submission - NC-SDESS 2026"
- **Content:**
  - Confirmation of receipt
  - Full submission details
  - Abstract ID (highlighted)
  - Next steps

### Email 2: Acceptance
- **To:** Participant
- **Subject:** "Your Abstract Submission Accepted - NC-SDESS 2026"
- **Content:**
  - Congratulations message
  - Abstract ID and title
  - Next steps (register, pay, prepare paper)
  - Important dates
  - Venue information

### Email 3: Admin Notification
- **To:** `ncsdess2026@hitam.org`
- **Subject:** "New Abstract Submission - [Abstract ID]"
- **Content:**
  - Full submission details
  - Participant information
  - Abstract content
  - Timestamp and IP

---

## 🔒 Security Features

✅ **Input Validation** - All fields sanitized and validated  
✅ **Email Verification** - Format validation  
✅ **Config Protection** - `.htaccess` blocks direct access  
✅ **Git Protection** - `.gitignore` prevents commits  
✅ **Error Logging** - Errors logged, not displayed  
✅ **SMTP Encryption** - TLS/SSL for email  
✅ **Limited Permissions** - Service account has minimal scope  

---

## 🐛 Troubleshooting

### Common Issues

| Error | Solution |
|-------|----------|
| "Class 'Google_Client' not found" | Run `composer install` |
| "Service account JSON not found" | Create `config/gsheets-service.json` |
| "The caller does not have permission" | Share sheet with service account email |
| "SMTP connect() failed" | Check SMTP host/port/credentials |
| "Authentication failed" | Use App Password for Gmail |

### Check Logs

Errors logged to: `logs/errors.log`

Enable detailed errors (development only):
```php
// In process-abstract.php
ini_set('display_errors', 1); // Change from 0 to 1
```

**⚠️ Set back to 0 in production!**

---

## 📦 Dependencies

Installed via Composer:

- **google/apiclient** (^2.15) - Google Sheets API client
- **phpmailer/phpmailer** (^6.9) - SMTP email library

---

## 🔄 Update Process

To update the system in future:

```powershell
cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
composer update
```

---

## 🧪 Testing Checklist

Before going live:

- [ ] Dependencies installed (`vendor/` folder exists)
- [ ] Service account JSON in place
- [ ] Google Sheet created and shared
- [ ] Spreadsheet ID configured
- [ ] SMTP credentials configured
- [ ] New handler activated
- [ ] Test submission successful
- [ ] Google Sheet updated
- [ ] Emails received (participant + admin)
- [ ] Abstract ID generated correctly
- [ ] No errors in logs

---

## 📞 Support Resources

### Documentation
- **Setup Checklist:** [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md)
- **Quick Start:** [QUICK_START_SHEETS.md](QUICK_START_SHEETS.md)
- **Full Guide:** [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)
- **Flow Diagram:** [FLOW_DIAGRAM.md](FLOW_DIAGRAM.md)

### External Resources
- **Google Sheets API:** https://developers.google.com/sheets/api
- **PHPMailer:** https://github.com/PHPMailer/PHPMailer
- **Composer:** https://getcomposer.org/doc/

### Conference Contact
- **Email:** ncsdess2026@hitam.org

---

## 🎓 How It Works (Simple Explanation)

```
User submits form
     ↓
PHP validates data
     ↓
Generates Abstract ID
     ↓
Saves to Google Sheets ← [CRITICAL: Must succeed]
     ↓
Sends 3 emails (via SMTP)
     ↓
Returns success to user
```

**Key Point:** If Google Sheets fails, nothing else happens (no emails sent).

---

## 📈 Monitoring

### Daily Checks
1. Open Google Sheet - verify new submissions
2. Check `logs/errors.log` - look for issues
3. Verify admin emails arriving

### Weekly Checks
1. Review submission counts
2. Export Google Sheet for backup
3. Check for unusual activity

---

## 🔐 Important Security Notes

### Never Commit These Files
- `config/gsheets-service.json`
- `config/email-config.php` (with real credentials)
- `logs/*.log`

### File Permissions
- Config files: `644` (readable)
- PHP files: `644`
- Logs directory: `755` (writable)

### Production Settings
```php
ini_set('display_errors', 0);  // Hide errors from users
ini_set('log_errors', 1);      // Log errors to file
```

---

## ✅ Success Indicators

You know it's working when:

1. ✅ Form submits without errors
2. ✅ User sees success message with Abstract ID
3. ✅ Google Sheet has new row immediately
4. ✅ Participant receives 2 emails within seconds
5. ✅ Admin receives notification email
6. ✅ No errors in `logs/errors.log`

---

## 🎯 Next Steps

1. **Review:** Read [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md)
2. **Setup:** Follow the checklist step-by-step
3. **Test:** Submit test abstracts
4. **Verify:** Check sheets and emails
5. **Deploy:** Go live with confidence!

---

## 📝 Notes

- Abstract IDs are unique (date + random hex)
- Google Sheets is the primary data store
- Emails are secondary (if they fail, data is still saved)
- All errors are logged for troubleshooting
- Service account has limited permissions (safer)

---

## 🏆 Benefits of This System

✅ **Automated** - No manual data entry  
✅ **Instant** - Emails and sheets update immediately  
✅ **Reliable** - Error handling and logging  
✅ **Secure** - Encrypted email, protected configs  
✅ **Scalable** - Handles many submissions  
✅ **Trackable** - All data in one spreadsheet  
✅ **Professional** - Branded email templates  

---

**Ready to get started? Open [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md) and begin!**

---

*Implementation completed: January 8, 2026*  
*For NC-SDESS 2026 Conference | HITAM, Hyderabad*
