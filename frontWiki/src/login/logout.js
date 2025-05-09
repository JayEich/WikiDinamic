import { BASE_URL } from '../../shared/config.js'; // Ajusta la ruta a shared/config.js
import { getCSRFToken } from '../../shared/csrf.js'; // Ajusta la ruta a shared/csrf.js
import { Swal } from '../../shared/libs.js'; // Ajusta la ruta a shared/libs.js

// Función exportable para cerrar sesión
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

// Event listener para el botón de cerrar sesión (ejemplo, puedes centralizar esto en main.js)
// Es común que el botón de logout esté en el layout general, por lo que podrías querer
// adjuntar este listener desde un archivo general o desde main.js
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('btnCerrarSesion');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            logoutWithConfirmation();
        });
    }
    const backBtn = document.getElementById('btnAtras'); // Si tienes un botón de "Atrás"
    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.history.back();
        });
    }
});