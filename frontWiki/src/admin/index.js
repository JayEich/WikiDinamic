import { logoutWithConfirmation } from '../shared/config.js';

document.getElementById('logoutBtn')?.addEventListener('click', (e) => {
  e.preventDefault();
  logoutWithConfirmation();
});
