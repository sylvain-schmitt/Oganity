<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Page::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->title)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'array', 'min:1'],
            'styles' => ['nullable', 'array'],
            'template' => ['string', 'in:default,full-width,sidebar'],
            'status' => ['string', 'in:draft,published'],

            // Configuration avec nouvelle validation pour is_home
            'is_home' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && Page::where('is_home', true)->exists()) {
                        $fail('Une autre page est déjà définie comme page d\'accueil.');
                    }
                },
            ],
            'sort_order' => ['integer', 'min:0'],
            'parent_id' => ['nullable', 'exists:pages,id'],

            // SEO basique
            'meta_title' => ['nullable', 'string', 'min:3', 'max:60'],
            'meta_description' => ['nullable', 'string', 'min:10', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_robots' => ['string'],

            // Balises de vérification
            'meta_google_verification' => ['nullable', 'string', 'max:255'],
            'meta_bing_verification' => ['nullable', 'string', 'max:255'],
            'meta_yandex_verification' => ['nullable', 'string', 'max:255'],

            // Open Graph
            'og_title' => ['nullable', 'string', 'min:3', 'max:60'],
            'og_description' => ['nullable', 'string', 'min:10', 'max:160'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'og_type' => ['string', 'max:255'],
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
            // Titre
            'title.required' => 'Le titre est obligatoire',
            'title.min' => 'Le titre doit contenir au moins :min caractères',
            'title.max' => 'Le titre ne doit pas dépasser :max caractères',

            // SEO
            'meta_title.min' => 'Le titre SEO doit contenir au moins :min caractères',
            'meta_title.max' => 'Le titre SEO ne doit pas dépasser :max caractères',
            'meta_description.min' => 'La description SEO doit contenir au moins :min caractères',
            'meta_description.max' => 'La description SEO ne doit pas dépasser :max caractères',
            'meta_keywords.max' => 'Les mots-clés ne doivent pas dépasser :max caractères',

            // Open Graph
            'og_title.min' => 'Le titre Open Graph doit contenir au moins :min caractères',
            'og_title.max' => 'Le titre Open Graph ne doit pas dépasser :max caractères',
            'og_description.min' => 'La description Open Graph doit contenir au moins :min caractères',
            'og_description.max' => 'La description Open Graph ne doit pas dépasser :max caractères',
            'og_image.max' => 'L\'URL de l\'image ne doit pas dépasser :max caractères',
            'template.in' => 'Le template sélectionné doit être l\'un des suivants : default, full-width, sidebar',
            'meta_google_verification.max' => 'Le code de vérification Google ne doit pas dépasser 255 caractères',
            'meta_bing_verification.max' => 'Le code de vérification Bing ne doit pas dépasser 255 caractères',
            'meta_yandex_verification.max' => 'Le code de vérification Yandex ne doit pas dépasser 255 caractères',

            // Configuration
            'is_home.boolean' => 'La valeur de page d\'accueil doit être un booléen',
            'sort_order.integer' => 'L\'ordre d\'affichage doit être un nombre entier',
            'sort_order.min' => 'L\'ordre d\'affichage doit être supérieur ou égal à 0',
            'parent_id.exists' => 'La page parente sélectionnée n\'existe pas',

            // Contenu
            'content.required' => 'Le contenu est obligatoire',
            'content.array' => 'Le contenu doit être un tableau',
            'content.min' => 'Veuillez ajouter au moins un bloc de contenu',
        ];
    }
}
