<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Throwable;

class MigrateProfilePicturesToMediaLibrary extends Command
{
    protected $signature = 'media:migrate-profile-pictures {--force}';
    protected $description = 'Migrate user profile pictures to Spatie Media Library';

    public function handle(): int
    {
        $total = User::count();

        $this->info("Starting profile picture migration ({$total} records)");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $stats = [
            'migrated' => 0,
            'skipped'  => 0,
            'missing'  => 0,
            'errors'   => 0,
        ];

        User::chunkById(50, function ($users) use (&$stats, $bar) {
            foreach ($users as $user) {
                try {
                    if (
                        $user->getMedia('profile_pictures')->isNotEmpty()
                        && ! $this->option('force')
                    ) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    if (empty($user->profile_picture)) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    $path = storage_path(
                        'profile_pictures/' . basename($user->profile_picture)
                    );

                    if (! is_file($path)) {
                        $stats['missing']++;
                        $this->warn("Missing file for user {$user->id}: {$path}");
                        $bar->advance();
                        continue;
                    }

                    $user
                        ->addMedia($path)
                        ->preservingOriginal()
                        ->toMediaCollection('profile_pictures', 'public');

                    $stats['migrated']++;
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->error("Error on user {$user->id}: {$e->getMessage()}");
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                ['Migrated', $stats['migrated']],
                ['Skipped',  $stats['skipped']],
                ['Missing',  $stats['missing']],
                ['Errors',   $stats['errors']],
            ]
        );

        $this->info('Profile picture migration completed.');

        return Command::SUCCESS;
    }
}
