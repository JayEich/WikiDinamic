function getCSRFToken(){
  return $('meta[name="csrf_token"]').attr("content");
}

function mostrarError(mensaje, campo) {
  $("#message").html(`<p class="text-dark">${mensaje}</p>`);
  if (campo) campo.focus();
}

function validarFormulario() {
  const usuario = $("#usuario").val().trim();
  const clave = $("#contraseña").val();

  if (!usuario && !clave) return mostrarError("Por favor, ingrese Usuario y contraseña", $("#usuario")), false;
  if (!usuario) return mostrarError("Por favor digite su usuario", $("#usuario")), false;
  if (!clave) return mostrarError("Por favor ingrese su contraseña", $("#contraseña")), false;

  return true;
}

$(document).ready(function () {
  let intentosFallidos = 0;
  let bloqueoHasta = 0;

  function estaBloqueado() {
      const ahora = Date.now();
      if (ahora < bloqueoHasta) {
          const restante = Math.ceil((bloqueoHasta - ahora) / 1000);
          mostrarError(`Demasiados intentos. Espera ${restante} segundos.`);
          return true;
      }
      return false;
  }

  $("#loginForm").on("submit", function (e) {
      e.preventDefault();
      if (estaBloqueado() || !validarFormulario()) return;

      let datos = $(this).serialize();
      datos += "&csrf_token=" + getCSRFToken();

      $.ajax({
          url: "index.php?controller=auth&action=login",
          type: "POST",
          data: datos,
          success: function (res) {
              if (res.startsWith("success:")) {
                  const redireccion = res.split(":")[1];
                  window.location.href = redireccion;
              } else {
                  mostrarError("Credenciales incorrectas.");
                  intentosFallidos++;
                  if (intentosFallidos >= 5) {
                      bloqueoHasta = Date.now() + 60000;
                      intentosFallidos = 0;
                  }
              }
          },
          error: function (xhr) {
              if (xhr.status === 403) {
                  window.location.href = "./index.php";
              } else {
                  mostrarError("Error en el servidor. Intenta más tarde.");
              }
          }
      });
  });

  // Mostrar alerta de mayúscula
  $("#contraseña").on("keydown keyup", function (e) {
      $("#capsLockIndicator").toggle(e.originalEvent.getModifierState("CapsLock"));
  });

  $(".toggle-password").click(function () {
      const input = $("#contraseña");
      const icon = $(this).find("i");
      const tipo = input.attr("type") === "password" ? "text" : "password";
      input.attr("type", tipo);
      icon.toggleClass("fa-eye fa-eye-slash");
  });

  // Prevenir copiar/pegar
  $("#contraseña").on("cut copy paste", function (e) {
      e.preventDefault();
  });


  if (!getCSRFToken()) {
      console.error("CSRF token not found");
      window.location.href = "./pp.php";
  }
});
