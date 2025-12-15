/* ===================================
   SCRIPT.JS - Global initialization
=================================== */

$(document).ready(function() {
  
  // Configure Toastr
  if (typeof toastr !== 'undefined') {
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: 'toast-top-right',
      timeOut: 3000
    };
  }

  // Initialize AuthService (check if logged in)
  if (typeof AuthService !== 'undefined' && AuthService.init) {
    AuthService.init();
  }
});

// Toggle password visibility
// Toggle password visibility
$(document).on('click', '.toggle-password', function() {
  // Find the input in the same input-group
  const input = $(this).closest('.input-group').find('input');
  const icon = $(this).find('i');
  
  if (input.attr('type') === 'password') {
    input.attr('type', 'text');
    icon.removeClass('bi-eye').addClass('bi-eye-slash');
  } else {
    input.attr('type', 'password');
    icon.removeClass('bi-eye-slash').addClass('bi-eye');
  }
});

// Password strength checker
$(document).on('input', '#signupPassword, #newPassword', function() {
  const password = $(this).val();
  let strength = 0;
  let text = 'None';
  let color = '';

  if (password.length >= 6) strength++;
  if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
  if (password.match(/[0-9]/)) strength++;
  if (password.match(/[^a-zA-Z0-9]/)) strength++;

  switch(strength) {
    case 0:
    case 1:
      text = 'Weak';
      color = 'bg-danger';
      break;
    case 2:
      text = 'Medium';
      color = 'bg-warning';
      break;
    case 3:
      text = 'Good';
      color = 'bg-info';
      break;
    case 4:
      text = 'Strong';
      color = 'bg-success';
      break;
  }

  $('#passwordStrengthBar')
    .removeClass('bg-danger bg-warning bg-info bg-success')
    .addClass(color)
    .css('width', (strength * 25) + '%');
  
  $('#passwordStrengthText').text(`Password strength: ${text}`);
});

// Generate random password
$(document).on('click', '#generatePassBtn', function() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
  let password = '';
  for (let i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  $('#signupPassword').val(password).trigger('input');
});

// Email validation feedback
$(document).on('blur', 'input[type="email"]', function() {
  const email = $(this).val();
  const feedback = $(this).siblings('.email-feedback');
  
  if (!email) {
    feedback.hide();
    return;
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    feedback.text('Please enter a valid email address').show();
  } else {
    feedback.hide();
  }
});