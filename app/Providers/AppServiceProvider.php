<?php

namespace App\Providers;

use App\Models\Point;
use App\Models\Tag;
use App\Models\Trip;
use App\Observers\PointObserver;
use App\Observers\TagObserver;
use App\Observers\TripObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Point::observe(PointObserver::class);
        Tag::observe(TagObserver::class);
        Trip::observe(TripObserver::class);

        // Настройка Scramble для OpenAPI документации
        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'sanctum')
            );
        });
    }
}
