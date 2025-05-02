<?php
namespace Core;
class Request
{
    protected array $data;

    public function __construct()
    {
        $this->data = array_merge($_GET, $_POST);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function validate(array $rules): array
    {
        $validated = [];
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = trim($this->input($field) ?? '');

            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[$field][] = 'Este campo es obligatorio.';
                }
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Debe ser un correo válido.';
                }
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = 'Debe ser numérico.';
                }
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $value;
            }
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo "<pre>Errores de validación:\n";
            print_r($errors);
            echo "</pre>";
            exit;
        }

        return $validated;
    }
}
