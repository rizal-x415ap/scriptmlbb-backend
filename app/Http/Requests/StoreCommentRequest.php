<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:100'],
            'author_email' => ['required', 'email', 'max:255'],
            'content' => [
                'required',
                'string',
                'min:2',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if (preg_match('/https?:\/\/|www\.|<script|<\/a>/i', $value)) {
                        $fail('Komentar hanya boleh berisi teks biasa. Tautan (link) dan tag script HTML tidak diizinkan.');
                    }
                }
            ],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $cleanName = strip_tags($this->author_name ?? '');
        $cleanEmail = strtolower(trim(strip_tags($this->author_email ?? '')));
        $cleanContent = strip_tags($this->content ?? '');

        // Strip HTML tags and script elements
        $cleanContent = preg_replace('/<[^>]*>/', '', $cleanContent);
        // Remove links & URLs
        $cleanContent = preg_replace('/https?:\/\/\S+/i', '', $cleanContent);
        $cleanContent = preg_replace('/www\.\S+/i', '', $cleanContent);

        $this->merge([
            'author_name' => trim($cleanName),
            'author_email' => $cleanEmail,
            'content' => trim($cleanContent),
        ]);
    }
}
