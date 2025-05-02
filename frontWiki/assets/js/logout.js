function getCSRFToken() {
  return $('meta[name="csrf_token"]').attr("content");
}

function logout(){
  $.ajax({
      url: "index.php?controller=auth&action=logout", 
      type: "POST",
      data: {
        csrf_token: getCSRFToken(),
      },
      success: function (response) {
        if (response === "success") {
          window.location.href = "index.php?controller=auth&action=login";
        } else {
          Swal.fire({
            title: "Error",
            text: "Error al cerrar sesión. Por favor, intente de nuevo.",
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          title: "Error",
          text: "Error de conexión al cerrar sesión. Verifica tu conexión e intenta de nuevo.",
          icon: "error",
          confirmButtonText: "OK",
        });
      },
  });
}

$(document).on("click", "#btnCerrarSesion", function (e) {
  e.preventDefault();
  logout();
});

$(document).on("click", "#btnAtras", function (e) {
  e.preventDefault();
  window.history.back();
});
