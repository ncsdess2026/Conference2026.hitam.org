# NATIONAL CONFERENCE OF EMERGING AND SUSTAINABLE TECHNOLOGIES (HITAM)

A responsive static website for the conference organized by the Department of Mechanical Engineering, HITAM in association with IEOM & ISAMPE.

## Quick Start (Windows)

- Option 1: Open directly
  - Double-click `conference-site/index.html` to open in your browser.

- Option 2: Serve locally (if Python is installed)
  ```powershell
  cd "c:\Users\user\OneDrive\Downloads\Desktop\TECH FEST\conference-site"
  python -m http.server 5500
  ```
  Open http://localhost:5500 in your browser.

## Update Checklist

- Event Date: Update in `assets/js/main.js` (target date) and in `index.html` hero meta line.
- Google Form Link: Replace the placeholder link in `index.html` (Registration section).
- Important Dates: Fill table in `index.html` under "Important Dates" section.
- Fees: Update the fees table in `index.html`.
- Committee: Replace all TBD entries in `index.html`.
- Tracks & Emails: Track buttons are in [conference-site/index.html](conference-site/index.html#L1) (`#tracks`). Update the email addresses in the mapping inside [conference-site/assets/js/main.js](conference-site/assets/js/main.js#L1) (`tracks` object). Each track has two placeholder emails; replace with the official ones.
- Call for Abstracts: Edit text and submission link in [conference-site/index.html](conference-site/index.html#L1) under `#call-for-abstracts`.
- Call for Posters: Edit text and submission link in [conference-site/index.html](conference-site/index.html#L1) under `#call-for-posters`.
- Map Embed: Update the Google Maps iframe `src` in `index.html` with correct embed URL.
- Downloads: Replace `assets/docs/paper-template.docx` and `assets/docs/brochure.pdf` with the official files.
- Logos: Replace the placeholder `assets/img/hitam-logo.svg` with the official HITAM logo (same filename), and optionally add `assets/img/ieom.png`, `assets/img/isampe.png` to reference in the footer.- Hyderabad Backgrounds: Add `assets/img/hyderabad-hero.jpg` (for hero section) and `assets/img/hyderabad-city.jpg` (for about-conference section). See `assets/img/HYDERABAD-IMAGES-GUIDE.txt` for details.- College Gallery: Replace `assets/img/placeholder-campus.svg` references in the `#gallery` section with actual campus photos (PNG/JPG/SVG) stored in `assets/img/`.
- Speakers & Management: Replace `assets/img/placeholder-person.svg` in the `#speakers` section with photos; update names, roles, and affiliations in `index.html`.

## Structure

- `index.html`: All sections in a single-page layout with fixed navigation.
- `assets/css/styles.css`: Visual styles and responsive layouts.
- `assets/js/main.js`: Countdown, mobile nav, smooth interactions, modals.
- `assets/docs/`: Placeholders for template and brochure downloads.
- `assets/img/`: Add campus and logo images here.

## Features

- Fixed navigation bar
- Hero banner with CTA and countdown timer
- Smooth scrolling and responsive design
- Modal pop-ups for downloads
- Tracks/cards layout and clean tables
- Gallery placeholders ready for images

## Notes

- This site uses no frameworks; it is pure HTML/CSS/JS for portability.
- If you need separate pages (e.g., a dedicated registration page), we can split sections into additional HTML files and wire the navigation accordingly.
