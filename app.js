document.addEventListener('DOMContentLoaded', () => {
  // Show/Hide password
  document.querySelectorAll('[data-toggle-password]').forEach(btn => btn.addEventListener('click', () => {
    const input = document.getElementById(btn.dataset.togglePassword);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.textContent = show ? 'Hide' : 'Show';
  }));

  // Auto-hide alerts
  document.querySelectorAll('[data-dismiss]').forEach(a => setTimeout(() => a.remove(), 5000));

  // Auto-submit filters
  document.querySelectorAll('[data-autosubmit]').forEach(el => el.addEventListener('change', () => el.form.submit()));

  // Delete confirmation modal
  const modal = document.getElementById('confirm-modal');
  if (!modal) return;
  let pending = null;
  const close = () => { modal.classList.remove('open'); pending = null; };
  document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', e => {
    if (form.dataset.ok) return;
    e.preventDefault();
    pending = form;
    modal.querySelector('p').textContent = form.dataset.confirm;
    modal.classList.add('open');
  }));
  document.getElementById('confirm-yes').addEventListener('click', () => { if (pending) { pending.dataset.ok = 1; pending.submit(); } });
  document.getElementById('confirm-no').addEventListener('click', close);
  modal.addEventListener('click', e => { if (e.target === modal) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
});
