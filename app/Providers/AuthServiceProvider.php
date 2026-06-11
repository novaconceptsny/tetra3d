<?php

namespace App\Providers;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Like;
use App\Models\Map;
use App\Models\Project;
use App\Models\SculptureModel;
use App\Models\SharedTour;
use App\Models\Spot;
use App\Models\Surface;
use App\Models\SurfaceState;
use App\Models\Tour;
use App\Models\User;
use App\Models\Wall;
use App\Policies\ArtworkCollectionPolicy;
use App\Policies\ArtworkPolicy;
use App\Policies\CommentPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\LikePolicy;
use App\Policies\MapPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SculptureModelPolicy;
use App\Policies\SharedTourPolicy;
use App\Policies\SpotConfigurationPolicy;
use App\Policies\SpotPolicy;
use App\Policies\SurfacePolicy;
use App\Policies\SurfaceVersionPolicy;
use App\Policies\TourPolicy;
use App\Policies\UserPolicy;
use App\Policies\WallPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Artwork::class, ArtworkPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Tour::class, TourPolicy::class);
        Gate::policy(Surface::class, SurfacePolicy::class);
        Gate::policy(Spot::class, SpotPolicy::class);
        Gate::policy(SurfaceState::class, SurfaceVersionPolicy::class);
        Gate::policy(Map::class, MapPolicy::class);
        Gate::policy(ArtworkCollection::class, ArtworkCollectionPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Like::class, LikePolicy::class);
        Gate::policy(SharedTour::class, SharedTourPolicy::class);
        Gate::policy(SculptureModel::class, SculptureModelPolicy::class);

        Gate::define('viewLogViewer', function (?User $user) {
            return (bool) $user?->isAdmin();
        });

        Gate::define('perform-admin-actions', function (User $user){
            return $user->isSuperAdmin();
        });

        Gate::define('access-backend', function (User $user){
            if ($user->isCompanyAdmin()){
                return true;
            }
        });

        Gate::after(function (User $user, $ability) {
            return $user->isSuperAdmin();
        });
    }
}
