<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'styles',
        'template',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_google_verification',
        'meta_bing_verification',
        'meta_yandex_verification',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'status',
        'is_home',
        'sort_order',
        'user_id',
        'parent_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'content' => 'array',
        'styles' => 'array',
        'is_home' => 'boolean',
        'sort_order' => 'integer'
    ];

    private static $reservedSlugs = [
        'auth',
        'admin',
        'api',
        'login',
        'logout',
        'register',
        'password',
        'email',
        'profile',
        'dashboard'
    ];

    /**
     * Constantes pour les statuts possibles
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    /**
     * Constantes pour les templates disponibles
     */
    public const TEMPLATE_DEFAULT = 'default';
    public const TEMPLATE_FULL_WIDTH = 'full-width';
    public const TEMPLATE_SIDEBAR = 'sidebar';

    public static function isSlugReserved($slug)
    {
        return in_array($slug, self::$reservedSlugs);
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            $page->slug = $page->generateUniqueSlug($page->title);
        });

        static::updating(function ($page) {
            if ($page->isDirty('title')) {
                $page->slug = $page->generateUniqueSlug($page->title);
            }
        });

        static::saving(function ($page) {
            // S'assurer qu'il n'y a qu'une seule page d'accueil
            if ($page->is_home) {
                static::where('id', '!=', $page->id)
                    ->where('is_home', true)
                    ->update(['is_home' => false]);
            }

            if (self::isSlugReserved($page->slug)) {
                throw new \Exception('Ce slug est réservé par le système.');
            }
        });
    }

    /**
     * Génère un slug unique basé sur le titre
     */
    protected function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = 2;

        while (static::where('slug', $slug)
            ->where('id', '!=', $this->id)
            ->exists()
        ) {
            $slug = Str::slug($title) . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get the user that owns the page.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the URL attribute.
     */
    public function getUrlAttribute()
    {
        return $this->is_home ? '/' : "/{$this->slug}";
    }

    /**
     * Get all metadata for the page.
     */
    public function getMetadata(): array
    {
        return [
            'meta' => [
                'title' => $this->meta_title ?? $this->title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
                'robots' => $this->meta_robots,
                'google_verification' => $this->meta_google_verification,
                'bing_verification' => $this->meta_bing_verification,
                'yandex_verification' => $this->meta_yandex_verification
            ],
            'og' => [
                'title' => $this->og_title ?? $this->meta_title ?? $this->title,
                'description' => $this->og_description ?? $this->meta_description,
                'image' => $this->og_image,
                'type' => $this->og_type,
                'url' => $this->url,
            ]
        ];
    }

    public function getFullSlugAttribute()
    {
        return $this->slug;
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    /**
     * Liste des statuts disponibles
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_PUBLISHED => 'Publié',
        ];
    }

    /**
     * Liste des templates disponibles
     */
    public static function getAvailableTemplates(): array
    {
        return [
            self::TEMPLATE_DEFAULT => 'Template par défaut',
            self::TEMPLATE_FULL_WIDTH => 'Pleine largeur',
            self::TEMPLATE_SIDEBAR => 'Avec barre latérale',
        ];
    }

    /**
     * Vérifie si un slug est unique
     */
    public static function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = static::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }
}
