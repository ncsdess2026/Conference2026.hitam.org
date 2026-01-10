# Google Form Setup Guide - Abstract Submission

## Overview
This guide will help you create a Google Form for abstract submissions matching the conference requirements. The form will collect all necessary information for abstract submissions.

## Step-by-Step Instructions to Create the Google Form

### Step 1: Create a New Google Form

1. Go to **https://forms.google.com**
2. Click the **"+" button** (Create) to start a new form
3. Click **"Blank Form"**
4. Set the form title to: **NC-SDESS 2026 - Abstract Submission**
5. Add form description:
   ```
   Please submit your abstract for NC-SDESS 2026 Conference.
   
   Guidelines:
   - Word Limit: 250-300 words
   - Language: English only
   - Include Title, Authors, Keywords (3-5), and Content
   - Must clearly state problem, methodology, and outcomes
   ```

### Step 2: Add Form Questions

Below is the exact configuration for each question. Add them in this order:

#### Question 1: Full Name *
- **Type**: Short answer
- **Question**: Full Name
- **Description**: Enter your complete name
- **Required**: Yes

#### Question 2: Email Address *
- **Type**: Short answer
- **Question**: Corresponding Author Email Address
- **Description**: We'll use this for all communications
- **Required**: Yes
- **Validation**: Email format

#### Question 3: Phone Number *
- **Type**: Short answer
- **Question**: Phone Number
- **Description**: Indian mobile number (e.g., 9876543210 or +91 9876543210)
- **Required**: Yes
- **Validation**: Pattern matching

#### Question 4: Organization/Institution *
- **Type**: Short answer
- **Question**: Organization/College/Institution
- **Description**: Name of your institution or organization
- **Required**: Yes

#### Question 5: Select Track *
- **Type**: Multiple choice (dropdown)
- **Question**: Select Research Track
- **Required**: Yes
- **Options**:
  ```
  Track 1: Sustainable Energy Solutions & Clean Power Technologies
  Track 2: Smart Electronics & Sensor-Based Solutions for Society
  Track 3: Software Systems & Cyber Solutions for Sustainable Development
  Track 4: AI & Data-Driven Solutions for Societal Challenges
  Track 5: Cyber-Physical Systems & Intelligent Automation for Smart Society
  Track 6: Sustainable Materials, Applied Physics & Engineering Innovations
  Track 7: Integrated Engineering Solutions for Sustainable Society
  ```

#### Question 6: Paper/Poster Title *
- **Type**: Short answer
- **Question**: Paper/Poster Title
- **Description**: Enter the title of your work
- **Required**: Yes

#### Question 7: Co-Authors (if any)
- **Type**: Paragraph
- **Question**: Co-Authors (if any)
- **Description**: List co-authors separated by commas (leave blank if no co-authors)
- **Required**: No

#### Question 8: Keywords *
- **Type**: Short answer
- **Question**: Keywords (3-5)
- **Description**: Enter 3-5 keywords separated by commas
- **Required**: Yes

#### Question 9: Abstract Content *
- **Type**: Paragraph
- **Question**: Abstract Content (250-300 words)
- **Description**: Clearly state the problem statement, methodology, and expected outcomes
- **Required**: Yes
- **Validation**: Length between 100-1000 words (approximately)

#### Question 10: Submission Type *
- **Type**: Multiple choice (dropdown)
- **Question**: Submission Type
- **Required**: Yes
- **Options**:
  ```
  Paper Presentation
  Poster Presentation
  ```

#### Question 11: Agree to Terms *
- **Type**: Checkbox
- **Question**: Agreement
- **Required**: Yes
- **Description**: ☑ I agree that my abstract is original and has not been submitted elsewhere. I also consent to receive emails regarding this submission.

### Step 3: Configure Form Settings

1. Click the **gear icon** (⚙️) at the top right
2. Go to **"General"** tab:
   - ☑ **Show progress bar** - Enable
   - ☑ **Collect email addresses** - Enable (Choose "Verified and shown to respondents")
   - ☑ **Shuffle question order** - Disable

3. Go to **"Presentation"** tab:
   - Choose a **blue or professional theme**
   - Enable **"Show in summary"** option

### Step 4: Configure Notifications

1. Click **"More"** (three dots) → **"Notification settings"**
2. For **form responses**, choose:
   - ☑ **Email notifications for every response**
   - Add your admin email (e.g., ncsdess2026@hitam.org)

### Step 5: Get Your Form Link

1. Click the **"Send"** button at the top right
2. Click the **"Link"** tab
3. Enable the shortened URL
4. **Copy the form link** - you'll need this for the website

**Your Google Form Link will look like:**
```
https://docs.google.com/forms/d/e/1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg/viewform
```

### Step 6: (Optional) Collect Responses in Google Sheet

1. Click **"Responses"** tab in your Google Form
2. Click the **green Google Sheets icon** (📊)
3. Choose **"Create a new spreadsheet"**
4. Google will create a sheet where all responses are automatically stored
5. Save the **Spreadsheet ID** for your admin panel integration

## Integration with Your Website

### Method 1: Direct Link (Simplest)
The form link will be added to your website buttons, and clicking them will open the Google Form in a new window.

### Method 2: Embedded Form (In-Page)
Create a separate page with the Google Form embedded directly:

1. Navigate to your Google Form
2. Click **"Send"** → **"Embed"**
3. Copy the **iframe code**
4. Use the provided `abstract-form.html` page on your website

### Method 3: Modal Popup (Advanced)
The form can open in a modal dialog when users click "Submit Abstract"

## Form Link Placement on Website

The form link will be added to these locations:

1. **Navigation bar** - "Submit Abstract" button
2. **Call for Abstract section** - Main submit button
3. **Hero section** - CTA button
4. **Poster section** - Link to form

## Security & Best Practices

1. **Limit responses**: Go to Settings → "General" → Set a maximum response limit if needed
2. **Restrict responses**: Optional - limit to HITAM email domain or specific emails
3. **Make a backup**: Regularly export responses as CSV from Google Sheets
4. **Test the form**: Submit a test response to ensure email notifications work

## After Receiving Responses

1. **Review in Google Sheet**: All responses appear automatically in your connected sheet
2. **Send confirmations**: Use the admin panel to accept/reject submissions
3. **Export data**: Download responses as CSV/Excel for records
4. **Acknowledge**: Reply to submissions with abstract ID

## Example Workflow

```
1. User fills form → Google Form
2. Auto-saved to Google Sheet
3. Admin receives email notification
4. Admin logs into admin panel
5. Admin accepts/rejects in admin dashboard
6. User receives automated confirmation email
```

## Troubleshooting

### Issue: Form not sending emails
- **Solution**: Check Gmail notifications settings → enable email alerts for form

### Issue: Responses not appearing in sheet
- **Solution**: Make sure you created the sheet connection (step 6 above)

### Issue: Need to edit questions later
- **Solution**: Google Forms allow editing questions even after receiving responses

## Template Summary

| Field | Type | Required | Options |
|-------|------|----------|---------|
| Full Name | Text | Yes | N/A |
| Email | Email | Yes | N/A |
| Phone | Text | Yes | Format validation |
| Organization | Text | Yes | N/A |
| Track | Dropdown | Yes | 7 tracks listed |
| Title | Text | Yes | N/A |
| Co-Authors | Paragraph | No | N/A |
| Keywords | Text | Yes | 3-5 keywords |
| Abstract (250-300 words) | Paragraph | Yes | N/A |
| Submission Type | Dropdown | Yes | Paper/Poster |
| Agreement | Checkbox | Yes | Must agree |

## Next Steps

1. ✅ Create the Google Form following the steps above
2. ✅ Test submit a response
3. ✅ Get the shareable link
4. ✅ Provide the link to website administrator
5. ✅ Website will be updated to link to your form
6. ✅ Users can now submit abstracts via Google Form

---

**Created**: January 9, 2026
**For**: NC-SDESS 2026 Conference
**Contact**: For questions, contact the admin panel

