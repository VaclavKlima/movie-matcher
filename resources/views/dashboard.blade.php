<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 text-sm shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        Movie Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-neutral-900 dark:text-neutral-100">
                    <div class="flex items-center justify-between">
                        <span>Total movies</span>
                        <span class="font-semibold">{{ number_format($movieStats['movies_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Movies with votes</span>
                        <span class="font-semibold">{{ number_format($movieStats['movies_with_votes']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Total votes</span>
                        <span class="font-semibold">{{ number_format($movieStats['votes_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Likes / Dislikes</span>
                        <span class="font-semibold">{{ number_format($movieStats['votes_up']) }} / {{ number_format($movieStats['votes_down']) }}</span>
                    </div>
                </div>
            </div>
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 text-sm shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        Queue Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-neutral-900 dark:text-neutral-100">
                    <div class="flex items-center justify-between">
                        <span>Current jobs</span>
                        <span class="font-semibold">
                            {{ ($movieStats['jobs_total'] ?? null) === null ? 'N/A' : number_format($movieStats['jobs_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Failed jobs</span>
                        <span class="font-semibold">
                            {{ ($movieStats['failed_jobs_total'] ?? null) === null ? 'N/A' : number_format($movieStats['failed_jobs_total']) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
