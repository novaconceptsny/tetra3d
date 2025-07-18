<?php

namespace App\Helpers;

class ProjectImageHelper
{
    /**
     * Get a random default project card image
     * 
     * @return string The full URL to a random default project card image
     */
    public static function getRandomDefaultImage(): string
    {
        $defaultImages = self::getAllDefaultImages();
        $randomImage = $defaultImages[array_rand($defaultImages)];
        
        return asset('images/Project card default images/' . $randomImage);
    }

    /**
     * Get all available default images
     * 
     * @return array Array of default image filenames
     */
    public static function getAllDefaultImages(): array
    {
        return [
            'project_card_default_001.jpg',
            'project_card_default_002.jpg',
            'project_card_default_003.jpg',
            'project_card_default_004.jpg',
            'project_card_default_005.jpg',
            'project_card_default_006.jpg',
            'project_card_default_007.jpg',
            'project_card_default_008.jpg',
            'project_card_default_009.jpg',
            'project_card_default_010.jpg',
            'project_card_default_011.jpg',
            'project_card_default_012.jpg',
            'project_card_default_013.jpg',
            'project_card_default_014.jpg',
            'project_card_default_015.jpg',
            'project_card_default_016.jpg',
        ];
    }

    /**
     * Check if a given URL is a default project image
     * 
     * @param string $url The URL to check
     * @return bool True if it's a default image, false otherwise
     */
    public static function isDefaultImage(string $url): bool
    {
        $defaultImages = self::getAllDefaultImages();
        foreach ($defaultImages as $image) {
            if (str_contains($url, $image)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the count of available default images
     * 
     * @return int Number of available default images
     */
    public static function getDefaultImageCount(): int
    {
        return count(self::getAllDefaultImages());
    }
} 