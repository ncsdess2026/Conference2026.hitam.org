# Abstract Submission Integration Guide

## Overview
Your conference website now has Google Form integration for abstract submissions. This guide explains how to set up your Google Form and integrate it with your website.

## What Has Been Done

### 1. Website Updates
✅ Updated `index.html` - Replaced inline form with Google Form links
✅ Created `abstract-form.html` - Dedicated page for form embedding
✅ Created `GOOGLE_FORM_CREATION_GUIDE.md` - Step-by-step form creation instructions

### 2. User Experience
When users click "Submit Abstract", they can:
- **Option 1**: Click "📝 Open Abstract Form" → Opens `abstract-form.html` with embedded Google Form
- **Option 2**: Click "🔗 Direct Google Form Link" → Opens Google Form directly in new tab

### 3. Navigation Integration
The "Submit Abstract" button appears in:
- Hero section CTA buttons
- Call for Abstract section
- Poster Submission section (link to abstract form)

## How to Set Up Your Google Form

### Quick Start (5 minutes)

1. Go to **https://forms.google.com**
2. Click **"Create"** → Select **"Blank Form"**
3. Follow the detailed guide in `GOOGLE_FORM_CREATION_GUIDE.md` (all 11 questions listed)
4. Get your form's shareable link
5. Update the `abstract-form.html` file (see below)

### Getting Your Form's Embedded Code

Once your Google Form is created:

1. In Google Forms, click the **"Send"** button (top right)
2. Click the **"<>" Embed** tab
3. You'll see an `<iframe>` code
4. Copy this code

### Integrating with Your Website

**Edit `abstract-form.html`:**

1. Find line 168-179 in `abstract-form.html`
2. Replace this section:
```html
<iframe 
    src="https://docs.google.com/forms/d/e/REPLACE_WITH_YOUR_FORM_ID/viewform?embedded=true" 
    width="100%" 
    height="1200" 
    frameborder="0" 
    marginheight="0" 
    marginwidth="0"
    title="NC-SDESS 2026 Abstract Submission Form">
    Loading...
</iframe>
```

With your actual Google Form's iframe code (from step 3 above).

3. Also update the direct link in the same file:
   - Find: `https://docs.google.com/forms/d/e/REPLACE_WITH_YOUR_FORM_ID/viewform`
   - Replace with your actual form's shareable link

4. Save the file and test!

## Form Fields Configuration

Your Google Form should include these 11 fields (in this order):

| # | Field Name | Type | Required | Notes |
|---|-----------|------|----------|-------|
| 1 | Full Name | Short answer | Yes | Basic name input |
| 2 | Email Address | Short answer | Yes | Validate email format |
| 3 | Phone Number | Short answer | Yes | Indian phone format |
| 4 | Organization/Institution | Short answer | Yes | College/Company name |
| 5 | Select Track | Dropdown | Yes | 7 tracks available |
| 6 | Paper/Poster Title | Short answer | Yes | Work title |
| 7 | Co-Authors (if any) | Paragraph | No | Optional |
| 8 | Keywords (3-5) | Short answer | Yes | Comma-separated |
| 9 | Abstract Content | Paragraph | Yes | 250-300 words |
| 10 | Submission Type | Dropdown | Yes | Paper or Poster |
| 11 | Agreement | Checkbox | Yes | Must check to submit |

**See `GOOGLE_FORM_CREATION_GUIDE.md` for complete field configurations.**

## Testing Your Integration

1. **Test the embedded form**:
   - Go to `abstract-form.html` in your browser
   - Scroll down and verify the form loads
   - Fill out and submit a test response

2. **Test the direct link**:
   - Click the "Direct Google Form Link" button
   - Verify it opens the form in a new tab

3. **Verify responses are collected**:
   - Check your Google Form's "Responses" tab
   - Should see your test submission

## Collecting Responses Securely

### Option A: Google Sheets (Recommended)

1. In Google Forms, click **"Responses"** tab
2. Click the **"📊 Create spreadsheet"** icon
3. Google will create a linked Google Sheet
4. All responses automatically appear in the sheet
5. You can then use the admin panel to manage acceptances

### Option B: Email Notifications

1. In Google Forms, click the **gear icon** (⚙️) → Settings
2. Enable **"Email notifications for every response"**
3. Add your admin email
4. You'll receive an email for each submission

### Option C: Both (Best Practice)

Enable both Google Sheets AND email notifications for redundancy.

## File Structure

```
conference-site/
├── index.html (updated - links to form)
├── abstract-form.html (new - embedding page)
├── GOOGLE_FORM_CREATION_GUIDE.md (new - instructions)
├── GOOGLE_FORM_INTEGRATION_GUIDE.md (new - this file)
└── assets/
    └── img/
        └── hitam logo.png
```

## Linking with Admin Panel

Once you have:
1. ✅ Google Form created
2. ✅ Google Sheet connected to form
3. ✅ Admin panel configured

**Workflow**:
```
User fills Google Form
    ↓
Responses appear in Google Sheet
    ↓
Admin logs into admin-login.html
    ↓
Admin views responses in admin-responses.html
    ↓
Admin clicks Accept/Reject
    ↓
Status updated in Google Sheet
    ↓
User receives confirmation email (optional)
```

## Troubleshooting

### Issue: Form doesn't load on abstract-form.html
**Solution**:
- Check if you replaced `REPLACE_WITH_YOUR_FORM_ID` with actual form ID
- Ensure iframe src URL is correct
- Try opening the direct link to verify form works

### Issue: "How do I get the form ID?"
**Solution**:
- Open your Google Form
- Look at the URL: `https://docs.google.com/forms/d/e/FORM_ID_HERE/viewform`
- Copy the part between `/d/e/` and `/viewform`

### Issue: Responses not appearing in Google Sheet
**Solution**:
- Make sure you created the sheet connection (see "Collecting Responses" section)
- Refresh the Google Sheet page
- Submit a new test response

### Issue: Direct link works but embedded form is blank
**Solution**:
- Check browser console (F12) for errors
- Verify the iframe URL ends with `embedded=true`
- Try clearing browser cache and refreshing

## Customization Options

### Change Form Theme
In Google Forms → Settings → Presentation → Choose a theme

### Add Logo to Form
In Google Forms → Settings → Presentation → Upload image

### Customize Confirmation Message
In Google Forms → Settings → Presentation → "Confirmation message"

### Set Response Limit
In Google Forms → Settings → General → Limit responses to X

### Require Sign-in
In Google Forms → Settings → General → Sign-in required

## Email Notifications Setup

### Automatic Confirmation to Users (Optional)

You can set up Google Forms to send auto-replies:
1. Use Google Apps Script or Zapier
2. Or set up a webhook to your PHP backend
3. See `api-send-confirmation.php` for email template reference

### Admin Notifications

Set Google Forms to email admin for each submission:
1. Forms Settings → Notifications → Email for each response
2. Add your admin email

## Best Practices

1. **Test first** - Always submit a test response before going live
2. **Backup responses** - Regularly download responses as CSV
3. **Monitor closely** - Check admin panel daily during deadline
4. **Clear instructions** - Keep guidelines visible on form page
5. **Mobile-friendly** - Test form on mobile devices
6. **Response limit** - Set a limit if you have capacity constraints

## Links on Website

The following buttons/links now point to the form:

1. **Hero section**: "Submit Abstract" button
2. **Call for Abstract**: "📝 Open Abstract Form" button + "🔗 Direct Google Form Link" button
3. **Poster section**: "Go to Abstract Form" link

## Timeline

| Phase | Status | Notes |
|-------|--------|-------|
| Website Integration | ✅ Complete | Form links added to website |
| Google Form Creation | ⏳ Your Action | Follow guide to create form |
| Embed Code Addition | ⏳ Your Action | Update abstract-form.html |
| Testing | ⏳ Your Action | Test submission process |
| Admin Integration | ⏳ Optional | Connect to admin panel |
| Go Live | ⏳ Your Action | Open submissions to public |

## Support Documents

- 📄 `GOOGLE_FORM_CREATION_GUIDE.md` - How to create the form
- 📄 `GOOGLE_FORM_INTEGRATION_GUIDE.md` - This file
- 📄 `ADMIN_PANEL_SETUP.md` - How to configure admin panel
- 📄 `index.html` - Main website (updated)
- 📄 `abstract-form.html` - Form embedding page (new)

## Next Steps

1. ✅ Read `GOOGLE_FORM_CREATION_GUIDE.md`
2. ✅ Create your Google Form following the guide
3. ✅ Get your form's embed code
4. ✅ Edit `abstract-form.html` with embed code
5. ✅ Test the form submission
6. ✅ Configure Google Sheets collection
7. ✅ Set up admin panel (if using)
8. ✅ Go live!

---

**Created**: January 9, 2026
**For**: NC-SDESS 2026 Conference
**Contact**: For technical support, contact website administrator

