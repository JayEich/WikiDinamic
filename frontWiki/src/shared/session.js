import { BASE_URL } from './config.js';
import { getCSRFToken } from './csrf.js';
import { Swal } from './libs.js';

export function logoutWithConfirmation() {
  Swal.fire({
    title: '¿Deseas cerrar sesión?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, salir',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`${BASE_URL}/logout`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `csrf_token=${getCSRFToken()}`
      })
      .then(res => res.text())
      .then(res => {
        if (res === 'success') {
          window.location.href = `${BASE_URL}/login`;
        } else {
          Swal.fire('Error', 'No se pudo cerrar sesión', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Hubo un problema con el servidor', 'error'));
    }
  });
}
