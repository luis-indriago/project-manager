<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
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
        return [
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'El proyecto es obligatorio.',
            'project_id.exists'   => 'El proyecto seleccionado no existe.',

            'assigned_to.required' => 'Debes asignar la tarea a un usuario.',
            'assigned_to.exists'   => 'El usuario seleccionado no existe.',

            'title.required' => 'El título es obligatorio.',
            'title.string'   => 'El título debe ser texto.',
            'title.max'      => 'El título no puede superar los 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string'   => 'La descripción debe ser texto.',
        ];
    }
}
