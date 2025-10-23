<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieSearchRequest extends FormRequest
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
            'search' => 'required|string|max:255',
            'type' => 'nullable|string|in:movie,series,episode',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'page' => 'nullable|integer|min:1',
            'newSearch' => 'nullable|boolean'
        ];
    }
}
