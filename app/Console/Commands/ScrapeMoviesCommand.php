<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeMovieJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ScrapeMoviesCommand extends Command
{
    protected $signature = 'scrape:movies';

    protected $description = 'Scrape movies from a website';

    public const URL = 'https://static.pmgstatic.com/sitemaps/www.csfd.cz/sitemap.xml';

    public function handle()
    {
        $this->info('Fetching sitemap index...');
        $siteMap = Http::get(self::URL);

        if ($siteMap->successful()) {
            $this->info('Processing sitemap index...');

            $sitemapIndex = 0;
            $totals = [
                'dispatched' => 0,
                'skipped_episode' => 0,
                'skipped_non_film' => 0,
            ];

            $this->streamLocsFromXml($siteMap->body(), function (string $loc) use (&$sitemapIndex, &$totals): void {
                $sitemapIndex++;
                $this->info('Sitemap '.$sitemapIndex.': '.$loc);

                $counts = $this->processSitemapUrl($loc);

                $totals['dispatched'] += $counts['dispatched'];
                $totals['skipped_episode'] += $counts['skipped_episode'];
                $totals['skipped_non_film'] += $counts['skipped_non_film'];

                $this->info('Sitemap '.$sitemapIndex.' done: dispatched '.$counts['dispatched']
                    .', skipped episodes '.$counts['skipped_episode']
                    .', skipped non-film '.$counts['skipped_non_film']);
            });

            $this->info('All done: dispatched '.$totals['dispatched']
                .', skipped episodes '.$totals['skipped_episode']
                .', skipped non-film '.$totals['skipped_non_film']);
        } else {
            $this->error('Failed to fetch the sitemap.');
        }
    }

    private function processSitemapUrl(string $loc): array
    {
        $counts = [
            'dispatched' => 0,
            'skipped_episode' => 0,
            'skipped_non_film' => 0,
        ];

        $this->streamLocsFromUrl($loc, function (string $movieLoc) use (&$counts): void {
            if (! str_starts_with($movieLoc, 'https://www.csfd.cz/film')) {
                $counts['skipped_non_film']++;

                return;
            }

            if ($this->isSeriesEpisodeUrl($movieLoc)) {
                $counts['skipped_episode']++;

                return;
            }

            if ($this->getOutput()->isVerbose()) {
                $this->info('Processing item: '.$movieLoc);
            }

            ScrapeMovieJob::dispatch($movieLoc);
            $counts['dispatched']++;
        });

        return $counts;
    }

    private function streamLocsFromUrl(string $loc, callable $handleLoc): void
    {
        $reader = new \XMLReader;
        $reader->open($loc);

        $this->streamLocsFromReader($reader, $handleLoc);
        $reader->close();
    }

    private function streamLocsFromXml(string $xml, callable $handleLoc): void
    {
        $reader = new \XMLReader;
        $reader->XML($xml);

        $this->streamLocsFromReader($reader, $handleLoc);
    }

    private function streamLocsFromReader(\XMLReader $reader, callable $handleLoc): void
    {
        while ($reader->read()) {
            if ($reader->nodeType === \XMLReader::ELEMENT && $reader->name === 'loc') {
                $handleLoc(trim($reader->readString()));
            }
        }
    }

    private function isSeriesEpisodeUrl(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $path = trim($path, '/');

        if (! str_starts_with($path, 'film/')) {
            return false;
        }

        $segments = explode('/', $path);

        return count($segments) >= 4;
    }
}
