<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('page'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'present|string|min:3|max:255',
            'slug' => [
                'present',
                'string',
                'max:255',
                Rule::unique('pages')->ignore($this->route('page')),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
            ],
            'content' => 'present|array|min:1',
            'styles' => 'present|array',
            'template' => 'present|string',
            'status' => 'present|string|in:draft,published',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_robots' => 'nullable|string|in:index follow,noindex follow,index nofollow,noindex nofollow',
            'meta_google_verification' => 'nullable|string|max:255',
            'meta_bing_verification' => 'nullable|string|max:255',
            'meta_yandex_verification' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:255',
            'og_image' => 'nullable|string|max:255',
            'og_type' => 'nullable|string|in:website,article',
            'is_home' => 'boolean',
            'sort_order' => 'integer|min:0',
            'parent_id' => 'nullable|integer|exists:pages,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.present' => 'Le titre est requis',
            'title.string' => 'Le titre doit être une chaîne de caractères',
            'title.min' => 'Le titre doit contenir au moins :min caractères',
            'title.max' => 'Le titre ne peut pas dépasser :max caractères',
            'slug.present' => 'Le slug est requis',
            'slug.string' => 'Le slug doit être une chaîne de caractères',
            'slug.max' => 'Le slug ne peut pas dépasser :max caractères',
            'slug.unique' => 'Ce slug est déjà utilisé',
            'slug.regex' => 'Le slug ne doit contenir que des lettres minuscules, des chiffres et des tirets',
            'content.present' => 'Le contenu est requis',
            'content.array' => 'Le contenu doit être un tableau',
            'content.min' => 'Veuillez ajouter au moins un bloc de contenu',
            'styles.present' => 'Les styles sont requis',
            'styles.array' => 'Les styles doivent être un tableau',
            'template.present' => 'Le template est requis',
            'template.string' => 'Le template doit être une chaîne de caractères',
            'status.present' => 'Le statut est requis',
            'status.string' => 'Le statut doit être une chaîne de caractères',
            'status.in' => 'Le statut doit être soit brouillon soit publié',
            'meta_title.max' => 'Le titre SEO ne peut pas dépasser :max caractères',
            'meta_description.max' => 'La description SEO ne peut pas dépasser :max caractères',
            'meta_robots.in' => 'La valeur robots n\'est pas valide',
            'og_type.in' => 'Le type Open Graph doit être soit website soit article',
            'sort_order.integer' => 'L\'ordre doit être un nombre entier',
            'sort_order.min' => 'L\'ordre ne peut pas être négatif',
            'parent_id.exists' => 'La page parente sélectionnée n\'existe pas'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation()
    {
        if ($this->has('title') && empty($this->input('slug'))) {
            $this->merge([
                'slug' => Str::slug($this->input('title'))
            ]);
        }
    }
}
