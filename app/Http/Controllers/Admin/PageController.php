<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => Page::query()
                ->with('user:id,name')
                ->orderBy('sort_order')
                ->paginate(10)
                ->through(fn($page) => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'status' => $page->status,
                    'is_home' => $page->is_home,
                    'author' => $page->user->name,
                    'created_at' => $page->created_at->format('d/m/Y H:i'),
                    'updated_at' => $page->updated_at->format('d/m/Y H:i'),
                ])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentPages = Page::where('status', 'published')
            ->select('id', 'title')
            ->get();

        // Vérifie si une page d'accueil existe déjà
        $homePageExists = Page::where('is_home', true)->exists();

        return Inertia::render('Admin/Pages/Create', [
            'templates' => Page::getAvailableTemplates(),
            'parentPages' => $parentPages,
            'homePageExists' => $homePageExists,
            'statuses' => [
                Page::STATUS_DRAFT => 'Brouillon',
                Page::STATUS_PUBLISHED => 'Publié'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
        $validated = $request->validated();
        
        // Vérifier si le contenu est vide ou si tous les blocs ont un contenu vide
        $hasContent = false;
        foreach ($validated['content'] as $block) {
            if (!empty($block['content'])) {
                $hasContent = true;
                break;
            }
        }
        
        if (!$hasContent) {
            return back()->withErrors(['content' => 'Le contenu de la page ne peut pas être vide.']);
        }
        
        $page = Page::create([
            ...$validated,
            'user_id' => $request->user()->id
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'La page a été créée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        $parentPages = Page::where('status', 'published')
            ->where('id', '!=', $page->id)
            ->select('id', 'title')
            ->get();

        // Vérifie si une page d'accueil existe déjà (autre que celle-ci)
        $homePageExists = Page::where('is_home', true)
            ->where('id', '!=', $page->id)
            ->exists();

        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
            'templates' => Page::getAvailableTemplates(),
            'parentPages' => $parentPages,
            'homePageExists' => $homePageExists,
            'statuses' => [
                Page::STATUS_DRAFT => 'Brouillon',
                Page::STATUS_PUBLISHED => 'Publié'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $validated = $request->validated();
        
        // Vérifier si le contenu est vide ou si tous les blocs ont un contenu vide
        $hasContent = false;
        foreach ($validated['content'] as $block) {
            if (!empty($block['content'])) {
                $hasContent = true;
                break;
            }
        }
        
        if (!$hasContent) {
            return back()->withErrors(['content' => 'Le contenu de la page ne peut pas être vide.']);
        }
        
        $page->update($validated);
        
        return redirect()->route('admin.pages.index')
            ->with('success', 'Page mise à jour avec succès.');
    }

    /**
     * Display the specified page.
     */
    public function show(Page $page)
    {
        $this->authorize('view', $page);

        if ($page->status === 'published') {
            return Inertia::render('Frontend/Page', [
                'page' => $this->formatPage($page)
            ]);
        }

        // Pour une page en brouillon (prévisualisation)
        return Inertia::render('Admin/Pages/Preview', [
            'page' => $this->formatPage($page),
            'can' => [
                'edit' => Gate::allows('update', $page),
                'publish' => Gate::allows('publish', $page)
            ]
        ]);
    }

    /**
     * Display the homepage.
     */
    public function home()
    {
        $page = Page::where('is_home', true)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        }

        return Inertia::render('Frontend/Page', [
            'page' => $this->formatPage($page)
        ]);
    }

    /**
     * Display the specified page by slug.
     */
    public function showBySlug($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return Inertia::render('Frontend/Page', [
            'page' => $this->formatPage($page)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'La page a été supprimée avec succès.');
    }

    /**
     * Publier directement une page depuis la prévisualisation.
     */
    public function publish(Page $page)
    {
        $this->authorize('update', $page);

        $page->update([
            'status' => 'published'
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'La page a été publiée avec succès.');
    }

    private function formatPage(Page $page)
    {
        return [
            'id' => $page->id,
            'title' => $page->title,
            'content' => $page->content,
            'styles' => $page->styles,
            'template' => $page->template,
            'status' => $page->status,
            'meta' => [
                'description' => $page->meta_description,
                'keywords' => $page->meta_keywords,
                'robots' => $page->meta_robots,
                'og_title' => $page->og_title,
                'og_description' => $page->og_description,
                'og_image' => $page->og_image,
                'og_type' => $page->og_type
            ]
        ];
    }
}
