<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
   /**
    * Determine whether the user can view any models.
    */
   public function viewAny(User $user): bool
   {
       // Admin et Super Admin peuvent voir la liste des pages
       return $user->hasAdminAccess();
   }

   /**
    * Determine whether the user can view the model.
    */
   public function view(User $user, Page $page): bool
   {
       // Admin et Super Admin peuvent voir toutes les pages
       // Les utilisateurs standards peuvent voir les pages publiées
       return $user->hasAdminAccess() || 
              ($page->status === 'published');
   }

   /**
    * Determine whether the user can create models.
    */
   public function create(User $user): bool
   {
       // Seuls Admin et Super Admin peuvent créer des pages
       return $user->hasAdminAccess();
   }

   /**
    * Determine whether the user can update the model.
    */
   public function update(User $user, Page $page): bool
   {
       // Super Admin peut tout modifier
       if ($user->isSuperAdmin()) {
           return true;
       }

       // Admin peut modifier sauf la page d'accueil
       if ($user->isAdmin()) {
           return !$page->is_home;
       }

       return false;
   }

   /**
    * Determine whether the user can delete the model.
    */
   public function delete(User $user, Page $page): bool
   {
       // Super Admin peut tout supprimer sauf page d'accueil
       if ($user->isSuperAdmin()) {
           return !$page->is_home;
       }

       // Admin peut supprimer sauf page d'accueil
       if ($user->isAdmin()) {
           return !$page->is_home;
       }

       return false;
   }

   /**
    * Determine whether the user can publish the page.
    */
   public function publish(User $user, Page $page): bool
   {
       // Seuls Admin et Super Admin peuvent publier/dépublier
       return $user->hasAdminAccess();
   }

   /**
    * Determine whether the user can manage SEO settings.
    */
   public function manageSeo(User $user, Page $page): bool
   {
       // Seuls Admin et Super Admin peuvent gérer le SEO
       return $user->hasAdminAccess();
   }

   /**
    * Determine whether the user can set a page as homepage.
    */
   public function setAsHomepage(User $user, Page $page): bool
   {
       // Seul le Super Admin peut définir la page d'accueil
       return $user->isSuperAdmin();
   }
}