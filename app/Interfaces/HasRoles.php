<?php

namespace App\Interfaces;

interface HasRoles
{
    public function isSuperAdmin(): bool;
    public function isAdmin(): bool;
    public function hasRole($roles): bool;
    public function hasAdminAccess(): bool;
}
