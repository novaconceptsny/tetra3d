<?php

namespace App\Providers;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Layout;
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
            return $user->isAdmin();
        });

        Gate::define('perform-admin-actions', function (User $user){
            return $user->isSuperAdmin();
        });

        // Upload / edit / delete sculptures from Inventory > Sculptures (own company only).
        // The backend sculpture pages stay super-admin only (SculptureModelPolicy).
        Gate::define('manage-inventory-sculptures', function (User $user) {
            return $user->isSuperAdmin() || $user->isCompanyAdmin();
        });

        // Place / move / delete sculptures in a layout in the tour editor (routes sculpture_save / sculpture_delete).
        // Super admins: any layout. Company admins: layouts of their own company's projects.
        // Employees: not allowed (yet). Without a layout (route middleware) only the role is checked;
        // SculptureController checks the actual layout.
        Gate::define('place-sculptures', function (User $user, ?Layout $layout = null) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if (! $user->isCompanyAdmin()) {
                return false;
            }
            if (! $layout) {
                return true;
            }
            $companyId = Project::withoutGlobalScope('forCurrentCompany')
                ->whereKey($layout->project_id)
                ->value('company_id');

            return $companyId && (int) $companyId === (int) $user->company_id;
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
