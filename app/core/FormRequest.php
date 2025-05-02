<?php
namespace Core;

class FormRequest extends Request
{
    protected array $errors = [];
    protected array $validated = [];

    public function __construct()
    {
        parent::__construct();
        \App\Helpers\Security::startSecureSession();

        if (!$this->authorize()) {
            $this->failedAuthorization();
        }

        $this->autoValidate();
    }

    public function rules(): array { return []; }
    public function messages(): array { return []; }
    public function authorize(): bool { return true; }


    protected function autoValidate(): void
    {
        $rules = $this->rules();
        $customMessages = $this->messages();

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = trim($this->input($field) ?? '');

            foreach ($rulesArray as $rule) {
                $ruleName = $rule;
                $param = null;

                if (str_contains($rule, ':')) {
                    [$ruleName, $param] = explode(':', $rule);
                }

                $key = "{$field}.{$ruleName}";

                switch ($ruleName) {
                    case 'required':
                        if ($value === '') {
                            $this->addError($field, $customMessages[$key] ?? 'Este campo es obligatorio.');
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, $customMessages[$key] ?? 'Debe ser un correo válido.');
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($value)) {
                            $this->addError($field, $customMessages[$key] ?? 'Debe ser numérico.');
                        }
                        break;
                    case 'min':
                        if (strlen($value) < (int)$param) {
                            $this->addError($field, $customMessages[$key] ?? "Debe tener al menos $param caracteres.");
                        }
                        break;
                    case 'max':
                        if (strlen($value) > (int)$param) {
                            $this->addError($field, $customMessages[$key] ?? "No debe exceder $param caracteres.");
                        }
                        break;
                }
            }

            if (!isset($this->errors[$field])) {
                $this->validated[$field] = $value;
            }
        }
    }

    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    protected function failedAuthorization(): void
    {
        http_response_code(403);
        \App\Helpers\Security::logError("Error en la Autorizacion(FormRequest): ". $_SERVER['REQUEST_URI'] . " - " . $_SERVER['HTTP_REFERER']);
        header('Location: ' . route('unauthorized')); exit;
        exit;
    }


    protected function failedValidation(): void 
    {
        $_SESSION['_errors'] = $this->errors;
        $_SESSION['_old_input'] = $this->all();
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    public static function getErrorsFromSession(): array {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']); 
        return $errors;
    }

    public static function getOldInputFromSession(): array {
        $input = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']); 
        return $input;
    }
}

