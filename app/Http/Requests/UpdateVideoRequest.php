<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
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
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:512000',
            'external_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul video wajib diisi.',
            'title.max' => 'Judul video maksimal 150 karakter.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail harus: jpeg, png, jpg, atau gif.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
            'video_file.file' => 'File video harus berupa file.',
            'video_file.mimes' => 'Format video harus: mp4, mov, avi, atau wmv.',
            'video_file.max' => 'Ukuran video maksimal 500MB.',
            'external_url.url' => 'URL eksternal harus valid.',
            'external_url.max' => 'URL eksternal maksimal 255 karakter.',
        ];
    }
}
