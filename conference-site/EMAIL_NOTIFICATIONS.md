# Email Notifications - What Participants Receive

## Overview

When a participant submits an abstract or poster, they automatically receive **TWO professional emails**:

1. **Thank You Email** (Immediate)
2. **Acceptance Notification Email** (Immediate)

Both emails are sent from the conference email address (ncsdess2026@hitam.org).

---

## Email 1: Thank You Email

### Subject Line:
```
Thank You for Your Abstract Submission - NC-SDESS 2026 Conference
```
or
```
Thank You for Your Poster Submission - NC-SDESS 2026 Conference
```

### Email Content:

```html
---
Dear [Participant Name],

Thank you for submitting your [Paper/Poster] to the 1st National Conference 
on Solution Driven Engineering for Sustainable Society (NC-SDESS: 2026).

We have successfully received your [Abstract/Poster] submission. Below are your 
submission details:

SUBMISSION DETAILS:
├─ Submission ID: ABS-2026-A1B2C3
├─ Name: [Participant Name]
├─ Email: [Email Address]
├─ Organization: [Organization Name]
├─ Track: [Selected Track Name]
├─ Title: [Paper/Poster Title]
├─ Submission Type: [Paper/Poster]
└─ Submitted On: [Date & Time]

WHAT'S NEXT?
✓ Your [abstract/poster] is under review by our technical committee.
✓ You will receive an acceptance notification email shortly.
✓ If submitting a full paper, please prepare according to guidelines.
✓ Keep your Submission ID safe - you'll need it for all correspondence.

If you have any questions, contact us at: ncsdess2026@hitam.org

Best regards,
NC-SDESS 2026 Conference Team

---
This is an automated email. Please do not reply directly to this message.
© 2026 NC-SDESS Conference. All rights reserved.
```

### Design Elements:
- ✓ Professional gradient header
- ✓ Conference branding
- ✓ Clear submission details box
- ✓ Next steps listed
- ✓ Contact information
- ✓ HTML formatted for all email clients

---

## Email 2: Acceptance Notification Email

### Subject Line:
```
Your Abstract Submission Accepted - NC-SDESS 2026 Conference
```
or
```
Your Poster Submission Accepted - NC-SDESS 2026 Conference
```

### Email Content:

```html
---
🎉 CONGRATULATIONS!

YOUR [ABSTRACT/POSTER] HAS BEEN ACCEPTED

Dear [Participant Name],

We are pleased to inform you that your [paper/poster] has been ACCEPTED for 
the 1st National Conference on Solution Driven Engineering for Sustainable 
Society (NC-SDESS: 2026).

YOUR SUBMISSION DETAILS:
├─ Submission ID: ABS-2026-A1B2C3
├─ Title: [Paper/Poster Title]
├─ Track: [Selected Track Name]
├─ Submission Type: [Paper/Poster]
└─ Authors: [Author Names]

NEXT STEPS:
1. Register for the Conference
   → Complete your registration at our website using this Submission ID.

2. Pay Registration Fee
   → Follow the payment instructions provided on the registration page.

3. Prepare Full Paper/Poster
   → Use the provided template to prepare your manuscript or poster.
   → For posters: Ensure format is A1 (594 × 841 mm)

4. Submit Camera-Ready Manuscript
   → Upload your final manuscript before the deadline.

5. Attend the Conference
   → Present your work on January 28-29, 2026

IMPORTANT DATES:
• Abstract Submission Deadline: January 10, 2026
• Full Paper Submission: January 20, 2026
• Conference Dates: January 28-29, 2026
• Venue: Hyderabad Institute of Technology and Management (HITAM)

For queries, contact: ncsdess2026@hitam.org

We look forward to your participation!

Best regards,
NC-SDESS 2026 Conference Team

---
This is an automated email. Please do not reply directly to this message.
© 2026 NC-SDESS Conference. All rights reserved.
```

### Design Elements:
- ✓ Celebratory emoji and success badge
- ✓ Clear acceptance confirmation
- ✓ Submission details highlighted
- ✓ Step-by-step next actions
- ✓ Important dates listed
- ✓ Venue information
- ✓ Contact for queries
- ✓ Professional footer

---

## Email 3: Admin Notification (Internal Use)

### To: ncsdess2026@hitam.org

### Subject Line:
```
New Abstract Submission - ABS-2026-A1B2C3
```
or
```
New Poster Submission - POST-2026-X9Y8Z7
```

### Content Includes:
- Submission ID
- Submission timestamp
- Participant name, email, phone, organization
- Complete submission details
- Full abstract/description
- Keywords/co-authors
- IP address of submission
- File name and size (for posters)

### Purpose:
- Helps admins track submissions
- Enables manual review if needed
- Provides backup notification
- Allows monitoring of submission volume

---

## Email Delivery Timeline

```
User submits form
   ↓
[< 1 second]
   ↓
Email 1: Thank You → Sent to participant
Email 2: Acceptance → Sent to participant
Email 3: Admin Notification → Sent to admin
   ↓
[1-5 minutes]
   ↓
Emails appear in participants' inboxes
```

---

## Email Personalization

Each email includes:
- ✓ Participant's name
- ✓ Their email address
- ✓ Their organization
- ✓ Selected track name
- ✓ Submission title
- ✓ Co-author names (if provided)
- ✓ Unique submission ID
- ✓ Exact submission date/time

---

## Customizing Email Content

To modify email content, edit these functions in the PHP files:

### In `process-abstract.php`:
```php
// Lines ~140-200
function generate_thank_you_email($data) {
    // Modify HTML here
}

// Lines ~205-280
function generate_acceptance_email($data) {
    // Modify HTML here
}
```

### In `process-poster.php`:
```php
// Lines ~230-290
function generate_thank_you_email($data) {
    // Modify HTML here
}

// Lines ~295-370
function generate_acceptance_email($data) {
    // Modify HTML here
}
```

---

## Email Customization Examples

### Change the greeting:
```php
// From:
$html = "<p>Dear <strong>{$data['name']}</strong>,</p>";

// To:
$html = "<p>Hello {$data['name']},<br>Thank you for your amazing submission!</p>";
```

### Add social media links:
```php
// Add before closing </body>
<div style="text-align: center; margin-top: 20px;">
    Follow us: 
    <a href="https://facebook.com/ncsdess2026">Facebook</a> | 
    <a href="https://twitter.com/ncsdess2026">Twitter</a>
</div>
```

### Add deadline for camera-ready:
```php
// Add in acceptance email
<p><strong>Camera-Ready Deadline: January 20, 2026</strong></p>
<p>Please submit your final manuscript by this date.</p>
```

---

## Troubleshooting Email Issues

### Issue: Participants not receiving emails
**Possible Causes:**
1. Email server not configured
2. Emails going to spam folder
3. Invalid email addresses in forms
4. PHP mail() function disabled

**Solutions:**
- Check PHP error logs
- Ask participants to check spam folder
- Verify email addresses are valid
- Enable mail() or use SMTP alternative

### Issue: Emails arriving late
**Possible Causes:**
1. Server mail queue backed up
2. SMTP server slow
3. Network latency

**Solutions:**
- Check mail server logs
- Use dedicated SMTP service (Mailgun, SendGrid)
- Implement job queue for email sending

### Issue: Email formatting looks wrong
**Possible Causes:**
1. Email client doesn't support HTML
2. CSS not rendering properly
3. Images not loading

**Solutions:**
- Test with multiple email clients
- Use inline CSS instead of style tags
- Host images externally
- Provide text-only alternative

---

## Email Best Practices

### ✓ DO:
- Always include submission ID
- Provide clear next steps
- Include contact information
- Use professional formatting
- Test on multiple email clients
- Monitor delivery rates
- Keep emails brief and scannable

### ✗ DON'T:
- Use too many colors or images
- Make emails longer than needed
- Use technical jargon
- Forget to personalize
- Send from noreply address (use real email)
- Include sensitive information
- Use poorly formatted HTML

---

## Testing Emails

### Before Going Live:

1. **Test with Gmail:**
   ```
   Send test submission → Check inbox
   Check spam folder → Verify HTML rendering
   ```

2. **Test with Outlook:**
   ```
   Repeat above steps
   Verify formatting
   Check image loading
   ```

3. **Test with Mobile:**
   ```
   Open email on phone
   Check responsiveness
   Verify all links work
   ```

4. **Test Submission ID:**
   ```
   Verify unique IDs generated
   Check ID appears in all emails
   ```

---

## Email Analytics

Consider tracking:
- ✓ Number of submissions
- ✓ Email delivery rate
- ✓ Email open rate
- ✓ Click-through rate to registration
- ✓ Peak submission times
- ✓ Geographic distribution

---

## GDPR & Privacy Considerations

The system:
- ✓ Only sends emails to participants who submitted
- ✓ Includes unsubscribe option (add if needed)
- ✓ Stores minimal required data
- ✓ Doesn't share data with third parties
- ✓ Uses secure email transmission

### To Add Unsubscribe Link:
```php
// Add to email footer
<p><a href="https://yoursite.com/unsubscribe?id={$submission_id}">
   Unsubscribe from future emails
</a></p>
```

---

## Multi-Language Support

To support multiple languages, modify email generation:

```php
function generate_thank_you_email($data, $language = 'en') {
    if ($language === 'hi') {
        // Hindi version
        $greeting = "नमस्ते {$data['name']},";
    } else {
        // English version
        $greeting = "Dear {$data['name']},";
    }
    // ... rest of email
}
```

---

## Contact & Support

For email configuration help or customization requests:
- Email: ncsdess2026@hitam.org
- See detailed guide: ABSTRACT_POSTER_SETUP.md
- See quick setup: QUICK_START.md

---

**Last Updated:** January 7, 2026
**Version:** 1.0
