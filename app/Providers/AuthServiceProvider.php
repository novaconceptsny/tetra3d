<?php

namespace App\Providers;

use App\Models\ArtowrkCollection;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use App\Models\Map;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Surface;
use App\Models\SurfaceState;
use App\Models\Tour;
use App\Models\Wall;
use App\Policies\ArtowrkCollectionPolicy;
use App\Policies\ArtworkCollectionPolicy;
use App\Policies\ArtworkPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\MapPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SpotPolicy;
use App\Policies\SurfacePolicy;
use App\Policies\SurfaceVersionPolicy;
use App\Policies\TourPolicy;
use App\Policies\WallPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Company::class => CompanyPolicy::class,
        Artwork::class => ArtworkPolicy::class,
        Project::class => ProjectPolicy::class,
        Tour::class => TourPolicy::class,
        Wall::class => WallPolicy::class,
        Surface::class => SurfacePolicy::class,
        Spot::class => SpotPolicy::class,
        SurfaceState::class => SurfaceVersionPolicy::class,
        Map::class => MapPolicy::class,
        ArtowrkCollection::class => ArtowrkCollectionPolicy::class,
        ArtworkCollection::class => ArtworkCollectionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::after(function ($user, $ability) {
            return true;
        });
    }
}
