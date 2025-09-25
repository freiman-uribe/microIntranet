<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CotizacionRequest extends FormRequest
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
        // Debug: validación temporal más permisiva
        return [
            'fecha_emision' => 'sometimes|nullable|date',
            'productos' => 'sometimes|array',
            'productos.*.sku' => 'sometimes|string',
            'productos.*.cantidad' => 'sometimes|integer|min:1',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser una fecha válida.',
            'productos.required' => 'Debe seleccionar al menos un producto.',
            'productos.array' => 'Los productos deben ser un arreglo válido.',
            'productos.min' => 'Debe seleccionar al menos un producto.',
            'productos.*.sku.required' => 'El SKU del producto es obligatorio.',
            'productos.*.sku.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
