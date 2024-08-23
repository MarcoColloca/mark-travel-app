<?php

namespace App\Http\Requests;

use App\Rules\CheckDate;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
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
            'name' => 'required|string',
            'cover' => 'nullable|image|max:2048',
            'description' => 'nullable|string|max:2500',
            'notes'=> 'nullable|string|max:2500',
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', new CheckDate($this->input('startDate'), $this->input('endDate'))],
        ];
    }
}
