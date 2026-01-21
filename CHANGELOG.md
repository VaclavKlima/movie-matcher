# Changelog

## [0.1.2](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.1.1...moviematcher-v0.1.2) (2026-01-15)


### Features

* **tmdb:** update default refresh count and interval values ([416d648](https://github.com/VaclavKlima/movie-matcher/commit/416d64801980cc274fb90877c7897965bec00250))


### Fixes

* **eslint:** add missing globals and use explicit `window` for clarity ([cbff979](https://github.com/VaclavKlima/movie-matcher/commit/cbff9797586e33fc9e34d4ebbb64873f98823b99))

## [0.1.1](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.1.0...moviematcher-v0.1.1) (2026-01-13)


### Features

* **app:** enable Telescope night mode in production environment ([3990f12](https://github.com/VaclavKlima/movie-matcher/commit/3990f12cbe8797e18d718e690649decb8b4526ef))
* **app:** replace Nightwatch with Telescope for monitoring & debugging ([5bdf245](https://github.com/VaclavKlima/movie-matcher/commit/5bdf245ac084736e7cae62a3e82202e2c00c77be))
* **tmdb:** add command and job for queueing and refreshing TMDB movies ([878af91](https://github.com/VaclavKlima/movie-matcher/commit/878af9129cbe768f03b150799aaf7de1061c471a))
* **tmdb:** add command and job for refreshing oldest fetched movies ([422a331](https://github.com/VaclavKlima/movie-matcher/commit/422a331666221f8d2c58f730281fc7edc45b749d))
* **tmdb:** adjust default min popularity threshold to 0.4 ([2d33467](https://github.com/VaclavKlima/movie-matcher/commit/2d33467f631013cd88f01f6a62c8fe28e7dfbda6))
* **tmdb:** improve movie scraping efficiency and memory usage ([ecb5a54](https://github.com/VaclavKlima/movie-matcher/commit/ecb5a546f243d75a22510058127608756c9b8dbb))
* **tmdb:** remove tmdb queue configuration from movie fetch job ([169403c](https://github.com/VaclavKlima/movie-matcher/commit/169403c8ad079c63c2998485b71295c933e955c6))
* **todo:** add daily movie updates and IMDB polling functionality ([1f9df30](https://github.com/VaclavKlima/movie-matcher/commit/1f9df305d8472aafd149b12865d0864b5da7798c))
* **ui:** add genre preference selection and improved genre scoring system ([5af8afa](https://github.com/VaclavKlima/movie-matcher/commit/5af8afa6af8780ec0f4b9d3ccf93cc15bc699b0f))
* **ui:** add overflow handling via Alpine in room match modal logic ([c26a487](https://github.com/VaclavKlima/movie-matcher/commit/c26a487b077a9647d1801ddec69bc592a44b136e))
* **ui:** adjust room match cards for improved responsiveness and readability ([a863526](https://github.com/VaclavKlima/movie-matcher/commit/a863526a3b7b5752fb7ba554dd0cb5e63b665e41))
* **ui:** adjust vote button text for improved responsiveness ([c3b7b91](https://github.com/VaclavKlima/movie-matcher/commit/c3b7b913317ac3e50860a0faae4b0a447528838a))
* **ui:** enhance room code UI with Alpine store and masked share URL support ([1ef0b6a](https://github.com/VaclavKlima/movie-matcher/commit/1ef0b6a92f57e511f4312635869744424aa71fdd))
* **ui:** fix button styling and alignment in room lobby layout ([0c5d3b8](https://github.com/VaclavKlima/movie-matcher/commit/0c5d3b8bc214d6a222e64c1ae3e69721c4306bf8))
* **ui:** refine room match cards for better scaling and spacing adjustments ([14b8cfa](https://github.com/VaclavKlima/movie-matcher/commit/14b8cfa63935c9f73f379f4d916f11e482043701))

## [0.1.0](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.0.5...moviematcher-v0.1.0) (2026-01-11)


### ⚠ BREAKING CHANGES

* **deployment:** APP_KEY must be set in the environment to ensure stable cookies and consistent encryption across deployments.

### Features

* **config:** adjust room_likes weight for moviematcher scoring system ([01507ff](https://github.com/VaclavKlima/movie-matcher/commit/01507ffc157beb12f3c0418383dfe0cc3c194ffa))
* **deployment:** support persistent APP_KEY across redeploys ([ba8ed85](https://github.com/VaclavKlima/movie-matcher/commit/ba8ed856b6e86a065c9abcb24a382e0aa92d5e3e))
* **refactor:** enforce strict typing and integrate Rector for Laravel ([2fec77c](https://github.com/VaclavKlima/movie-matcher/commit/2fec77c8c8be49d7007bb12cd210a10843273d2e))
* **ui:** add admin rooms and trends dashboards with updated layouts ([74823cb](https://github.com/VaclavKlima/movie-matcher/commit/74823cbbda42c12efdcf3fda4f0d29d0fd942116))
* **ui:** redesign app logo with new SVG and updated branding ([40caa75](https://github.com/VaclavKlima/movie-matcher/commit/40caa75a2b6d867d013d24ec646311c662f91a2a))

## [0.0.5](https://github.com/VaclavKlima/movie-matcher/compare/moviematcher-v0.0.4...moviematcher-v0.0.5) (2026-01-05)


### Features

* **config:** centralize moviematcher constants in config file and update usage ([e2e94e8](https://github.com/VaclavKlima/movie-matcher/commit/e2e94e8e7741542322c2dd666a907877d2103a7c))
* **favicon:** add new SVG favicon and update head partial for icons ([4463805](https://github.com/VaclavKlima/movie-matcher/commit/4463805e255c43a0d399ed33b68d151981edbf6a))
* **middleware:** add PlayerIdentificationCookieMiddleware and refactor participant handling ([49a6289](https://github.com/VaclavKlima/movie-matcher/commit/49a628948b88c547b52371ef1e1646021bed6b67))

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
