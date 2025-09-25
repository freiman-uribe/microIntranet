<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'edad' => 'required|integer|min:1|max:120',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => $this->isMethod('post') ? 'required|string|min:6|confirmed' : 'nullable|string|min:6|confirmed',
            'admin' => [
                'boolean',
                function ($attribute, $value, $fail) use ($userId) {
                    // Si es una actualización y el usuario autenticado es admin
                    if ($userId && auth()->check() && auth()->user()->isAdmin() && auth()->id() === $userId) {
                        // Verificar si está intentando cambiar su propio rol
                        $currentUser = auth()->user();
                        $currentAdminStatus = $currentUser->admin;
                        
                        // Si intenta cambiar su propio estatus de admin
                        if ($currentAdminStatus !== (bool)$value) {
                            $fail('No puedes modificar tu propio rol de administrador por razones de seguridad.');
                        }
                    }
                }
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'apellido.required' => 'El campo apellido es obligatorio.',
            'edad.required' => 'El campo edad es obligatorio.',
            'edad.integer' => 'La edad debe ser un número entero.',
            'edad.min' => 'La edad debe ser mayor a 0.',
            'edad.max' => 'La edad no puede ser mayor a 120 años.',
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'Debe proporcionar un email válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'admin.boolean' => 'El campo de administrador debe ser verdadero o falso.',
        ];
    }
}
