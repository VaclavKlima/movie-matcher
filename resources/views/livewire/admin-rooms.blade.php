<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 sm:p-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                🎬 Administration
            </div>
            <h1 class="mt-2 text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 drop-shadow-[0_0_30px_rgba(251,191,36,0.3)] sm:text-3xl">
                🎫 Screening Rooms
            </h1>
            <p class="mt-2 max-w-2xl text-sm text-purple-200/90">
                Track every screening at a glance and keep tabs on the theater vibe.
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-200">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.2em] text-amber-200/80">
                    <tr>
                        <th class="px-4 py-3">🎫 Screening</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Viewers</th>
                        <th class="px-4 py-3">Started</th>
                        <th class="px-4 py-3">Ended</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-400/10">
                    @forelse ($rooms as $room)
                        <tr class="transition hover:bg-amber-500/10">
                            <td class="px-4 py-3 font-semibold text-amber-100">
                                <a
                                    href="{{ route('rooms.stats', ['code' => $room->code]) }}"
                                    class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-slate-900/50 px-3 py-1 text-xs uppercase tracking-[0.2em] text-amber-100 transition hover:border-amber-400/70 hover:text-amber-50"
                                >
                                    {{ $room->code }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                @if ($room->ended_at)
                                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">
                                        🎬 Screening Ended
                                    </span>
                                @elseif ($room->started_at)
                                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200">
                                        🎬 Now Showing
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full border border-purple-400/40 bg-purple-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-purple-200">
                                        🎭 Theater Lobby
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-amber-100">
                                {{ number_format($room->participants_count) }}
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                {{ $room->started_at?->format('M j, Y H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                {{ $room->ended_at?->format('M j, Y H:i') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-sm text-amber-100/80" colspan="5">
                                🍿 No screenings in progress.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-amber-400/20 bg-slate-950/40 px-4 py-3">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
