# Changelog

## [0.0.4](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.0.3...moviematcher-v0.0.4) (2026-01-03)


### Features

* **docker:** add APP_URL environment variable to docker-compose ([331574d](https://github.com/VaclavKlima/movie-matcher/commit/331574dd12e3c222b73ef8bbe61b082e6041caca))
* **docker:** add NIGHTWATCH_TOKEN to environment variables in docker-compose ([951358c](https://github.com/VaclavKlima/movie-matcher/commit/951358c7fb8488dd15568d131273e780c21caa84))
* **errors:** add custom error pages with styled layouts for 403, 404, 419, 500, and 503 ([13cae25](https://github.com/VaclavKlima/movie-matcher/commit/13cae2575ed6822650e5e33b8991e69e2fc75217))
* **logging:** add Nightwatch logging driver and update environment variables ([48ffc51](https://github.com/VaclavKlima/movie-matcher/commit/48ffc5157508ea2c28aa7247d5bb89c8b8d5888c))
* **movies:** integrate TMDB API and replace CSFD-based scraping logic ([a9d893b](https://github.com/VaclavKlima/movie-matcher/commit/a9d893b494cc5117d3c365b533346dfea8a8b44f))
* **nightwatch:** add Laravel Nightwatch integration with necessary configs ([f6e73f7](https://github.com/VaclavKlima/movie-matcher/commit/f6e73f753ef221affd1cac67924627d3f3071a30))
* **providers:** add NightwatchServiceProvider with job rejection logic ([280871e](https://github.com/VaclavKlima/movie-matcher/commit/280871eae4ab40d17f0bd22d0e404e8bb1071e30))
* **telescope:** integrate Laravel Telescope for application monitoring and tracking ([6024057](https://github.com/VaclavKlima/movie-matcher/commit/60240570782ad41219e0481713285199fc6d9059))
* **tmdb:** add TMDB integration and related configurations ([c886324](https://github.com/VaclavKlima/movie-matcher/commit/c886324ee1bf1e2fe43fd49b670b65e8d3985d8e))


### Fixes

* **tmdb:** handle incomplete downloads and add stream decoding error check ([456e019](https://github.com/VaclavKlima/movie-matcher/commit/456e01969b37b49f21a69a91b21c33923704396c))
* **tmdb:** improve export download logging and handle stream exceptions ([1a87011](https://github.com/VaclavKlima/movie-matcher/commit/1a870111d6b898a9cb886d693c4a54728babae26))
* **tmdb:** pad single-digit month/day in export URL and log URL for debugging ([efa6107](https://github.com/VaclavKlima/movie-matcher/commit/efa610728df204324956132fec6358bdfad88065))
* **tmdb:** remove redundant exception throw in HTTP request ([a7c07d7](https://github.com/VaclavKlima/movie-matcher/commit/a7c07d7af3f0b62fcf74f526544c662ecda4d34d))


### Refactors

* **docker:** reformat docker-compose.yml for improved readability and maintainability ([7e27f86](https://github.com/VaclavKlima/movie-matcher/commit/7e27f869b1875d9671a915f279fa6d7d103132d8))
* **docker:** remove unused zlib extension from Dockerfile ([18181f7](https://github.com/VaclavKlima/movie-matcher/commit/18181f7d2d3356d9f9b34e315bdc03ad111fa9e5))
* **footer:** simplify footer layout structure and remove conditional admin check ([2dab34e](https://github.com/VaclavKlima/movie-matcher/commit/2dab34ec31fb16d6534caa176d8cdba5abe43f8a))
* **stats:** update non-host stats handling with DataCollection ([be3f53a](https://github.com/VaclavKlima/movie-matcher/commit/be3f53af48a94257f3d54b6d97d221062be58e57))
* **telescope:** remove Laravel Telescope integration and related configurations ([1c76d92](https://github.com/VaclavKlima/movie-matcher/commit/1c76d92af477e3d3f510f0239c9e78d46b855e9e))
* **tmdb:** remove file storage dependency and process movie ids in memory ([84045a4](https://github.com/VaclavKlima/movie-matcher/commit/84045a4bb250aa402aafd763b913648956a65eb9))
* **tmdb:** remove progress bar and unused download safety checks ([47676a5](https://github.com/VaclavKlima/movie-matcher/commit/47676a5526d6cae55c6e548ae998f45e2e9cb2e4))

## [0.0.3](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.0.2...moviematcher-v0.0.3) (2026-01-02)


### Features

* **cli:** add movies:optimize-images command for image optimization ([85ccc4e](https://github.com/VaclavKlima/movie-matcher/commit/85ccc4eabe1cab9d04734f21b989dc75dc5c7d9a))
* **cookie-auth:** implement player cookie ID for session tracking and history ([312778e](https://github.com/VaclavKlima/movie-matcher/commit/312778e2fc2d8e739611d9035998a9ddd53239de))
* **cookie-auth:** implement player cookie ID for session tracking and history ([e2fec53](https://github.com/VaclavKlima/movie-matcher/commit/e2fec53d56cffe620103eb1db589c4791c552eb9))
* **docker:** add GD extension with support for PNG, JPEG, WebP, and Freetype ([0bb6287](https://github.com/VaclavKlima/movie-matcher/commit/0bb62871fdebb6b7c8473e4ae400a5c7c96dbbb0))
* **docker:** add queue-worker-2 service to docker-compose configuration ([6dc4650](https://github.com/VaclavKlima/movie-matcher/commit/6dc465094119a1a74d40945f30d1264352f40f24))
* **room-stats:** add participant stats, movie votes, and match breakdown to stats view ([f0cfe91](https://github.com/VaclavKlima/movie-matcher/commit/f0cfe9129357516c567c2e83006d13aa1b82e9bb))
* **rooms:** add inactive room cleanup and room stats tracking ([96d6847](https://github.com/VaclavKlima/movie-matcher/commit/96d6847cc621d2d8f8af23ebd660e4bfabf92770))
* **schedule:** add daily movies:optimize-images command at 2 AM ([a893220](https://github.com/VaclavKlima/movie-matcher/commit/a893220841bb50e4351ab4d0fbe530d26a9c42d3))
* **search:** integrate Meilisearch for movie recommendations and filtering ([30bfd61](https://github.com/VaclavKlima/movie-matcher/commit/30bfd612c8c714c192e19cabdbf7a2e0acbeee71))
* **todo:** add initial TODO list for upcoming features ([19cdc7a](https://github.com/VaclavKlima/movie-matcher/commit/19cdc7a956810b236d74303497ca112e18437a10))
* **ui:** add avatar support and voting stats to room views ([8cb66bb](https://github.com/VaclavKlima/movie-matcher/commit/8cb66bb24bf0dc0e27eb0ac9a2903b0c1b32dbda))
* **ui:** enhance interactivity with hover and animation effects across components ([e75b17a](https://github.com/VaclavKlima/movie-matcher/commit/e75b17ad6be3e754598b31922980bfb7aa140188))


### Fixes

* **docker:** adjust Meilisearch healthcheck to use curl with shorter intervals ([3d75d18](https://github.com/VaclavKlima/movie-matcher/commit/3d75d183cf43e6510be0d9871d960d9047d4d964))
* **docker:** remove duplicate Meilisearch healthcheck entry ([ee85ba8](https://github.com/VaclavKlima/movie-matcher/commit/ee85ba892c5317b0b67b6c093df35660df0f01e1))
* **docker:** remove duplicate meilisearch-data volume declaration ([201c348](https://github.com/VaclavKlima/movie-matcher/commit/201c34854e74e5caabe180af915253de9b2b7fc4))
* **docker:** replace Meilisearch healthcheck with `service_started` condition ([3c062fb](https://github.com/VaclavKlima/movie-matcher/commit/3c062fb50bb162b1d6b84ca054416423b4f7fc80))
* **docker:** update Meilisearch conditions to use service_healthy with healthcheck ([69045ce](https://github.com/VaclavKlima/movie-matcher/commit/69045ce8ad06c8b9c5c3b746698d61f17a5d4cb1))
* **docker:** update Meilisearch healthcheck to use wget instead of curl ([b54aa3e](https://github.com/VaclavKlima/movie-matcher/commit/b54aa3eb8be9f1f3fe8e3baf6eb8f5c5405ccbf0))
* **env:** update Meilisearch key in .env.example ([2dd078a](https://github.com/VaclavKlima/movie-matcher/commit/2dd078a3952933932dd63334c2e6a77a81849152))

## [0.0.2](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.0.1...moviematcher-v0.0.2) (2025-12-31)


### Documentation

* update AGENTS.md with commit message prompt guidelines ([0f35ad8](https://github.com/VaclavKlima/movie-matcher/commit/0f35ad8cdabb10bff9e93abb88de87129d522d8b))

## 0.0.1 (2025-12-31)

### Features

* Initial release
