<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Post;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Roles: Sólo maestros pueden verificar posts o crear grupos
        Gate::define('verify-post', function (User $user) {
            return $user->role === 'teacher';
        });

        Gate::define('create-group', function (User $user) {
            return true; // Todos pueden crear grupos
        });

        // 2. Propietarios pueden gestionar sus posts
        Gate::define('manage-post', function (User $user, Post $post) {
            return $user->id === $post->user_id;
        });
    }
}
