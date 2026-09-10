document.addEventListener('DOMContentLoaded', () => {
  // fade out and remove flash messages on their own after a bit
  document.querySelectorAll('.flash').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 400);
    }, 4000);
  });

  // lock the button after the checkout form is submitted so people can't
  // double-click and end up placing the same order twice
  const checkoutForm = document.querySelector('.form-page form');
  if (checkoutForm && window.location.pathname.includes('checkout.php')) {
    checkoutForm.addEventListener('submit', () => {
      const btn = checkoutForm.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Placing order...';
      }
    });
  }

  // keep qty inputs within stock limits without waiting on a server round trip
  document.querySelectorAll('.qty-input').forEach((input) => {
    input.addEventListener('change', () => {
      const max = parseInt(input.getAttribute('max'), 10);
      if (max && parseInt(input.value, 10) > max) {
        input.value = max;
      }
      if (parseInt(input.value, 10) < 0) {
        input.value = 0;
      }
    });
  });
});
