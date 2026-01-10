# Abstract & Poster Submission System Setup Guide

## Overview

The NC-SDESS 2026 Conference website now includes automated abstract and poster submission forms with automatic email notifications. When participants submit an abstract or poster, they will automatically receive:
1. **Thank You Email** - Acknowledging receipt of their submission
2. **Acceptance Email** - Confirming acceptance of their submission

## Features

### Call for Abstract Section
- Mandatory for both paper and poster submissions
- Word limit: 250-300 words
- Collects author information, track selection, keywords
- Automatic acknowledgment and acceptance emails
- Unique submission ID generation

### Call for Posters Section
- Separate submission form for posters
- Requires abstract to be submitted first (as per requirements)
- File upload support (PDF, PNG, JPG up to 10 MB)
- Same automatic email notifications as abstract submissions

## Files Modified/Created

### New Files:
1. **process-abstract.php** - Backend script for processing abstract submissions and sending emails
2. **process-poster.php** - Backend script for processing poster submissions and sending emails

### Modified Files:
1. **index.html** - Added two new sections:
   - `#call-for-abstract` - Abstract submission section
   - `#call-for-posters` - Poster submission section
   - Updated navigation menu with new links

2. **assets/css/styles.css** - Added comprehensive form styling:
   - Form input styles
   - Form validation states
   - Button states and animations
   - Message containers for success/error notifications
   - Responsive design for mobile

3. **assets/js/main.js** - Added JavaScript functionality:
   - Word counter for abstract content
   - Form submission handling via AJAX
   - Dynamic message display
   - Loading states and animations

## Email Configuration

### Conference Email Address
Update the following in the PHP files to set your conference email:

**In `process-abstract.php` (Line 6):**
```php
$CONFERENCE_EMAIL = "ncsdess2026@hitam.org"; // Change this to your conference email
```

**In `process-poster.php` (Line 6):**
```php
$CONFERENCE_EMAIL = "ncsdess2026@hitam.org"; // Change this to your conference email
```

### Email Server Configuration

The system uses PHP's `mail()` function. Ensure your server has mail functionality configured:

#### Option 1: Using PHP Built-in Mail (Linux/Unix)
The `mail()` function uses the server's sendmail configuration. No additional setup needed if your server has sendmail installed.

#### Option 2: Using SMTP (Recommended for Windows/Reliability)

Modify the PHP scripts to use SMTP instead of the default mail() function. Add this configuration:

```php
// Add this at the top of process-abstract.php and process-poster.php after the opening <?php tag

ini_set("SMTP", "smtp.gmail.com"); // Or your mail server
ini_set("smtp_port", "587");
ini_set("sendmail_from", "ncsdess2026@hitam.org");

// For Gmail, you may need to:
// 1. Enable "Less secure app access" in Gmail settings
// 2. Or use an App Password if 2FA is enabled
```

#### Option 3: Using PHPMailer (Most Reliable)

For production environments, use PHPMailer library. Here's how to modify the scripts:

1. Install PHPMailer:
```bash
composer require phpmailer/phpmailer
```

2. Replace the `mail()` calls in the PHP scripts with:
```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Or your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your-email@gmail.com';
    $mail->Password   = 'your-app-password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('ncsdess2026@hitam.org', $CONFERENCE_NAME);
    $mail->addAddress($email);
    $mail->Subject = $subject_thank_you;
    $mail->isHTML(true);
    $mail->Body = $message_thank_you;
    
    $mail->send();
} catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
}
```

## File Storage

### Abstract Submissions
Submissions are stored in: `/conference-site/submissions/ABS-YYYY-XXXXXX.json`
- Contains participant details, abstract content, and metadata
- JSON format for easy processing and integration with databases

### Poster Files
Poster files are stored in: `/conference-site/uploads/posters/POST-YYYY-XXXXXX.pdf|jpg|png`
- Original metadata stored separately in `/submissions/` directory
- Supports PDF and image formats

## Directory Structure

Ensure these directories exist and are writable:
```
conference-site/
├── submissions/           (Created automatically)
│   ├── ABS-YYYY-XXXXXX.json
│   └── POST-YYYY-XXXXXX.json
├── uploads/
│   └── posters/          (Created automatically)
│       ├── POST-YYYY-XXXXXX.pdf
│       ├── POST-YYYY-XXXXXX.jpg
│       └── POST-YYYY-XXXXXX.png
└── process-abstract.php
└── process-poster.php
```

## Form Validation

### Client-Side (JavaScript)
- Real-time word counting for abstracts
- Required field validation
- Email format validation
- File type and size checking

### Server-Side (PHP)
- Sanitization of all inputs
- Email validation (filter_var)
- Word count verification (150-400 words ≈ 250-300 words)
- File MIME type checking
- File size validation (max 10 MB)
- Unique submission ID generation

## Email Templates

The system automatically generates two HTML emails for each submission:

### 1. Thank You Email
- Confirms receipt of submission
- Displays submission ID
- Shows all submitted details
- Professional formatting with branding

### 2. Acceptance Email
- Congratulates participant on acceptance
- Lists next steps for registration and payment
- Provides important dates and venue information
- Encourages participation

### 3. Admin Notification (Internal)
- Notifies conference administrator of new submission
- Contains all submission details
- Helps track submissions

## Customization

### Email Content
To customize email templates, modify the functions:
- `generate_thank_you_email()` - in process-abstract.php or process-poster.php
- `generate_acceptance_email()` - in process-abstract.php or process-poster.php
- `generate_admin_notification_email()` - in process-abstract.php or process-poster.php

### Track Names
Update the `$track_names` array in both PHP files if track names change:
```php
$track_names = [
    '1' => 'Your Track Name Here',
    // ...
];
```

### Submission ID Format
Change the prefix in process-abstract.php and process-poster.php:
```php
// Current: ABS-YYYY-XXXXXX (for abstract), POST-YYYY-XXXXXX (for poster)
$submission_id = 'ABS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
```

## Testing

### Local Testing
1. Ensure PHP is installed with mail capabilities
2. Run a local server: `php -S localhost:8000`
3. Test the forms on `http://localhost:8000/index.html`

### Email Testing
For testing without sending real emails, add this to PHP files:
```php
// Instead of actual mail, save to file (for testing)
// file_put_contents(__DIR__ . '/test-email.txt', $message_thank_you);
// file_put_contents(__DIR__ . '/test-email-2.txt', $message_acceptance);
```

### Production Checklist
- [ ] Set correct conference email address
- [ ] Test email sending with real SMTP server
- [ ] Verify all track names are correct
- [ ] Test form submission with valid data
- [ ] Test file upload with various formats
- [ ] Check that submission files are being saved
- [ ] Verify email delivery to multiple test addresses
- [ ] Test on mobile devices
- [ ] Set up admin email monitoring for notifications

## Security Considerations

1. **Input Sanitization**: All inputs are sanitized using `htmlspecialchars()`
2. **Email Validation**: Uses PHP's `filter_var()` with FILTER_VALIDATE_EMAIL
3. **File Validation**: 
   - MIME type checking
   - Size limits (10 MB)
   - Safe filename generation
4. **CSRF Protection**: Consider adding CSRF tokens for production
5. **Rate Limiting**: Consider implementing rate limiting to prevent spam
6. **Database Storage**: For production, store submissions in database instead of JSON files

## Database Integration (Recommended for Production)

Instead of file-based storage, integrate with a database:

```php
// Example: MySQL Integration
$pdo = new PDO('mysql:host=localhost;dbname=ncsdess', 'user', 'password');

$stmt = $pdo->prepare('INSERT INTO abstracts (submission_id, name, email, phone, organization, track, title, keywords, content, submission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

$stmt->execute([
    $submission_id, $name, $email, $phone, $organization,
    $track, $title, $keywords, $content, date('Y-m-d H:i:s')
]);
```

## Troubleshooting

### Emails Not Sending
1. Check PHP error logs: `tail -f /var/log/php-errors.log`
2. Verify mail server is running: `ps aux | grep sendmail` or `ps aux | grep postfix`
3. Check SMTP credentials if using external SMTP
4. Look for firewall blocking outbound SMTP (port 25, 465, 587)

### File Upload Issues
1. Check directory permissions: `chmod 755 uploads/ submissions/`
2. Verify max upload size in php.ini: `upload_max_filesize = 10M`
3. Check disk space: `df -h`

### Form Not Submitting
1. Check browser console for JavaScript errors (F12)
2. Verify process-abstract.php and process-poster.php are in correct location
3. Check PHP file permissions: `chmod 644 process-*.php`

## Support & Maintenance

- Monitor `submissions/` directory for growth
- Regularly backup submission data
- Check admin notification emails
- Update track names if they change
- Test email delivery periodically

## Future Enhancements

- [ ] Database storage integration
- [ ] Payment gateway integration
- [ ] Automated review workflow
- [ ] Participant dashboard to view submission status
- [ ] Email templates customization via admin panel
- [ ] Spam filtering and rate limiting
- [ ] Two-factor authentication for sensitive operations
- [ ] SMS notifications alongside email

---

**For additional support or questions, contact:** ncsdess2026@hitam.org
