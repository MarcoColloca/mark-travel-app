<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStageRequest extends FormRequest
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
            'day_id' => 'required',
            'name' => 'string',
            'cover' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:2500',    
            'description' => 'nullable|string|max:2500',  
            'curiosities' => 'nullable|string|max:2500',  
            'address' => 'nullable|string|max:2500', 
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric', 
        ];
    }
}
