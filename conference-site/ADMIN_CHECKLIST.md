# Administrator Checklist - Abstract & Poster Submission System

## Pre-Launch Setup (Do This First)

### Configuration
- [ ] Open `process-abstract.php`
  - [ ] Line 6: Update `$CONFERENCE_EMAIL = "your-email@hitam.org"`
  - [ ] Verify track names match your conference
  - [ ] Review email templates for accuracy

- [ ] Open `process-poster.php`
  - [ ] Line 6: Update `$CONFERENCE_EMAIL = "your-email@hitam.org"`
  - [ ] Verify track names match your conference
  - [ ] Review email templates for accuracy

### Directory Setup
- [ ] Create `submissions/` directory (auto-created on first submission)
  - [ ] Test: `mkdir -p submissions && chmod 755 submissions`
  
- [ ] Create `uploads/posters/` directory
  - [ ] Test: `mkdir -p uploads/posters && chmod 755 uploads/posters`

### Testing Email System
- [ ] Test with your personal email:
  1. Go to website
  2. Click "Call for Abstract"
  3. Fill form with test data
  4. Submit
  5. Check inbox for 2 emails (Thank You + Acceptance)

- [ ] Test with colleague's email:
  1. Repeat above with different email
  2. Verify emails arrive properly
  3. Check all details are correct

### Website Testing
- [ ] Navigation menu shows both new links
  - [ ] "Call for Abstract" link works
  - [ ] "Call for Posters" link works

- [ ] Forms display correctly
  - [ ] All fields visible
  - [ ] Buttons clickable
  - [ ] Mobile responsive

- [ ] Form functionality
  - [ ] Word counter works
  - [ ] File upload input appears
  - [ ] Submit buttons work
  - [ ] Error messages display

### Email Verification
- [ ] Thank You email arrives
  - [ ] Contains submission ID
  - [ ] Shows all participant details
  - [ ] Properly formatted HTML

- [ ] Acceptance email arrives
  - [ ] Shows acceptance message
  - [ ] Lists next steps
  - [ ] Includes conference details

- [ ] Admin notification arrives
  - [ ] Sent to conference email
  - [ ] Contains all submission data
  - [ ] Useful for tracking

### Data Storage Verification
- [ ] Files saved in submissions/
  - [ ] Check: `ls submissions/`
  - [ ] Files are JSON format
  - [ ] Contains all form data

- [ ] Poster files saved correctly
  - [ ] Check: `ls uploads/posters/`
  - [ ] Files have correct names
  - [ ] Can open/view the files

---

## During Submission Period

### Daily Tasks
- [ ] Check admin notification emails
  - [ ] Open email from ncsdess2026@hitam.org
  - [ ] Review new submissions
  - [ ] Note any issues

- [ ] Monitor submissions directory
  - [ ] Count new files: `ls submissions/ | wc -l`
  - [ ] Check latest submission: `ls -lt submissions/ | head`

- [ ] Respond to participant queries
  - [ ] Check support email
  - [ ] Reply within 24 hours
  - [ ] Use submission ID for reference

- [ ] Back up submissions
  - [ ] Daily: Copy submissions/ folder
  - [ ] Store in safe location
  - [ ] Verify copies are readable

### Weekly Tasks
- [ ] Generate submission report
  - [ ] Count total submissions
  - [ ] Count by track
  - [ ] Count paper vs poster
  - [ ] Calculate acceptance rate

- [ ] Check email delivery
  - [ ] Verify no delivery failures
  - [ ] Check spam folder trends
  - [ ] Monitor bounce rate

- [ ] System health check
  - [ ] Check server disk space: `df -h`
  - [ ] Verify directories still exist
  - [ ] Check file permissions: `ls -l submissions/`

- [ ] Database integrity (if used)
  - [ ] Run backup: `mysqldump ...`
  - [ ] Verify backup completeness
  - [ ] Store off-site

---

## Before Announcement to Participants

### Messaging
- [ ] Email content finalized
- [ ] Track names confirmed
- [ ] Important dates set
- [ ] Next steps clearly documented

### Support Preparation
- [ ] FAQ document created
- [ ] Support email monitored
- [ ] Response template prepared
- [ ] Escalation process defined

### Communication Channels
- [ ] Website accessible
- [ ] Both forms publicly visible
- [ ] Links working correctly
- [ ] Instructions clear

### Backup Planning
- [ ] Backup system in place
- [ ] Backup location secured
- [ ] Restore procedure tested
- [ ] Team trained on backups

---

## Announcement & Go-Live

### Before Publishing Links
- [ ] Website tested one final time
- [ ] Email system verified working
- [ ] All forms functional
- [ ] Directories created and writable

### Communication
- [ ] Send announcement email to potential participants
- [ ] Update social media
- [ ] Share registration page link
- [ ] Share abstract submission link
- [ ] Include important dates

### Monitoring
- [ ] Monitor first submissions closely
- [ ] Be ready for immediate issues
- [ ] Have support team on standby
- [ ] Track submission rate

---

## During Submission Window

### Real-Time Monitoring
- [ ] Check submissions multiple times daily
- [ ] Monitor submission rate
  - [ ] Slow? Send reminder emails
  - [ ] Fast? Ensure system can handle volume

- [ ] Watch for spam or invalid submissions
- [ ] Verify email delivery continues
- [ ] Address any technical issues immediately

### Communication
- [ ] Send reminder emails at milestones:
  - [ ] 1 week before deadline
  - [ ] 3 days before deadline
  - [ ] 24 hours before deadline

- [ ] Respond promptly to all queries
- [ ] Provide submission ID when requested
- [ ] Help troubleshoot submission issues

### Quality Assurance
- [ ] Spot-check submissions for quality
- [ ] Verify files are readable
- [ ] Check for spam or inappropriate content
- [ ] Document any suspicious submissions

### Performance Monitoring
- [ ] Track website performance
- [ ] Monitor server resources
- [ ] Check error logs: `tail -f /var/log/php-errors.log`
- [ ] Address slowdowns quickly

---

## As Deadline Approaches

### Final Push
- [ ] Increase monitoring frequency
- [ ] Send urgent reminder emails
- [ ] Have support team available
- [ ] Be ready for last-minute submissions

### Deadline Day
- [ ] Monitor submissions until exact deadline
- [ ] Note any late submissions
- [ ] Document deadline compliance
- [ ] Send confirmation that deadline has passed

### Post-Deadline
- [ ] Stop accepting submissions
- [ ] Disable forms or show "closed" message
- [ ] Announce closure to participants
- [ ] Begin review process

---

## After Submission Closes

### Data Management
- [ ] Final backup of all submissions
  - [ ] Copy submissions/ folder
  - [ ] Copy uploads/posters/ folder
  - [ ] Store in multiple locations
  - [ ] Document backup date

- [ ] Verify file integrity
  - [ ] Count total files
  - [ ] Sample-check file readability
  - [ ] Verify no corrupted files

- [ ] Archive submissions
  - [ ] Move to archive location
  - [ ] Maintain readable format
  - [ ] Document archive contents

### Reporting
- [ ] Generate statistics
  - [ ] Total submissions
  - [ ] Breakdown by track
  - [ ] Paper vs poster split
  - [ ] Participant statistics

- [ ] Create summary report
- [ ] Document any issues
- [ ] Provide to review committee
- [ ] Share with leadership

### Review Process
- [ ] Distribute submissions to track chairs
- [ ] Set review timeline
- [ ] Monitor review progress
- [ ] Collect acceptance decisions

---

## Technical Maintenance

### Regular Tasks
- [ ] Check disk space (weekly)
  ```bash
  df -h
  du -sh submissions/ uploads/
  ```

- [ ] Verify permissions (weekly)
  ```bash
  ls -l submissions/
  ls -l uploads/posters/
  ```

- [ ] Check error logs (daily)
  ```bash
  tail -50 /var/log/php-errors.log
  ```

- [ ] Monitor email logs (daily)
  ```bash
  tail -50 /var/log/mail.log
  ```

### Troubleshooting Commands
- [ ] Check if PHP is running: `php -v`
- [ ] Test email: `echo "Test" | mail -s "Test" your-email@hitam.org`
- [ ] Check directory permissions: `stat submissions/`
- [ ] List recent submissions: `ls -ltr submissions/ | tail -10`

---

## Security Checklist

### Access Control
- [ ] Restrict submissions folder access
  ```bash
  chmod 750 submissions/
  chmod 750 uploads/
  ```

- [ ] Regular password changes
- [ ] Limited admin access
- [ ] Audit trail maintained

### Data Protection
- [ ] HTTPS enabled on website
- [ ] Regular backups verified
- [ ] Off-site backup copies maintained
- [ ] Encrypted storage for sensitive data

### Monitoring
- [ ] Watch for suspicious patterns
- [ ] Monitor for spam submissions
- [ ] Track access logs
- [ ] Alert on errors

---

## Escalation Procedures

### Issue: Forms Not Working
1. Check error logs
2. Verify PHP files exist and permissions correct
3. Test with fresh form submission
4. Check browser console (F12) for errors
5. Contact hosting support if needed

### Issue: Emails Not Sending
1. Verify mail server running
2. Check email configuration in PHP files
3. Test with simple email script
4. Check mail logs for errors
5. Use alternative email service if needed

### Issue: File Upload Failing
1. Check uploads/ directory exists and writable
2. Verify file size under 10 MB
3. Check file type is allowed
4. Check server disk space
5. Review PHP upload settings

### Issue: High Submission Volume
1. Monitor server resources
2. Check for spam pattern
3. Consider closing submissions early if over capacity
4. Implement rate limiting if needed
5. Scale server if necessary

---

## Post-Conference

### Archive & Cleanup
- [ ] Archive all submissions securely
- [ ] Maintain backup copies indefinitely
- [ ] Consider deleting after retention period
- [ ] Document retention policy

### Lessons Learned
- [ ] Collect feedback from team
- [ ] Document what worked well
- [ ] Note areas for improvement
- [ ] Plan enhancements for next year

### Documentation Update
- [ ] Update setup guide with real experience
- [ ] Document any customizations made
- [ ] Record email server configuration
- [ ] Save system configuration

### Improvement Planning
- [ ] Database integration
- [ ] API connections
- [ ] Review workflow automation
- [ ] Enhanced reporting

---

## Emergency Contacts

### System Support
- Server Admin: ________________
- Email Support: ________________
- Technical Contact: ________________

### Conference Team
- Conference Coordinator: ________________
- Track Chair: ________________
- Registration Manager: ________________

---

## Important Dates to Remember

- [ ] Submission Deadline: January 10, 2026
- [ ] Notification Deadline: January 15, 2026
- [ ] Camera-Ready Deadline: January 20, 2026
- [ ] Registration Deadline: January 22, 2026
- [ ] Conference Dates: January 28-29, 2026

---

## Resources Available

### Documentation Files
1. **QUICK_START.md** - 5-minute setup
2. **ABSTRACT_POSTER_SETUP.md** - Comprehensive guide
3. **EMAIL_NOTIFICATIONS.md** - Email customization
4. **IMPLEMENTATION_SUMMARY.md** - Features overview
5. **This Checklist** - Administrator tasks

### Key Files
- `process-abstract.php` - Abstract handling
- `process-poster.php` - Poster handling
- `submissions/` - Storage directory
- `uploads/posters/` - Poster files

---

## Sign-Off

**System Prepared By:** ________________  Date: __________
**System Tested By:** ________________  Date: __________
**System Approved By:** ________________  Date: __________

**Go-Live Date:** January 10, 2026
**Expected Peak:** January 8-10, 2026
**Contingency Plan:** Have backup email system ready

---

## Notes

```
[Space for additional notes and observations]


```

---

**Revised:** January 7, 2026
**Status:** Ready for Production
**Last Review:** January 7, 2026

**Questions?** Contact: ncsdess2026@hitam.org
