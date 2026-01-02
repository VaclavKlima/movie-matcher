# Changelog

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
