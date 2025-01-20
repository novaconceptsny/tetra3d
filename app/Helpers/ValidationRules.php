<?php


namespace App\Helpers;


use App\Models\User;
use App\Rules\RequiredForAdmin;
use Illuminate\Validation\Rule;

class ValidationRules
{
    public static $basicString = 'required|string|max:255';

    public static function storeUser()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'password' => 'required|string|max:25|min:6',
            'role' => 'required'
        ];
    }

    public static function updateUser(User $user)
    {
        $rules = self::storeUser();
        $rules['email'] .= ','.$user->id;
        $rules['password'] = 'nullable|string|max:25|min:6';

        return $rules;
    }

    public static function storeSpot()
    {
        return [
            'name' => self::$basicString,
        ];
    }

    public static function updateSpot()
    {
        return self::storeSpot();
    }

    public static function storeProject()
    {
        return [
            'name' => 'required',
            'tour_ids' => 'required|array'
        ];
    }

    public static function updateProject()
    {
        return self::storeProject();
    }

    public static function storeTour()
    {
        return array_merge([
            'name' => 'required',
            'company_id' => new RequiredForAdmin()
        ]);
    }

    public static function updateTour()
    {
        return self::storeTour();
    }

    public static function storeCompany()
    {
        return [
            'name' => self::$basicString,
        ];
    }

    public static function updateCompany()
    {
        return self::storeCompany();
    }

    public static function storeMap($prefix = true)
    {
        return [
            'map.height' => 'nullable|numeric',
            'map.width' => 'nullable|numeric',
            'map.spots.*.x' => 'nullable|numeric',
            'map.spots.*.y' => 'nullable|numeric',
        ];
    }

    public static function updateMap()
    {
        return self::storeMap();
    }

    public static function storeArtwork()
    {
        return [
            'name' => 'required',
            'artist' => 'required',
            'type' => 'required',
            'data.width_inch' => 'required|numeric',
            'data.height_inch' => 'required|numeric',
            'image' => 'required',
        ];
    }

    public static function updateArtwork()
    {

        return array_merge(self::storeArtwork(), [
            'image' => '',
        ]);
    }

    public static function storeSculpture()
    {
        return [
            'name' => 'required',
            'artist' => 'required',
            'type' => 'required',
            'sculpture' => 'required',
            'thumbnail' => 'required',
            'interaction' => 'required',
        ];
    }

    public static function updateSculpture()
    {
        return array_merge(self::storeSculpture(), [
            'sculpture' => '',
            'thumbnail' => '',
            'interaction' => '',
        ]);
    }
}
