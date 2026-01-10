# Option B: Database Integration Setup Guide

## Overview
This system automates the entire payment-to-confirmation flow:
1. Student pays → Webhook captures data → Database stores it
2. Professor views pending verifications in admin dashboard
3. Professor clicks "Verify" and/or "Send Confirmation"
4. System automatically sends confirmation emails with unique registration IDs

## Prerequisites
- PHP 7.4+ with MySQL support
- MySQL/MariaDB database
- Server that can receive webhooks (for payment gateway integration)

---

## Step 1: Database Setup

### 1.1 Create the Database
```sql
CREATE DATABASE nc_sdess_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.2 Run Database Initialization
Visit your server at: `http://yourserver.com/conference-site/db-init.php`

This will create 5 tables:
- **participants**: Student/faculty information
- **payments**: Payment transaction records
- **submissions**: Abstract, paper, poster submissions
- **confirmations**: Registration and confirmation status
- **email_logs**: Email delivery tracking

### 1.3 Configure Database Connection
Edit `db-config.php`:
```php
define('DB_HOST', 'localhost');      // Your MySQL host
define('DB_USER', 'root');           // Your MySQL user
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'nc_sdess_2026');  // Database name
```

---

## Step 2: Payment Gateway Webhook Integration

### 2.1 Payment to Webhook Endpoint Flow

**Endpoint**: `webhook-payment-receiver.php`

**Expected JSON Payload**:
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "+91-9876543210",
  "organization": "HITAM",
  "category": "Student",
  "participation_type": "Paper",
  "transaction_id": "TXN123456789",
  "amount_paid": 500.00,
  "payment_method": "Online",
  "attendance_mode": "In-person"
}
```

### 2.2 Setting Up Payment Gateway Webhook

If using Google Forms + Razorpay/PayPal/Stripe:

**A. Using Razorpay Webhook**:
1. Log in to Razorpay Dashboard
2. Go to Settings → Webhooks
3. Add webhook URL: `http://yourserver.com/conference-site/webhook-payment-receiver.php`
4. Select events: `payment.authorized`, `payment.captured`
5. Configure your server to extract payment data and send JSON to webhook

**B. Using Custom Payment Form**:
1. Create a form that collects student details + payment
2. On successful payment, POST data to webhook endpoint
3. Webhook automatically creates database records

### 2.3 Example: Simple Payment Integration
```html
<!-- On your payment page, after successful payment -->
<script>
fetch('webhook-payment-receiver.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    first_name: "John",
    last_name: "Doe",
    email: "john@example.com",
    phone: "+91-9876543210",
    organization: "HITAM",
    category: "Student",
    participation_type: "Paper",
    transaction_id: "TXN-" + Date.now(),
    amount_paid: 500,
    payment_method: "Online",
    attendance_mode: "In-person"
  })
})
.then(r => r.json())
.then(data => console.log('Payment recorded:', data));
</script>
```

---

## Step 3: Admin Dashboard

### 3.1 Access Admin Dashboard
Visit: `http://yourserver.com/conference-site/admin-dashboard.html`

**Dashboard Shows**:
- Total verified payments
- Pending verifications (needs professor approval)
- Confirmations sent count
- List of all participants with actions

### 3.2 Admin Workflow

**Single Participant**:
1. Find participant in table
2. Click "Verify" (if payment not verified)
3. Click "Send" (to send confirmation email)
4. Click "Resend" (to resend confirmation)

**Bulk Actions**:
1. Check "Select All" or select individual checkboxes
2. Click "Verify Selected" (marks all as verified)
3. Click "Send Confirmations" (sends emails to all)

### 3.3 Filtering & Search
- **Filter by Category**: Student, Faculty, Professional
- **Filter by Status**: Pending Verification, Verified (No Confirmation), Confirmed & Sent
- **Search by Email**: Real-time search

---

## Step 4: Automatic Email System

### 4.1 Email Files

**API Endpoints**:
- `api-get-dashboard-stats.php` - Get dashboard statistics
- `api-get-pending-participants.php` - Fetch pending list with filters
- `api-verify-payment.php` - Mark payment as verified
- `api-send-confirmation.php` - Send single confirmation email
- `api-bulk-verify.php` - Verify multiple payments
- `api-bulk-send-confirmations.php` - Send multiple confirmations

### 4.2 Confirmation Email Template

Includes:
- Registration Number (REG-2026-XXXXXX)
- Participant details (name, email, phone, organization)
- Payment info (transaction ID, amount)
- Submission IDs (if any abstracts/papers/posters submitted)
- Attendance mode
- Next steps information

### 4.3 Email Customization

Edit `api-send-confirmation.php` to customize:
- Email subject line
- Email body HTML
- Logo/branding
- Call-to-action links

---

## Step 5: Data Integration with Google Forms

### 5.1 Option A: Google Forms → Webhook

If using Google Forms for registration:

1. **Set up Google Apps Script webhook**:
   - Go to Forms settings
   - Set up notification email to a specific address
   - Create a Google Apps Script to forward responses to your webhook

2. **Or use third-party service** (Zapier, Make.com):
   - Connect Google Forms to Zapier
   - Create action: When new Google Form response → POST to webhook
   - Map form fields to webhook JSON fields

### 5.2 Option B: Manual CSV Import (Legacy)

If you still have CSV data:
1. Insert data using import script (can be created if needed)
2. Or manually upload via admin dashboard extension

### 5.3 Google Forms Fields Recommended

Your Google Form should collect:
```
- Full Name (First Name + Last Name)
- Email Address
- Phone Number
- Organization/Institution
- Category (Student/Faculty/Professional)
- Participation Type (Paper/Poster/Abstract)
- Attendance Mode (Online/In-person/Hybrid)
- Transaction ID (after payment)
- Amount Paid (auto-filled from payment)
- Any other submission IDs (ABS-, PAPER-, POST- IDs)
```

---

## Step 6: File Structure

```
conference-site/
├── db-config.php                          # Database connection config
├── db-init.php                            # Database initialization
├── webhook-payment-receiver.php           # Receives payment data
├── admin-dashboard.html                   # Admin interface
├── api-get-dashboard-stats.php           # API: Get stats
├── api-get-pending-participants.php      # API: Get participants list
├── api-verify-payment.php                # API: Verify single payment
├── api-send-confirmation.php             # API: Send single confirmation
├── api-bulk-verify.php                   # API: Bulk verify
├── api-bulk-send-confirmations.php       # API: Bulk send confirmations
├── SETUP_GUIDE.md                        # This file
└── [existing files...]
```

---

## Step 7: Security Considerations

### 7.1 Webhook Security
- Add secret key validation to `webhook-payment-receiver.php`
- Verify payment gateway signature before processing
- Rate limit webhook endpoint
- Log all incoming webhooks for audit trail

### 7.2 Admin Dashboard Security
- Add authentication layer (username/password)
- Use HTTPS only for admin dashboard
- Log all actions (verify, send, resend)
- Implement IP whitelisting for admin access

### 7.3 Database Security
- Use strong passwords for database user
- Regular backups of database
- Never commit `db-config.php` to version control
- Use prepared statements (already implemented)

---

## Step 8: Testing Workflow

### 8.1 Test Payment Webhook
```bash
curl -X POST http://yourserver.com/conference-site/webhook-payment-receiver.php \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Test",
    "last_name": "Student",
    "email": "test@example.com",
    "phone": "9876543210",
    "organization": "Test Org",
    "category": "Student",
    "participation_type": "Paper",
    "transaction_id": "TEST123",
    "amount_paid": 500,
    "payment_method": "Online",
    "attendance_mode": "In-person"
  }'
```

Expected response:
```json
{
  "status": "success",
  "message": "Payment data received and processed",
  "participant_id": "PART-2026-XXXXXX",
  "transaction_id": "TEST123"
}
```

### 8.2 Test Admin Dashboard
1. Visit `admin-dashboard.html`
2. Check if test participant appears in pending list
3. Click "Verify" and verify database updates
4. Click "Send Confirmation" and check email received

### 8.3 Test Bulk Operations
1. Create 3-5 test entries via webhook
2. Select all in dashboard
3. Click "Verify Selected"
4. Click "Send Confirmations"
5. Verify all emails received

---

## Step 9: Monitoring & Troubleshooting

### 9.1 Check Email Logs
View `email_logs` table to see:
- All emails sent (confirmation_email)
- Webhook events (payment_webhook_received)
- Errors (payment_webhook_error)

### 9.2 Database Queries for Verification

Check total payments:
```sql
SELECT COUNT(*) FROM payments WHERE payment_status = 'verified';
```

Check pending confirmations:
```sql
SELECT * FROM confirmations WHERE confirmation_sent = 0;
```

Check email delivery:
```sql
SELECT * FROM email_logs WHERE email_type = 'confirmation_email' ORDER BY sent_at DESC;
```

### 9.3 Common Issues

**Webhook not receiving data**:
- Check payment gateway webhook settings
- Verify endpoint URL is correct
- Check server logs for errors
- Ensure PHP error logging is enabled

**Emails not sending**:
- Check mail server configuration (php.ini)
- Verify email address is valid
- Check email_logs table for failures
- Ensure SMTP is configured if needed

**Dashboard not loading**:
- Check database connection (db-config.php)
- Run db-init.php again
- Check PHP error logs
- Verify API endpoints are accessible

---

## Step 10: Production Deployment

### 10.1 Checklist
- [ ] Database backed up
- [ ] db-config.php updated with production credentials
- [ ] Webhook URL updated in payment gateway
- [ ] SSL/HTTPS enabled for admin dashboard
- [ ] Admin authentication implemented
- [ ] Email configuration tested with production SMTP
- [ ] Database user has minimal required permissions
- [ ] Error logging enabled for debugging
- [ ] Backup schedule configured
- [ ] Admin trained on dashboard workflow

### 10.2 Going Live
1. Update webhook URL in payment gateway to production server
2. Test with small payment (₹1) to verify flow
3. Monitor email_logs and email delivery
4. Train admin staff on using dashboard
5. Provide admin dashboard URL and credentials to team

---

## Support & Questions

For issues or questions about the setup:
1. Check email_logs table for error details
2. Review PHP error logs
3. Test webhook with curl command
4. Verify database connection
5. Contact your hosting provider for PHP/MySQL issues

---

**System Ready for Production!** ✅

Your NC-SDESS 2026 conference registration system is now automated. Student payments automatically populate the database, and professors simply verify and send confirmations with a single click.
