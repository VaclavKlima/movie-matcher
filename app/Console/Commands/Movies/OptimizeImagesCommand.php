<?php

namespace App\Console\Commands\Movies;

use App\Models\Movie;
use Illuminate\Console\Command;

class OptimizeImagesCommand extends Command
{
    protected $signature = 'movies:optimize-images';

    protected $description = 'Creates jobs to optimize images in the database';

    public function handle(): void
    {
        Movie::query()
            ->whereNotNull('poster_image')
            ->where('poster_image', 'not like', 'data:image/webp;base64,%')
            ->lazyById()
            ->each(function (Movie $movie) {
                $originalSize = $this->getImageSize($movie->poster_image);
                $this->info('Original image size: ' . $originalSize . ' MB');

                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $movie->poster_image));
                $image = imagecreatefromstring($imageData);

                if ($image === false) {
                    $this->error('Failed to decode image for movie: ' . $movie->name);
                    return;
                }

                if (!imageistruecolor($image)) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $trueColorImage = imagecreatetruecolor($width, $height);
                    imagecopy($trueColorImage, $image, 0, 0, 0, 0, $width, $height);
                    imagedestroy($image);
                    $image = $trueColorImage;
                }

                ob_start();
                imagewebp($image, null, 85); // 85% quality
                $webpData = ob_get_clean();
                imagedestroy($image);

                $webpBase64 = 'data:image/webp;base64,' . base64_encode($webpData);

                $newSize = $this->getImageSize($webpBase64);
                $difference = (($originalSize - $newSize) / $originalSize) * 100;

                $this->info('New WebP size: ' . $newSize . ' MB');
                $this->info('Size difference: ' . round($difference, 2) . '%');

                $movie->poster_image = $webpBase64;
                $movie->save();

                $this->info('Saved optimized image for movie: ' . $movie->name);
                $this->newLine();
            });
    }

    private function getImageSize(string $image): float
    {
        return strlen($image) / 1024 / 1024;
    }
}
