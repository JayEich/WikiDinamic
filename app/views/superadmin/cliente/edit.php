<?php
// Recuperar errores y old input al principio de la vista
use Core\FormRequest; // Necesitamos importar FormRequest para usar los helpers estáticos

$errors = FormRequest::getErrorsFromSession();
$oldInput = FormRequest::getOldInputFromSession();

// Asumimos que $client contiene los datos si NO venimos de una redirección con error
// Si venimos de error, $oldInput tendrá los datos a mostrar
$clientName = $oldInput['name'] ?? ($client['name'] ?? '');
$colorPrimary = $oldInput['color_primary'] ?? ($client['color_primary'] ?? '#030339');
$colorSecondary = $oldInput['color_secondary'] ?? ($client['color_secondary'] ?? '#FFFFFF');
$colorTertiary = $oldInput['color_tertiary'] ?? ($client['color_tertiary'] ?? '#555555');
$logoPath = $client['logo_path'] ?? null; // El logo no se repopula desde old input

$csrftoken = $_SESSION['csrf_token'];

// Manejo de mensajes flash de éxito/error (como antes)
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrftoken ?>">
    <title>Editar Datos del Cliente - WikiConcept</title>
    <style>
        /* Estilo simple para errores */
        .is-invalid { border-color: #dc3545; }
        .invalid-feedback { display: block; color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Editar Datos del Cliente</h1>

        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashMessage['type']) ?>" role="alert">
                <?= htmlspecialchars($flashMessage['text']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors) && !isset($errors['name']) && !isset($errors['color_primary']) /*...etc...*/ ): ?>
            <div class="alert alert-danger">
                Hubo errores al procesar el formulario.
                <?php // print_r($errors); // Para depurar ?>
            </div>
        <?php endif; ?>


        <?php if (isset($client)): // Asegurarse que $client existe (puede no existir si hay error grave al cargar) ?>
            <form action="<?= route('superadmin.client.update') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrftoken ?>">

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Cliente</label>
                    <input type="text"
                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           id="name" name="name"
                           value="<?= htmlspecialchars($clientName) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(implode(', ', $errors['name'])) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="color_primary" class="form-label">Color Primario</label>
                        <input type="color"
                               class="form-control form-control-color <?= isset($errors['color_primary']) ? 'is-invalid' : '' ?>"
                               id="color_primary" name="color_primary"
                               value="<?= htmlspecialchars($colorPrimary) ?>" title="Elige tu color primario">
                        <?php if (isset($errors['color_primary'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars(implode(', ', $errors['color_primary'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                     <div class="col-md-4">
                        <label for="color_secondary" class="form-label">Color Secundario</label>
                        <input type="color"
                               class="form-control form-control-color <?= isset($errors['color_secondary']) ? 'is-invalid' : '' ?>"
                               id="color_secondary" name="color_secondary"
                               value="<?= htmlspecialchars($colorSecondary) ?>" title="Elige tu color secundario">
                         <?php if (isset($errors['color_secondary'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars(implode(', ', $errors['color_secondary'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                     <div class="col-md-4">
                        <label for="color_tertiary" class="form-label">Color Terciario</label>
                        <input type="color"
                               class="form-control form-control-color <?= isset($errors['color_tertiary']) ? 'is-invalid' : '' ?>"
                               id="color_tertiary" name="color_tertiary"
                               value="<?= htmlspecialchars($colorTertiary) ?>" title="Elige tu color terciario">
                         <?php if (isset($errors['color_tertiary'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars(implode(', ', $errors['color_tertiary'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Logo Actual</label>
                    <div>
                         <?php if (!empty($logoPath)): ?>
                            <img src="<?= asset($logoPath) ?>" alt="Logo actual" style="max-height: 80px; border: 1px solid #ccc; padding: 5px;">
                        <?php else: ?>
                            <p>No hay logo cargado.</p>
                        <?php endif; ?>
                    </div>
                    <label for="logo_upload" class="form-label mt-2">Cargar Nuevo Logo (Opcional)</label>
                    <input class="form-control" type="file" id="logo_upload" name="logo_upload" accept="image/png, image/jpeg, image/webp">
                     <small class="form-text text-muted">Dejar vacío para mantener el logo actual.</small>
                      </div>


                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="<?= route('superadmin.dashboard') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
              No se pudieron cargar los datos del cliente.
            </div>
        <?php endif; ?>
    </div>
    </body>
</html>