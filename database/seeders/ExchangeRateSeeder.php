<?php

namespace Database\Seeders;

use App\Services\ExchangeRateSyncService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding exchange rates for the last 14 days...');

        try {
            $result = app(ExchangeRateSyncService::class)->syncTimeframe(
                Carbon::now()->subDays(13)->toDateString(),
                Carbon::now()->toDateString(),
            );

            $this->command->info(
                "Exchange rates seeding completed! Created: {$result['created']}, Skipped: {$result['skipped']}"
            );
        } catch (\Exception $e) {
            $this->command->error("Exchange rates seeding failed: {$e->getMessage()}");
            Log::error('ExchangeRateSeeder: Seeding failed', ['error' => $e->getMessage()]);
        }
    }
}
