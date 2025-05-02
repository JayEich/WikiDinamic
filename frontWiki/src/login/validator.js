export function mostrarError(message, field = null) {
  const msg = document.querySelector("#message");
  if (msg) msg.innerHTML = `<p class="text-dark">${message}</p>`;
  if (field) field.focus();
}


export function validateForm() {
  const usuario = document.querySelector("#usuario")?.value.trim();
  const clave = document.querySelector("#contraseña")?.value;

  if (!usuario && !clave) {
    mostrarError("Por favor, ingrese Usuario y contraseña", document.querySelector("#usuario"));
    return false;
  }

  if (!usuario) {
    mostrarError("Por favor digite su usuario", document.querySelector("#usuario"));
    return false;
  }

  if (!clave) {
    mostrarError("Por favor ingrese su contraseña", document.querySelector("#contraseña"));
    return false;
  }

  return true;
}
