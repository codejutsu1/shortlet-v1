<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Database\Seeder;

class PropertyImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();

        // Unsplash collection IDs for different property types
        $imageCategories = [
            'luxury' => ['photo-1512917774080-9991f1c4c750', 'photo-1600596542815-ffad4c1539a9', 'photo-1600607687939-ce8a6c25118c'],
            'apartment' => ['photo-1522708323590-d24dbb6b0267', 'photo-1560448204-e02f11c3d0e2', 'photo-1574643156929-51fa098b0394'],
            'studio' => ['photo-1536376072261-38c75010e6c9', 'photo-1493809842364-78817add7ffb', 'photo-1502672260266-1c1ef2d93688'],
            'penthouse' => ['photo-1600607687644-aac4c3eac7f4', 'photo-1600607687920-4e2a09cf159d', 'photo-1600585154340-be6161a56a0c'],
        ];

        foreach ($properties as $property) {
            // Determine category based on price or title
            $category = 'apartment';
            if ($property->price_per_night > 200000) {
                $category = 'penthouse';
            } elseif ($property->price_per_night > 100000) {
                $category = 'luxury';
            } elseif ($property->bedrooms == 1 || stripos($property->title, 'studio') !== false) {
                $category = 'studio';
            }

            // Generate 4-6 images per property
            $imageCount = rand(4, 6);
            $usedImages = [];

            for ($i = 0; $i < $imageCount; $i++) {
                // Select a random image from the category, avoiding duplicates
                $availableImages = array_diff($imageCategories[$category], $usedImages);
                if (empty($availableImages)) {
                    $availableImages = $imageCategories[$category];
                    $usedImages = [];
                }

                $imageId = $availableImages[array_rand($availableImages)];
                $usedImages[] = $imageId;

                // Use Unsplash placeholder with random seed for variety
                $seed = rand(1, 1000);
                $imagePath = "https://images.unsplash.com/{$imageId}?w=1200&h=800&fit=crop&seed={$seed}";

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $imagePath,
                    'is_primary' => $i === 0, // First image is primary
                    'display_order' => $i + 1,
                ]);
            }
        }
    }
}
