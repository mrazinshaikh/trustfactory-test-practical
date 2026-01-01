<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->names() as $index => $name) {
            $index = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $image = "/img/image-{$index}.jpg";
            $path  = base_path("/public{$image}");

            if (! File::exists($path)) {
                continue;
            }

            Product::create([
                'name'           => $name,
                'image'          => $image,
                'price'          => rand(100, 1000),
                'stock_quantity' => rand(10, 100),
            ]);
        }
    }

    private function names(): array
    {
        return [
            'Subtle Freshness - Roses and Lilies', /* 1 */
            'Sweet Mother - Roses and Carnations', /* 2 */
            'Dressed in Pink - Roses and Gerberas', /* 3 */
            'Colorful Cocktail - Gerberas', /* 4 */
            'Elegant Beauty - White Roses', /* 5 */
            'Pure Heart - Spray Roses and Gerberas', /* 6 */
            'Peach Flavour - Orange Roses and Lilies', /* 7 */
            'Infinite Strength', /* 8 */
            'Endless Passion - 30 Red Roses', /* 9 */
            'Rays of Happiness', /* 10 */
            'Pink Bloom - Pink roses', /* 11 */
            'Pumpkin Spice - Roses and hypericum', /* 12 */
            'Vibrant Blooms - Red Roses', /* 13 */
            'Red Hearts - Red Wine and Anthurium', /* 14 */
            'Sweet Melody - Anthurium and Sweets', /* 15 */
            'Sweet Charm - Orchid and Chocolates', /* 16 */
            'Sweet Treasure - Premium Chocolates', /* 17 */
            'Romantic Blooms - Carnations', /* 18 */
            'Rose Blooming', /* 19 */
            'Gentle Harmony - Roses and Callas', /* 20 */
            'Love Note - Roses and Lilies', /* 21 */
            'Smooth Song - Pink Orchid', /* 22 */
            'Deliciously Sweet - Moët and Premium Treats', /* 23 */
            'Yellow Rising - Yellow Orchid', /* 24 */
            'Romantic Reminder - 3 Red Roses', /* 25 */
            'True Affection - Red Anthurium', /* 26 */
            'Snowflakes Dance - Orchid', /* 27 */
        ];
    }
}
