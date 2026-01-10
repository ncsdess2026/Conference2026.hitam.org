# Quick Start Guide - Abstract & Poster Submission System

## What's New?

Your conference website now has two new sections for abstract and poster submissions with **automatic email notifications**:

1. **Call for Abstract** - Mandatory submission before paper/poster presentations
2. **Call for Posters** - Separate poster submission with file upload

## Getting Started (5 Minutes)

### Step 1: Update Conference Email Address

Open both these files and update the email address:

**File 1: `process-abstract.php` (Line 6)**
```php
$CONFERENCE_EMAIL = "YOUR_CONFERENCE_EMAIL@hitam.org";
```

**File 2: `process-poster.php` (Line 6)**
```php
$CONFERENCE_EMAIL = "YOUR_CONFERENCE_EMAIL@hitam.org";
```

### Step 2: Verify Email Server Setup

Your server needs email capability. Choose one:

#### If you're on Linux/Unix (Most Common):
- Email should work automatically ✓
- Server has sendmail or postfix installed

#### If you're on Windows or want SMTP:
- Add SMTP configuration to the PHP files
- See detailed setup in `ABSTRACT_POSTER_SETUP.md`

### Step 3: Test the System

1. Go to your conference site
2. Click "Call for Abstract" in the navigation menu
3. Fill the form and submit
4. Check your email for two automatic emails:
   - ✓ Thank you email
   - ✓ Acceptance email

**That's it!** The system is ready to use.

## What Happens When Someone Submits?

### Automatic Flow:

```
Participant fills form
        ↓
Clicks "Submit Abstract"
        ↓
Form validation (client-side + server-side)
        ↓
✓ Generates unique Submission ID (e.g., ABS-2026-A1B2C3)
✓ Saves submission to /submissions/ folder
        ↓
Automatically sends EMAIL 1:
  - Thank You Email with submission details
        ↓
Automatically sends EMAIL 2:
  - Acceptance Notification Email
        ↓
Admin notification email sent to conference email
        ↓
Participant sees success message with Submission ID
```

## Files Created/Modified

### New Files:
```
conference-site/
├── process-abstract.php          (NEW) - Handles abstract submissions
├── process-poster.php            (NEW) - Handles poster submissions
└── ABSTRACT_POSTER_SETUP.md      (NEW) - Detailed setup guide
```

### Modified Files:
```
conference-site/
├── index.html                    (UPDATED) - Added 2 new sections + nav links
├── assets/css/styles.css         (UPDATED) - Added form styling
└── assets/js/main.js             (UPDATED) - Added form handling & word counter
```

## Forms Added to Website

### 1. Call for Abstract Form
- **Location:** After "Tracks" section, before "Important Dates"
- **Fields:**
  - Name, Email, Phone, Organization
  - Track Selection (7 tracks)
  - Paper/Poster Title
  - Co-Authors (optional)
  - Keywords (3-5)
  - Abstract Content (250-300 words with live word counter)
  - Submission Type (Paper or Poster)
  - Agreement checkbox

### 2. Call for Posters Form
- **Location:** After "Call for Abstract" section
- **Fields:**
  - Same as above PLUS
  - File Upload (PDF/PNG/JPG, max 10 MB)
  - Poster Description

## Email Templates Included

### Email 1: Thank You Email
```
✓ Professional HTML email
✓ Shows submission ID
✓ Lists all submitted details
✓ Confirms receipt
```

### Email 2: Acceptance Email
```
✓ Congratulations message
✓ Next steps for participant
✓ Conference dates & venue
✓ Registration instructions
```

## Important Features

✅ **Automatic Emails** - No manual intervention needed
✅ **Word Counter** - Live count for abstract (250-300 words)
✅ **File Upload** - Secure poster upload (max 10 MB)
✅ **Unique IDs** - Each submission gets unique ID (ABS-2026-XXXXXX)
✅ **Validation** - Client and server-side validation
✅ **Mobile Responsive** - Works on all devices
✅ **Data Storage** - Saved locally for backup
✅ **Admin Notifications** - Admins get notified of submissions

## Customization Options

### Change Track Names
Edit the track names in the form:
- In `process-abstract.php` - Update `$track_names` array (lines ~140)
- In `process-poster.php` - Update `$track_names` array (lines ~230)

### Change Email Templates
- Edit functions in process-abstract.php:
  - `generate_thank_you_email()`
  - `generate_acceptance_email()`
  - `generate_admin_notification_email()`

### Change Submission ID Format
In process-abstract.php (line ~68):
```php
// Current format: ABS-2026-XXXXXX
$submission_id = 'ABS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
```

## Where are Submissions Stored?

```
conference-site/
├── submissions/
│   ├── ABS-2026-A1B2C3.json    (Abstract metadata)
│   └── POST-2026-X9Y8Z7.json   (Poster metadata)
└── uploads/
    └── posters/
        ├── POST-2026-X9Y8Z7.pdf   (Uploaded poster file)
        └── POST-2026-X9Y8Z7.jpg
```

**Note:** These directories are created automatically. Make sure they're writable:
```bash
chmod 755 submissions/
chmod 755 uploads/posters/
```

## Testing Checklist

- [ ] Update conference email address in both PHP files
- [ ] Test abstract form submission
- [ ] Check if thank you email arrives
- [ ] Check if acceptance email arrives
- [ ] Test with various file formats (for posters)
- [ ] Test on mobile device
- [ ] Verify submission files are saved
- [ ] Check admin notification email

## Troubleshooting Quick Fix

### Issue: Emails not being sent
**Solution:** Check if your server has email configured
```bash
# On Linux, verify mail service:
sudo systemctl status postfix
# or
sudo systemctl status sendmail
```

### Issue: File upload not working
**Solution:** Create upload directory and set permissions
```bash
mkdir -p uploads/posters
chmod 755 uploads/
chmod 755 uploads/posters/
```

### Issue: Submissions directory not found
**Solution:** Create submissions directory
```bash
mkdir -p submissions
chmod 755 submissions/
```

## Next Steps

1. ✅ Update the conference email address
2. ✅ Test form submissions
3. ✅ Verify emails are being sent
4. ✅ Check submission files are saved
5. ✅ Share the links with participants!

## Need Help?

For detailed setup instructions, see: `ABSTRACT_POSTER_SETUP.md`

For quick customizations, modify:
- Email content → Edit email template functions in PHP
- Track names → Update arrays in PHP files
- Form styling → Modify CSS in `assets/css/styles.css`
- Form behavior → Edit JavaScript in `assets/js/main.js`

---

**Ready to go live?** Share these links with participants:
- Abstract Submission: yoursite.com#call-for-abstract
- Poster Submission: yoursite.com#call-for-posters
