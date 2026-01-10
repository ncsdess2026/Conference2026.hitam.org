// Smooth scroll handled via CSS scroll-behavior

// Mobile menu toggle
(function() {
  const toggle = document.getElementById('menu-toggle');
  const list = document.querySelector('.nav-list');
  if (toggle && list) {
    toggle.addEventListener('click', () => list.classList.toggle('open'));
    list.querySelectorAll('a').forEach(a => a.addEventListener('click', () => list.classList.remove('open')));
  }

  // Dropdown toggle for mobile
  const dropdowns = document.querySelectorAll('.nav .dropdown');
  dropdowns.forEach(dropdown => {
    const toggleLink = dropdown.querySelector('.dropdown-toggle');
    if (toggleLink) {
      toggleLink.addEventListener('click', (e) => {
        if (window.innerWidth <= 640) {
          e.preventDefault();
          dropdown.classList.toggle('open');
        }
      });
    }
  });
})();

// Countdown timer to Jan 28, 2026 09:00 IST
(function() {
  const target = new Date('2026-01-28T09:00:00+05:30').getTime();
  const elDays = document.getElementById('cd-days');
  const elHours = document.getElementById('cd-hours');
  const elMins = document.getElementById('cd-mins');
  const elSecs = document.getElementById('cd-secs');

  function update() {
    const now = Date.now();
    let diff = Math.max(0, target - now);
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    diff -= days * (1000 * 60 * 60 * 24);
    const hours = Math.floor(diff / (1000 * 60 * 60));
    diff -= hours * (1000 * 60 * 60);
    const mins = Math.floor(diff / (1000 * 60));
    diff -= mins * (1000 * 60);
    const secs = Math.floor(diff / 1000);

    if (elDays) elDays.textContent = days.toString();
    if (elHours) elHours.textContent = hours.toString();
    if (elMins) elMins.textContent = mins.toString();
    if (elSecs) elSecs.textContent = secs.toString();
  }

  update();
  setInterval(update, 1000);
})();

// Modals (template & brochure)
(function() {
  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
  }
  function closeModal(modal) {
    modal.classList.remove('open');
  }

  document.querySelectorAll('[data-modal]').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn.getAttribute('data-modal')));
  });

  document.querySelectorAll('.modal').forEach(modal => {
    const closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) closeBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(modal); });
  });
})();

// (Legacy modal for track emails was removed; using inline in-charge buttons instead)

// Track Selection with In-charge Gmail buttons
document.addEventListener('DOMContentLoaded', function() {
  function gmailComposeUrl(to, subject, body = '', bcc = '') {
    const params = new URLSearchParams({ view: 'cm', fs: '1', to, su: subject, body });
    if (bcc) params.set('bcc', bcc);
    return `https://mail.google.com/mail/?${params.toString()}`;
  }

  const trackData = {
    1: {
      name: 'Track 1: Sustainable Energy Solutions & Clean Power Technologies (MECH + EEE)',
      subject: 'NC-SDESS Track 1 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. O. P. Suresh', email: 'ncsdess2026+trk1a@hitam.org' },
        { label: 'Incharge 2', name: 'Mr. K. Suresh', email: 'ncsdess2026+trk1b@hitam.org' }
      ]
    },
    2: {
      name: 'Track 2: Smart Electronics & Sensor-Based Solutions for Society (ECE + IoT)',
      subject: 'NC-SDESS Track 2 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. J. Rajeshwar Goud', email: 'ncsdess2026+trk2a@hitam.org' },
        { label: 'Incharge 2', name: 'Dr. S. V. Devika', email: 'ncsdess2026+trk2b@hitam.org' }
      ]
    },
    3: {
      name: 'Track 3: Software Systems & Cyber Solutions for Sustainable Development (CSE + CS)',
      subject: 'NC-SDESS Track 3 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. S. V. Hemanth', email: 'ncsdess2026+trk3a@hitam.org' },
        { label: 'Incharge 2', name: 'Dr. K. David Raju', email: 'ncsdess2026+trk3b@hitam.org' }
      ]
    },
    4: {
      name: 'Track 4: AI & Data-Driven Solutions for Societal Challenges (AI-ML + DS)',
      subject: 'NC-SDESS Track 4 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. Rohit', email: 'ncsdess2026+trk4a@hitam.org' },
        { label: 'Incharge 2', name: 'Dr. Aparna', email: 'ncsdess2026+trk4b@hitam.org' }
      ]
    },
    5: {
      name: 'Track 5: Cyber-Physical Systems & Intelligent Automation for Smart Society (IoT + ECE + CSE)',
      subject: 'NC-SDESS Track 5 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. P. Padmaja', email: 'ncsdess2026+trk5a@hitam.org' },
        { label: 'Incharge 2', name: 'Mr. Bhaskar Das', email: 'ncsdess2026+trk5b@hitam.org' }
      ]
    },
    6: {
      name: 'Track 6: Sustainable Materials, Applied Physics & Engineering Innovations (Physics + MECH)',
      subject: 'NC-SDESS Track 6 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. Motilal Lakavat', email: 'ncsdess2026+trk6a@hitam.org' },
        { label: 'Incharge 2', name: 'Dr. Jagga Rao', email: 'ncsdess2026+trk6b@hitam.org' }
      ]
    },
    7: {
      name: 'Track 7: Integrated Engineering Solutions for Sustainable Society (Interdisciplinary)',
      subject: 'NC-SDESS Track 7 Submission',
      incharges: [
        { label: 'Incharge 1', name: 'Dr. T. Rambabu', email: 'ncsdess2026+trk7a@hitam.org' },
        { label: 'Incharge 2', name: 'Dr. B. V. R. S. Subrahmanyam', email: 'ncsdess2026+trk7b@hitam.org' }
      ]
    }
  };

  const trackBtns = document.querySelectorAll('.track-btn');
  const modal = document.getElementById('trackDetailsModal');
  const titleEl = document.getElementById('trackTitle');
  const inchargeListEl = document.getElementById('inchargeList');

  if (!modal) return;

  trackBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const trackNum = btn.getAttribute('data-track');
      const track = trackData[trackNum];

      if (!track) return;

      titleEl.textContent = track.name;
      inchargeListEl.innerHTML = track.incharges.map((entry) => {
        const link = gmailComposeUrl(entry.email, track.subject, `Track: ${track.name}\nTo: ${entry.name}\n`);
        return `
          <div style="margin: 8px 0; padding: 10px; background: #f8faff; border-radius: 4px; border: 1px solid #d6e4ff; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div style="font-weight: 600;">${entry.label}: ${entry.name}</div>
            <a class="btn primary" href="${link}" target="_blank" rel="noopener" style="margin-left: auto;">${entry.label}</a>
          </div>`;
      }).join('');

      modal.style.display = 'block';
      modal.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
  });

  // Submission ID generator & thank-you email
  const idForm = document.getElementById('idGenForm');
  const idResult = document.getElementById('idGenResult');
  const idValueEl = document.getElementById('idGenValue');
  const idEmailBtn = document.getElementById('idGenEmailBtn');

  function generateShortId(track, type) {
    const prefix = type === 'PAPER' ? 'NCS-P' : 'NCS-PS';
    const rand = Math.floor(1000 + Math.random() * 9000); // 4-digit
    return `${prefix}-${track}-${rand}`;
  }

  if (idForm && idResult && idValueEl && idEmailBtn) {
    idForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('idName').value.trim();
      const email = document.getElementById('idEmail').value.trim();
      const track = document.getElementById('idTrack').value;
      const type = document.getElementById('idType').value;
      const title = document.getElementById('idTitle').value.trim();

      if (!name || !email || !track || !type || !title) {
        alert('Please fill all required fields.');
        return;
      }

      const id = generateShortId(track, type);
      idValueEl.textContent = id;
      idResult.style.display = 'block';

      const typeLabel = type === 'PAPER' ? 'Paper' : 'Poster';
      const subject = `NC-SDESS ${typeLabel} Submission ID ${id}`;
      const body = [
        `Dear ${name},`,
        '',
        'Thank you for your submission to NC-SDESS 2026.',
        `Submission ID: ${id}`,
        `Type: ${typeLabel}`,
        `Track: ${track}`,
        `Title: ${title}`,
        '',
        'Please keep this ID for all correspondence and include it when emailing your in-charge.',
        '',
        'Regards,',
        'NC-SDESS 2026 Conference Team'
      ].join('\n');

      const mailUrl = gmailComposeUrl(email, subject, body, 'ncsdess2026@hitam.org');
      idEmailBtn.href = mailUrl;
    });
  }
});

// Abstract Form Handling
(function() {
  const abstractForm = document.getElementById('abstractForm');
  const abstractContent = document.getElementById('abs-content');
  const wordCountEl = document.getElementById('wordCount');

  if (abstractForm) {
    // Word counter
    if (abstractContent && wordCountEl) {
      abstractContent.addEventListener('input', function() {
        const words = this.value.trim().split(/\s+/).filter(w => w.length > 0).length;
        wordCountEl.textContent = words;
      });
    }

    // Inline field validation and messaging
    const nameInput = document.getElementById('abs-name');
    const emailInput = document.getElementById('abs-email');
    const phoneInput = document.getElementById('abs-phone');

    function setError(input, message) {
      input.classList.add('invalid');
      // Remove any existing error
      const parent = input.parentElement;
      let err = parent.querySelector('.error-text');
      if (!err) {
        err = document.createElement('small');
        err.className = 'error-text';
        parent.appendChild(err);
      }
      err.textContent = message;
    }

    function clearError(input) {
      input.classList.remove('invalid');
      const parent = input.parentElement;
      const err = parent.querySelector('.error-text');
      if (err) err.remove();
    }

    function validateInput(input) {
      if (input.checkValidity()) {
        clearError(input);
        return true;
      }
      const message = input.title || 'Please correct this field.';
      setError(input, message);
      return false;
    }

    [nameInput, emailInput, phoneInput].forEach((el) => {
      if (!el) return;
      el.addEventListener('blur', () => validateInput(el));
      el.addEventListener('input', () => {
        if (el.classList.contains('invalid')) validateInput(el);
      });
    });

    // Form submission
    abstractForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Built-in browser validation first
      if (!abstractForm.checkValidity()) {
        abstractForm.reportValidity();
        // Also ensure inline messages are visible for the core fields
        [nameInput, emailInput, phoneInput].forEach((el) => el && validateInput(el));
        return;
      }

      // Disable submit button
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.classList.add('loading');

      const formData = new FormData(this);

      fetch('process-abstract.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showMessage('Abstract submitted successfully! Check your email for confirmation and acceptance emails. Submission ID: ' + data.submission_id, 'success');
          
          // Reset form
          abstractForm.reset();
          if (wordCountEl) wordCountEl.textContent = '0';
          
          // Scroll to top
          window.scrollTo(0, 0);
          
          // Reset button after 3 seconds
          setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = originalText;
          }, 3000);
        } else {
          showMessage(data.message || 'An error occurred during submission', 'error');
          submitBtn.disabled = false;
          submitBtn.classList.remove('loading');
          submitBtn.textContent = originalText;
        }
      })
      .catch(error => {
        showMessage('Error: ' + error.message, 'error');
        submitBtn.disabled = false;
        submitBtn.classList.remove('loading');
        submitBtn.textContent = originalText;
      });
    });
  }
})();

// Show message function
function showMessage(message, type) {
  // Create message container if it doesn't exist
  let messageContainer = document.getElementById('messageContainer');
  if (!messageContainer) {
    messageContainer = document.createElement('div');
    messageContainer.id = 'messageContainer';
    messageContainer.style.position = 'fixed';
    messageContainer.style.top = '80px';
    messageContainer.style.left = '50%';
    messageContainer.style.transform = 'translateX(-50%)';
    messageContainer.style.zIndex = '999';
    messageContainer.style.maxWidth = '90%';
    messageContainer.style.maxWidth = '500px';
    document.body.appendChild(messageContainer);
  }

  const messageEl = document.createElement('div');
  messageEl.style.padding = '16px';
  messageEl.style.borderRadius = '8px';
  messageEl.style.marginBottom = '12px';
  messageEl.style.animation = 'slideDown 0.3s ease';
  messageEl.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
  
  if (type === 'success') {
    messageEl.style.background = '#e8f5e9';
    messageEl.style.border = '1px solid #4CAF50';
    messageEl.style.color = '#2e5e2e';
  } else {
    messageEl.style.background = '#ffebee';
    messageEl.style.border = '1px solid #d32f2f';
    messageEl.style.color = '#b71c1c';
  }

  messageEl.textContent = message;
  messageContainer.appendChild(messageEl);

  // Auto-remove after 6 seconds
  setTimeout(() => {
    messageEl.style.animation = 'slideUp 0.3s ease';
    setTimeout(() => messageEl.remove(), 300);
  }, 6000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateX(-50%) translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }
  }
  @keyframes slideUp {
    from {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }
    to {
      opacity: 0;
      transform: translateX(-50%) translateY(-20px);
    }
  }
`;
document.head.appendChild(style);

// Function to toggle required fields based on submission type in abstract form
function updateAbstractFormFields() {
  const trackField = document.getElementById('abs-track').parentElement;
  const titleField = document.getElementById('abs-title').parentElement;
  const contentField = document.getElementById('abs-content').parentElement;

  // Always require track, title, and content for paper/poster submissions
  trackField.style.display = 'block';
  titleField.style.display = 'block';
  contentField.style.display = 'block';
  document.getElementById('abs-track').setAttribute('required', 'required');
  document.getElementById('abs-title').setAttribute('required', 'required');
  document.getElementById('abs-content').setAttribute('required', 'required');
}

// Function to disable/enable paper type field based on participant type
function updatePaperTypeField() {
  const participantType = document.getElementById('participantType').value;
  const organizationSelect = document.getElementById('organization');
  const submissionTypeSelect = document.getElementById('submissionType');
  const paperTypeSelect = document.getElementById('paperType');

  // Get the form-group divs by walking up the DOM
  const submissionTypeFormGroup = submissionTypeSelect.parentElement;
  const paperTypeFormGroup = paperTypeSelect.parentElement;

  console.log('Participant Type:', participantType);
  console.log('Paper Type FormGroup:', paperTypeFormGroup);

  if (participantType === 'listener') {
    // For listeners, auto-set organization to 'any' and submission type to 'listener'
    organizationSelect.value = 'any';
    organizationSelect.disabled = true;
    submissionTypeSelect.value = 'listener';
    
    // Hide and disable submission type field completely
    submissionTypeFormGroup.style.display = 'none';
    submissionTypeSelect.removeAttribute('required');
    submissionTypeSelect.disabled = true;
    
    // Hide and disable paper type field
    paperTypeFormGroup.style.display = 'none';
    paperTypeSelect.removeAttribute('required');
    paperTypeSelect.value = 'na';
    paperTypeSelect.disabled = true;
    
    console.log('Listener mode activated - fields hidden');
  } else {
    // For other participant types, reset and enable fields
    organizationSelect.disabled = false;
    organizationSelect.value = '';
    
    submissionTypeFormGroup.style.display = 'block';
    submissionTypeSelect.setAttribute('required', 'required');
    submissionTypeSelect.value = '';
    submissionTypeSelect.disabled = false;
    
    paperTypeFormGroup.style.display = 'block';
    paperTypeSelect.setAttribute('required', 'required');
    paperTypeSelect.value = '';
    paperTypeSelect.disabled = false;
    
    console.log('Non-listener mode - fields shown');
  }
}

// Function to toggle required fields based on submission type in fee calculator
function updateSubmissionTypeFields() {
  const submissionType = document.getElementById('submissionType').value;
  const paperTypeField = document.getElementById('paperType').parentElement;
  const paperTypeSelect = document.getElementById('paperType');

  if (submissionType === 'listener') {
    // For listeners, hide paper type field but set it to 'na'
    paperTypeField.style.display = 'none';
    paperTypeSelect.removeAttribute('required');
    paperTypeSelect.value = 'na';
  } else {
    // For papers and posters, show paper type field
    paperTypeField.style.display = 'block';
    paperTypeSelect.setAttribute('required', 'required');
    paperTypeSelect.value = '';
  }
}

