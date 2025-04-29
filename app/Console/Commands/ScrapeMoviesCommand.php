<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeMovieJob;
use Illuminate\Console\Command;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

class ScrapeMoviesCommand extends Command
{
    protected $signature = 'scrape:movies';

    protected $description = 'Scrape movies from a website';

    public const URL = 'https://static.pmgstatic.com/sitemaps/www.csfd.cz/sitemap.xml';

    public function handle()
    {
        $siteMap = Http::get(self::URL);

        if ($siteMap->successful()) {
            $xml = simplexml_load_string($siteMap->body());
            $json = json_encode($xml, JSON_THROW_ON_ERROR);
            $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            foreach ($array['sitemap'] as $url) {
                $this->info('Processing URL: ' . $url['loc']);

                $this->processSitemapUrl($url['loc']);
            }
        } else {
            $this->error('Failed to fetch the sitemap.');
        }
    }

    private function processSitemapUrl(string $loc)
    {
        $collection = LazyCollection::make(static function () use ($loc) {
            $reader = new \XMLReader();
            $reader->open($loc);

            while ($reader->read()) {
                if ($reader->nodeType === \XMLReader::ELEMENT && $reader->name === 'url') {
                    $node = simplexml_load_string($reader->readOuterXml());
                    yield [
                        'loc' => (string) $node->loc,
                        'lastmod' => (string) $node->lastmod ?? null,
                    ];
                }
            }

            $reader->close();
        });

        $collection->each(function ($item) {
            $this->info('Processing item: ' . $item['loc']);

            // Dispatch the job to scrape the movie
            ScrapeMovieJob::dispatch($item['loc']);
        });

    }
}
