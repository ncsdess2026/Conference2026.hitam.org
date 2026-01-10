# Google Form Setup Instructions for NC-SDESS 2026 Registration

## Overview
This document provides step-by-step instructions to set up the Google Form for conference registration with payment tracking and discount eligibility.

## Required Form Fields

### Section 1: Basic Information
1. **Full Name** (Short answer, Required)
2. **Email Address** (Short answer, Email validation, Required)
3. **Phone Number** (Short answer, Required)
4. **Organization/College** (Dropdown, Required)
   - HITAM
   - Other Academic / Research Institution
   - Industry
   - Other

5. **Department** (Short answer, Required)

### Section 2: Participation Details
6. **Participant Category** (Dropdown, Required)
   - Student (UG/PG/PhD)
   - Faculty
   - Professional
   - Accompanying Person / Listener

7. **Conference Track** (Dropdown, Required)
   - Track 1: Sustainable Energy Solutions & Clean Power Technologies
   - Track 2: Smart Electronics & Sensor-Based Solutions
   - Track 3: Software Systems & Cyber Solutions
   - Track 4: AI & Data-Driven Solutions
   - Track 5: Cyber-Physical Systems & Intelligent Automation
   - Track 6: Sustainable Materials & Applied Physics
   - Track 7: Integrated Engineering Solutions (Interdisciplinary)

8. **Participation Mode** (Multiple choice, Required)
   - Paper Presenter (Offline)
   - Paper Presenter (Online)
   - Poster Presenter
   - Attendee/Listener Only

9. **Paper Type** (Multiple choice, Required if presenter)
   - Full Paper (8-10 pages)
   - Half Paper (Extended Abstract 4-6 pages)
   - Not Applicable (Attendee/Listener only)

### Section 3: Discount Eligibility
10. **Registration Date** (Date, Required)
    - Note: This will be auto-filled, but users can modify if needed

11. **Technical Chapter Membership** (Checkboxes)
    - ISAMPE Member
    - IEOM Member
    - IEEE Member
    - ISNT Member
    - None of the above

### Section 4: Fee Calculation (Information Only)
12. **Calculated Fee Information** (Section description)
    Add this text in the section description:
    ```
    Before completing this form:
    1. Visit https://[your-domain]/fee-calculator.html to calculate your exact fee
    2. Make payment to the provided bank account
    3. Take a screenshot or save your payment receipt
    4. Upload the receipt in the next question
    
    PAYMENT DETAILS:
    Account Number: 0547053000005960
    Account Name: IEEE Account, Hyderabad Institute of Technology and Management
    IFSC Code: SIBL0000547
    Branch: Quthbullapur
    
    DISCOUNT INFORMATION:
    - Early Bird Discount (10%): Register on or before January 15, 2026
    - Membership Discount (10%): Valid membership in ISAMPE, IEOM, IEEE, or ISNT
    - Both discounts can be combined (20% total)
    ```

### Section 5: Payment Details
13. **Amount Paid** (Short answer, Number validation, Required)
    - Add description: "Enter the exact amount you paid in INR"

14. **Transaction ID / Reference Number** (Short answer, Required)
    - Add description: "Enter the UTR/Transaction ID from your bank transfer"

15. **Payment Date** (Date, Required)

16. **Payment Receipt/Screenshot** (File upload, Required)
    - Accept: Images (JPG, PNG, PDF)
    - Max file size: 10 MB
    - Add description: "Upload your payment receipt or transaction screenshot"

### Section 6: Abstract Submission (For Presenters)
17. **Abstract Title** (Short answer, Required if presenter)

18. **Abstract File** (File upload, Required if presenter)
    - Accept: PDF, DOC, DOCX
    - Max file size: 5 MB

19. **Additional Comments** (Long answer, Optional)

### Section 7: Confirmation
20. **Declaration** (Checkbox, Required)
    - "I confirm that all information provided is accurate and I have made the payment as indicated above"

## Form Settings

### General Settings
- Collect email addresses: **Enabled**
- Limit to 1 response per email: **Enabled**
- Allow response editing after submit: **Disabled**
- Show progress bar: **Enabled**

### Confirmation Message
```
Thank you for registering for NC-SDESS 2026!

Your registration has been received. Our team will verify your payment details within 24-48 hours and send you a confirmation email.

IMPORTANT: Please save a copy of your responses for your records.

If you have any questions, contact us at:
Email: ncsdess2026@hitam.org

We look forward to seeing you at the conference!

- NC-SDESS 2026 Organizing Committee
```

## Setting Up Conditional Logic (If using Google Forms Add-ons)

Since Google Forms doesn't natively support complex calculations, we recommend:

1. **Use Form Publisher Add-on** - To generate automated confirmation emails with payment details
2. **Use Form Limiter Add-on** - To close registration after deadline
3. **Use formRanger Add-on** - To dynamically update dropdowns if needed

## Alternative: Google Sheets Integration for Verification

After form submissions:
1. Link form responses to Google Sheets
2. Create a verification column to track:
   - Payment verification status
   - Discount eligibility check
   - Final amount validation
3. Use conditional formatting to highlight discrepancies

## Verification Formula for Google Sheets

Add this formula in a new column to calculate expected fee:

```
=IF(AND(B2="Student (UG/PG/PhD)", C2="HITAM", D2="Offline Paper"), 600,
  IF(AND(B2="Student (UG/PG/PhD)", C2="HITAM", D2="Online Paper"), 500,
  IF(AND(B2="Student (UG/PG/PhD)", C2="HITAM", D2="Poster"), 500,
  IF(AND(B2="Student (UG/PG/PhD)", C2<>"HITAM", D2="Offline Paper"), 800,
  IF(AND(B2="Student (UG/PG/PhD)", C2<>"HITAM", D2="Online Paper"), 700,
  IF(AND(B2="Student (UG/PG/PhD)", C2<>"HITAM", D2="Poster"), 600,
  IF(AND(B2="Faculty", C2="HITAM", D2="Offline Paper"), 1000,
  IF(AND(B2="Faculty", C2="HITAM", D2="Online Paper"), 900,
  IF(AND(B2="Faculty", C2="HITAM", D2="Poster"), 700,
  IF(AND(B2="Faculty", C2<>"HITAM", D2="Offline Paper"), 1400,
  IF(AND(B2="Faculty", C2<>"HITAM", D2="Online Paper"), 1100,
  IF(AND(B2="Faculty", C2<>"HITAM", D2="Poster"), 1000,
  IF(AND(B2="Professional", D2="Offline Paper"), 1700,
  IF(AND(B2="Professional", D2="Online Paper"), 1400,
  IF(AND(B2="Professional", D2="Poster"), 1200,
  IF(B2="Accompanying Person / Listener", 300, 0))))))))))))))))
```

Then apply discount formula:
```
=Expected_Fee * (1 - IF(Registration_Date <= DATE(2026,1,15), 0.1, 0) - IF(Has_Membership=TRUE, 0.1, 0))
```

## Communication Template

### Pre-Registration Email
Subject: Register for NC-SDESS 2026 - Important Instructions

Dear Participant,

Thank you for your interest in NC-SDESS 2026!

To complete your registration:

1. **Calculate Your Fee**: Visit https://[your-domain]/fee-calculator.html
2. **Make Payment**: Use the bank details provided
3. **Submit Form**: Complete the registration form with payment proof

Important Discounts:
- 10% Early Bird (Register by Jan 15, 2026)
- 10% Membership Discount (ISAMPE/IEOM/IEEE/ISNT)

Registration Link: [Your Google Form Link]

Best regards,
NC-SDESS 2026 Team

### Post-Registration Confirmation
Subject: Registration Received - NC-SDESS 2026

Dear [Name],

Your registration for NC-SDESS 2026 has been received!

Registration Details:
- Name: [Name]
- Category: [Category]
- Amount Paid: ₹[Amount]
- Transaction ID: [ID]

Our team will verify your payment within 24-48 hours. You will receive a confirmation email once verified.

If you have questions, contact: ncsdess2026@hitam.org

Best regards,
NC-SDESS 2026 Organizing Committee

## Support Contact
For technical issues with the form:
- Email: ncsdess2026@hitam.org
- Phone: [Add phone number]

---
Document Version: 1.0
Last Updated: January 6, 2026
