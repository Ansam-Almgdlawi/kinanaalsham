<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventPostRequest extends FormRequest
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

    public function rules()
    {
        return [
            'event_id' => 'required|exists:events,id',
            'content' => 'required|string|max:2000',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,png,mp4|max:2048'
        ];
    }
}
