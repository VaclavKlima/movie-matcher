<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class EndInactiveRoomsCommand extends Command
{
    protected $signature = 'rooms:end-inactive';

    protected $description = 'End rooms when all participants are inactive';

    public function handle(): int
    {
        $inactiveAfterMinutes = (int) config('room.inactive_after_minutes', 15);
        $cutoff = Carbon::now()->subMinutes($inactiveAfterMinutes);

        $roomIds = Room::query()
            ->whereNull('ended_at')
            ->whereDoesntHave('participants', function ($query) use ($cutoff): void {
                $query->whereNull('kicked_at')
                    ->whereNotNull('last_seen_at')
                    ->where('last_seen_at', '>=', $cutoff);
            })
            ->pluck('id');

        if ($roomIds->isEmpty()) {
            $this->info('No inactive rooms to end.');

            return Command::SUCCESS;
        }

        $now = Carbon::now();
        $endedCount = 0;
        $kickedCount = 0;

        $roomIds->chunk(500)->each(function ($chunk) use ($now, &$endedCount, &$kickedCount): void {
            $endedCount += Room::whereIn('id', $chunk)
                ->whereNull('ended_at')
                ->update(['ended_at' => $now]);

            $kickedCount += RoomParticipant::whereIn('room_id', $chunk)
                ->whereNull('kicked_at')
                ->update(['kicked_at' => $now]);
        });

        $this->info("Ended {$endedCount} rooms and kicked {$kickedCount} participants.");

        return Command::SUCCESS;
    }
}
