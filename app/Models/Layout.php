<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleted(function(self $model) {
            $model->surfaceStates()->delete();
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function assignedTour()
    {
        $assignedTour = Tour::withoutGlobalScope('forCurrentCompany')->where('id', $this->tour_id)->first();    
        return $assignedTour;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function surfaceStates()
    {
        return $this->hasMany(SurfaceState::class);
    }

    /**
     * Get artwork counts across all surfaces in this layout
     * Returns an array with artwork_id as key and count as value
     */
    public function getArtworkCounts()
    {
        $counts = [];
        
        // Get all surface states for this layout
        $surfaceStates = $this->surfaceStates()->with('artworks')->get();
        
        foreach ($surfaceStates as $surfaceState) {
            foreach ($surfaceState->artworks as $artwork) {
                $artworkId = $artwork->id;
                if (!isset($counts[$artworkId])) {
                    $counts[$artworkId] = 0;
                }
                $counts[$artworkId]++;
            }
        }
        
        return $counts;
    }

    /**
     * Refresh artwork counts for this layout
     * This method can be called after artwork changes to ensure counts are up to date
     */
    public function refreshArtworkCounts()
    {
        // This method can be used to trigger a refresh of artwork counts
        // For now, it just returns the current counts
        return $this->getArtworkCounts();
    }
}
