<?php

namespace App\Providers;

use App\Models\Point;
use App\Models\Tag;
use App\Models\Trip;
use App\Models\TripPoint;
use App\Observers\PointObserver;
use App\Observers\TagObserver;
use App\Observers\TripObserver;
use App\Observers\TripPointObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        TripPoint::observe(TripPointObserver::class);

        // Настройка Scramble для OpenAPI документации
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer', 'JWT')
                );
            })->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            });
    }
}
