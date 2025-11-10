<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'string|nullable',
            'location.longitude' => 'required|regex:/^-?\d{1,2}\.\d{6,}$/',
            'location.latitude' => 'required|regex:/^-?\d{1,2}\.\d{6,}$/',
            'note' => 'nullable|string',
        ];
    }
}
