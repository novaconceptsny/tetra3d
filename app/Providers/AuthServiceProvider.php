<?php

namespace App\Providers;

use App\Models\Artwork;
use App\Models\Company;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Surface;
use App\Models\SurfaceVersion;
use App\Models\Tour;
use App\Models\Wall;
use App\Policies\ArtworkPolicy;
use App\Policies\CompanyPolicy;
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
        SurfaceVersion::class => SurfaceVersionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
