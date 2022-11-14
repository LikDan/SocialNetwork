<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'posts' => Post::class,
            'users' => User::class
        ]);

        Builder::macro('paginateBy', function (
            mixed  $params,
            bool   $desc = false,
            string $byColumn = "id",
            int    $defaultPerPage = 20
        ) {
            $perPage = $params["per_page"] ?? $defaultPerPage;
            $from = $params["from"] ?? null;

            return $this->when(
                $desc,
                fn(Builder $query) => $query->orderByDesc($byColumn),
                fn(Builder $query) => $query->orderBy($byColumn)
            )->when(
                $from,
                fn(Builder $query) => $query->where($byColumn, $desc ? "<" : ">", $from)
            )
                ->limit($perPage)
                ->get();
        });

        JsonResource::withoutWrapping();
    }
}
