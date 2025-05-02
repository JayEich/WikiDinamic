<?php
namespace App\Requests;

use Core\FormRequest;
use App\Helpers\Security; 

class UpdateClientRequest extends FormRequest
{
    /**
     * Define las reglas de validación para la petición de actualización de cliente.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'color_primary' => 'required', 
            'color_secondary' => 'required',
            'color_tertiary' => 'required',
        ];
    }

    /**
     * Define mensajes de error personalizados para las reglas de validación.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cliente es obligatorio.',
            'name.max' => 'El nombre del cliente no puede exceder los 255 caracteres.',
            'color_primary.required' => 'El color primario es obligatorio.',
            'color_secondary.required' => 'El color secundario es obligatorio.',
            'color_tertiary.required' => 'El color terciario es obligatorio.',
        ];
    }

    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {

        $isAuthorized = isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin' && !empty($_SESSION['client_uuid']);

        if (!$isAuthorized) {
             Security::logError("Intento no autorizado de actualizar cliente.");
        }
        return $isAuthorized;
    }
}