<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripPointRequest extends FormRequest
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
            'point_uuid' => 'required|string|exists:points,uuid',
            'day' => 'required|integer|min:1',
            'time' => 'nullable|date_format:H:i',
            'order' => 'nullable|integer|min:0',
            'note' => 'nullable|string',
        ];
    }
}
