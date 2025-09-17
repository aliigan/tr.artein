// Contact form validation and submission + success toast
(function () {
  // Bootstrap Toast init for contact success
  document.addEventListener('DOMContentLoaded', function () {
    const toastEl = document.getElementById('contactToast');
    if (!toastEl) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
      try {
        const t = new bootstrap.Toast(toastEl);
        t.show();
      } catch (e) {
        // Fallback
        toastEl.classList.add('show');
        toastEl.style.display = 'block';
        toastEl.style.opacity = '1';
      }
    } else {
      // Fallback: show toast without Bootstrap
      toastEl.classList.add('show');
      toastEl.style.display = 'block';
      toastEl.style.opacity = '1';
      // Auto-hide after 5s
      setTimeout(function(){
        toastEl.style.opacity = '0';
        setTimeout(function(){ toastEl.style.display = 'none'; }, 500);
      }, 5000);
    }
  });

  // Form validation + AJAX submit
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.contact-form form, section#contact form, form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const name = (form.querySelector('input[name="name"]') || {}).value?.trim() || '';
      const email = (form.querySelector('input[name="email"]') || {}).value?.trim() || '';
      const phone = (form.querySelector('input[name="phone"]') || {}).value?.trim() || '';
      const message = (form.querySelector('textarea[name="message"]') || {}).value?.trim() || '';
      const recaptchaEl = form.querySelector('textarea[name="g-recaptcha-response"]');
      const recaptcha = recaptchaEl ? recaptchaEl.value.trim() : '';

      // Clear previous alerts
      document.querySelectorAll('.contact-form .alert, section#contact .alert').forEach(function (el) { el.remove(); });

      if (!name || !email || !message) {
        showAlert('Lütfen zorunlu alanları doldurun.', 'danger');
        return false;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showAlert('Lütfen geçerli bir e-posta adresi girin.', 'danger');
        return false;
      }

      if (phone) {
        const phoneClean = phone.replace(/[\s\-\(\)]/g, '');
        if (!/^\+?[0-9]{10,15}$/.test(phoneClean)) {
          showAlert('Lütfen geçerli bir telefon numarası girin.', 'danger');
          return false;
        }
      }

      if (!recaptcha) {
        showAlert('Lütfen güvenlik doğrulamasını tamamlayın.', 'danger');
        return false;
      }

      // Loading state
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Gönderiliyor...';
        submitBtn.disabled = true;
      }

      // Submit via fetch to current URL
      const formData = new FormData(form);
      fetch(window.location.href, { method: 'POST', body: formData })
        .then(function (r) { return r.text(); })
        .then(function () { window.location.reload(); })
        .catch(function () {
          showAlert('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
          if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          }
        });
    });
  });

  function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
    alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + message +
      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    const container = document.querySelector('.contact-form') || document.querySelector('section#contact') || document.body;
    if (container.firstChild) {
      container.insertBefore(alertDiv, container.firstChild);
    } else {
      container.appendChild(alertDiv);
    }
  }
})();


