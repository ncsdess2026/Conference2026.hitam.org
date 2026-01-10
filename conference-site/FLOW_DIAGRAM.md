# Abstract Submission Flow - Visual Guide

```
┌─────────────────────────────────────────────────────────────────────┐
│                    USER SUBMITS ABSTRACT FORM                        │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    PROCESS-ABSTRACT.PHP                              │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  STEP 1: VALIDATE INPUT                                     │    │
│  │  • Check required fields                                    │    │
│  │  • Validate email format                                    │    │
│  │  • Verify submission type (paper/poster)                   │    │
│  │  • Check word count (150-400)                               │    │
│  │  • Verify agreement checkbox                                │    │
│  └────────────────────────────────────────────────────────────┘    │
│                               │                                      │
│                               ▼                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  STEP 2: GENERATE ABSTRACT ID                               │    │
│  │  Format: ABS-YYYYMMDD-XXXXXX                                │    │
│  │  Example: ABS-20260108-A3F91C                               │    │
│  └────────────────────────────────────────────────────────────┘    │
│                               │                                      │
│                               ▼                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  STEP 3: SAVE TO GOOGLE SHEETS                              │    │
│  │  ┌──────────────────────────────────────────────────────┐  │    │
│  │  │  • Load service account credentials                   │  │    │
│  │  │  • Connect to Google Sheets API                       │  │    │
│  │  │  • Append row with all submission data                │  │    │
│  │  │  • Return success confirmation                        │  │    │
│  │  └──────────────────────────────────────────────────────┘  │    │
│  │                                                              │    │
│  │  ❌ IF FAILS: Stop process, log error, return error to user │    │
│  └────────────────────────────────────────────────────────────┘    │
│                               │                                      │
│                               ▼                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  STEP 4: SEND EMAILS VIA SMTP                               │    │
│  │  ┌──────────────────────────────────────────────────────┐  │    │
│  │  │  EMAIL 1: Thank You Email                            │  │    │
│  │  │  • To: Participant                                    │  │    │
│  │  │  • From: ncsdess2026@hitam.org                       │  │    │
│  │  │  • Subject: Thank You for Your Submission            │  │    │
│  │  │  • Content: Submission details + Abstract ID         │  │    │
│  │  └──────────────────────────────────────────────────────┘  │    │
│  │                         │                                    │    │
│  │                         ▼                                    │    │
│  │  ┌──────────────────────────────────────────────────────┐  │    │
│  │  │  EMAIL 2: Acceptance Email                           │  │    │
│  │  │  • To: Participant                                    │  │    │
│  │  │  • From: ncsdess2026@hitam.org                       │  │    │
│  │  │  • Subject: Abstract Accepted                        │  │    │
│  │  │  • Content: Congratulations + Next steps             │  │    │
│  │  └──────────────────────────────────────────────────────┘  │    │
│  │                         │                                    │    │
│  │                         ▼                                    │    │
│  │  ┌──────────────────────────────────────────────────────┐  │    │
│  │  │  EMAIL 3: Admin Notification                         │  │    │
│  │  │  • To: ncsdess2026@hitam.org                        │  │    │
│  │  │  • From: ncsdess2026@hitam.org                      │  │    │
│  │  │  • Subject: New Submission - [Abstract ID]          │  │    │
│  │  │  • Content: Full submission details                  │  │    │
│  │  └──────────────────────────────────────────────────────┘  │    │
│  └────────────────────────────────────────────────────────────┘    │
│                               │                                      │
│                               ▼                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  STEP 5: RETURN SUCCESS RESPONSE                            │    │
│  │  {                                                           │    │
│  │    "success": true,                                          │    │
│  │    "message": "Abstract submitted successfully!",            │    │
│  │    "submission_id": "ABS-20260108-A3F91C",                  │    │
│  │    "emails_sent": true                                       │    │
│  │  }                                                           │    │
│  └────────────────────────────────────────────────────────────┘    │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    USER SEES SUCCESS MESSAGE                         │
│  "Abstract submitted successfully! Check your email for              │
│   confirmation and acceptance notification."                         │
│  Abstract ID: ABS-20260108-A3F91C                                   │
└─────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════
                         PARALLEL OUTCOMES
═══════════════════════════════════════════════════════════════════════

┌─────────────────────────┐    ┌─────────────────────────┐    ┌──────────────────────┐
│   GOOGLE SHEETS         │    │   PARTICIPANT INBOX     │    │   ADMIN INBOX        │
├─────────────────────────┤    ├─────────────────────────┤    ├──────────────────────┤
│ New row added:          │    │ Email 1:                │    │ New submission       │
│ • Submission ID         │    │ "Thank You for Your     │    │ notification:        │
│ • Timestamp             │    │  Submission"            │    │ • Abstract ID        │
│ • Name                  │    │ + Submission details    │    │ • Participant info   │
│ • Email                 │    │ + Abstract ID           │    │ • Abstract content   │
│ • Phone                 │    │                         │    │ • Timestamp          │
│ • Organization          │    │ Email 2:                │    │                      │
│ • Track                 │    │ "Abstract Accepted!"    │    │                      │
│ • Title                 │    │ + Next steps            │    │                      │
│ • Co-Authors            │    │ + Registration info     │    │                      │
│ • Keywords              │    │ + Important dates       │    │                      │
│ • Abstract Content      │    │                         │    │                      │
│ • Submission Type       │    │                         │    │                      │
│ • Word Count            │    │                         │    │                      │
│ • IP Address            │    │                         │    │                      │
└─────────────────────────┘    └─────────────────────────┘    └──────────────────────┘


═══════════════════════════════════════════════════════════════════════
                         ERROR HANDLING
═══════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────┐
│  IF GOOGLE SHEETS FAILS:                                             │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  • Stop immediately (no emails sent)                        │    │
│  │  • Log error to: logs/errors.log                            │    │
│  │  • Return error response to user:                           │    │
│  │    "Failed to save submission. Please try again later       │    │
│  │     or contact support."                                    │    │
│  └────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  IF EMAIL FAILS:                                                     │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │  • Data already saved to Google Sheets ✓                    │    │
│  │  • Log email error to: logs/errors.log                      │    │
│  │  • Still return success (data is saved)                     │    │
│  │  • Admin can manually resend emails if needed               │    │
│  └────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════
                      CONFIGURATION FILES
═══════════════════════════════════════════════════════════════════════

config/email-config.php
├── SMTP Host (smtp.gmail.com)
├── SMTP Port (587)
├── SMTP Username (ncsdess2026@hitam.org)
├── SMTP Password (App Password)
└── From/Reply-To addresses

config/sheets-config.php
├── Spreadsheet ID
├── Sheet Name ("Abstract Submissions")
└── Column Headers

config/gsheets-service.json
├── Service Account Email
├── Private Key
└── Project ID


═══════════════════════════════════════════════════════════════════════
                      SECURITY MEASURES
═══════════════════════════════════════════════════════════════════════

✓ Input validation & sanitization
✓ Email format verification
✓ Word count limits
✓ Submission type restrictions
✓ Config files protected (.htaccess)
✓ Sensitive files in .gitignore
✓ Error logging (not displayed to users)
✓ SMTP encryption (TLS/SSL)
✓ Service account permissions (limited scope)


═══════════════════════════════════════════════════════════════════════
                      MONITORING & LOGS
═══════════════════════════════════════════════════════════════════════

logs/errors.log
├── Google Sheets API errors
├── SMTP connection failures
├── Email sending failures
└── Validation errors

Google Sheets
├── Real-time submission tracking
├── All data centralized
└── Easy export/analysis

Admin Email Notifications
├── Immediate awareness of new submissions
├── Full submission details
└── No need to check sheet constantly
```

---

## Quick Reference

| Component | Purpose | Location |
|-----------|---------|----------|
| **Form Handler** | Processes submissions | `process-abstract.php` |
| **Google Sheets API** | Saves data to spreadsheet | `vendor/google/apiclient` |
| **PHPMailer** | Sends SMTP emails | `vendor/phpmailer/phpmailer` |
| **Email Config** | SMTP credentials | `config/email-config.php` |
| **Sheets Config** | Sheet ID & columns | `config/sheets-config.php` |
| **Service Account** | Google API auth | `config/gsheets-service.json` |
| **Error Logs** | Troubleshooting | `logs/errors.log` |

---

## Abstract ID Format

```
ABS-20260108-A3F91C
│   │        │
│   │        └─── Random 6-char hex (unique)
│   └──────────── Date (YYYYMMDD)
└──────────────── Prefix (Abstract)
```

---

**Implementation Date:** January 8, 2026  
**Conference:** NC-SDESS 2026  
**Institution:** HITAM, Hyderabad
