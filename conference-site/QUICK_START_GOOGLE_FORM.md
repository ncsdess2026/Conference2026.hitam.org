# Quick Start: Google Form Setup (5-Minute Guide)

## For the Impatient 🚀

### What Changed?
✅ Website now shows "Submit Abstract" buttons
✅ Clicking them opens a Google Form
✅ Need: Your Google Form link

---

## Step 1: Create Google Form (3 minutes)

1. Go to **https://forms.google.com**
2. Click **"+"** → **"Blank Form"**
3. Title: `NC-SDESS 2026 - Abstract Submission`
4. Add these 11 fields (use guide: `GOOGLE_FORM_CREATION_GUIDE.md`):
   - Full Name (text)
   - Email (email)
   - Phone (text)
   - Institution (text)
   - Track (dropdown - 7 options)
   - Title (text)
   - Co-Authors (optional paragraph)
   - Keywords (text)
   - Abstract (paragraph)
   - Type (dropdown - Paper/Poster)
   - Agreement (checkbox)

---

## Step 2: Get Your Form Link (1 minute)

1. Click **"Send"** button in Google Form
2. Click **"<>"** (Embed tab)
3. Copy the `src="https://..."` URL
4. Example format:
   ```
   https://docs.google.com/forms/d/e/1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg/viewform
   ```

---

## Step 3: Update Website (1 minute)

Edit file: `abstract-form.html`

Find (around line 168):
```html
src="https://docs.google.com/forms/d/e/REPLACE_WITH_YOUR_FORM_ID/viewform?embedded=true"
```

Replace `REPLACE_WITH_YOUR_FORM_ID` with your actual form ID from your URL.

**Example**:
- Your URL: `https://docs.google.com/forms/d/e/1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg/viewform`
- Form ID: `1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg`
- Result: `src="https://docs.google.com/forms/d/e/1FAIpQLScfJlvdiSgFEjBqBqyqj2U2KsXLqMVElZw2z25L24GUpDviLg/viewform?embedded=true"`

Also update the direct link around line 176 (same URL, no `?embedded=true`).

---

## Step 4: Test (1 minute)

1. Open your website
2. Click "Submit Abstract" button
3. Form should load or link should work
4. Submit a test response
5. Check Google Forms "Responses" tab - should see your test entry

✅ Done!

---

## Common Issues

| Problem | Solution |
|---------|----------|
| Form doesn't load | Check if you replaced REPLACE_WITH_YOUR_FORM_ID correctly |
| "Link not found" | Double-check the form ID matches your Google Form URL |
| No responses showing | Submit a test response, check Google Forms Responses tab |
| Form still shows "REPLACE_WITH" text | Make sure you saved the abstract-form.html file |

---

## Where It Shows Up

✅ Hero Section - "Submit Abstract" button
✅ Call for Abstract Section - Both buttons
✅ Poster Section - "Go to Abstract Form" link
✅ Dedicated Page - `abstract-form.html`

---

## Files You Need to Know

| File | Purpose |
|------|---------|
| `abstract-form.html` | PAGE - Form embedding page (update this) |
| `index.html` | Already updated - buttons added |
| `GOOGLE_FORM_CREATION_GUIDE.md` | GUIDE - Detailed form setup |
| `GOOGLE_FORM_INTEGRATION_GUIDE.md` | GUIDE - Full integration doc |

---

## That's It! 🎉

Your abstract submission form is now live on the website!

Users can now:
1. Click "Submit Abstract"
2. Fill Google Form
3. Get confirmation immediately
4. You see responses in Google Forms

---

**Need more details?** See `GOOGLE_FORM_CREATION_GUIDE.md` for complete field configurations.

**Need help managing responses?** See `ADMIN_PANEL_SETUP.md` to set up the admin dashboard.

