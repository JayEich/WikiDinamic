import { Swal } from '../shared/libs.js';
import { BASE_URL } from '../shared/config.js';
console.log("✅ events.js cargado correctamente");

export function bindLoginEvents({ validateForm, mostrarError, getCSRFToken }) {

  const passInput = document.querySelector("#contraseña");
  const toggleBtn = document.querySelector(".toggle-password");

  // Mostrar aviso de Mayúsculas activadas
  passInput?.addEventListener("keyup", (e) => {
    document.querySelector("#capsLockIndicator")?.classList.toggle("d-none", !e.getModifierState("CapsLock"));
  });

  // Mostrar/ocultar contraseña
  toggleBtn?.addEventListener("click", () => {
    const type = passInput.type === "password" ? "text" : "password";
    passInput.type = type;
    const icon = toggleBtn.querySelector("i");
    if (icon) {
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    }
  });

  // Prevenir copiar/pegar
  ["cut", "copy", "paste"].forEach((evt) =>
    passInput?.addEventListener(evt, (e) => e.preventDefault())
  );
}
