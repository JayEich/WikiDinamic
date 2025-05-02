<?php
namespace App\Controllers\Superadmin; 

use App\Models\Client;
use App\Models\Usuario;
      
use App\Requests\UpdateClientRequest;
use App\Helpers\Security;

class ClientController {

    public function edit() {
        $client_uuid = $_SESSION['client_uuid']; 
        $client = Client::findByUuid($client_uuid);

        if (!$client) {
            http_response_code(404);
            echo "Cliente asociado no encontrado.";
            exit;
        }

        require __DIR__ . '/../../../views/superadmin/clients/edit.php';
    }

   
    public function update(UpdateClientRequest $request) {
        $client_uuid = $_SESSION['client_uuid'];
        $currentUser = Usuario::findByUuid($_SESSION['user_id']); 
        $currentClientData = Client::findByUuid($client_uuid); 

        $validatedData = $request->validated();
        $newLogoPath = $currentClientData['logo_path'] ?? null;

        if (isset($_FILES['logo_upload']) && $_FILES['logo_upload']['error'] === UPLOAD_ERR_OK) {
            if (!$currentUser) {
                 $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Error: No se pudo encontrar el usuario actual.'];
                 header('Location: ' . route('superadmin.client.edit'));
                 exit;
            }
            try {
                $newLogoPath = Security::guardarImagenHasheada(
                    $_FILES['logo_upload'],
                    $currentUser['username'],
                    $currentClientData['logo_path'] ?? null
                );
            } catch (\Exception $e) {
                Security::logError("Error subiendo logo para cliente {$client_uuid}: " . $e->getMessage());
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Error al subir el logo: ' . $e->getMessage()];
                header('Location: ' . route('superadmin.client.edit'));
                exit;
            }
        }

        $validatedData['logo_path'] = $newLogoPath;
        $success = Client::update($client_uuid, $validatedData);

        if ($success) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Datos del cliente actualizados correctamente.'];
        } else {
             Security::logError("Error al actualizar cliente {$client_uuid} en la BD.");
            $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Error al guardar los datos en la base de datos. Intente de nuevo.'];
            $_SESSION['old_input'] = $request->all();
        }

        header('Location: ' . route('superadmin.client.edit'));
        exit;
    }
}