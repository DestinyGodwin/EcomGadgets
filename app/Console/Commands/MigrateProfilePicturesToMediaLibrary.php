<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateProfilePicturesToMediaLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-profile-pictures {--force}';

    protected $description = 'Migrate user profile pictures to Spatie Media Library';

    public function handle(): int
    {
        $this->info('Starting profile picture migration...');

        User::chunk(50, function ($users) {
            foreach ($users as $user) {

                if (
                    $user->getMedia('profile_pictures')->isNotEmpty()
                    && ! $this->option('force')
                ) {
                    continue;
                }

                if (empty($user->profile_picture)) {
                    continue;
                }

                $path = storage_path('profile_pictures/'.$user->profile_picture);

                if (! is_file($path)) {
                    $this->warn("Missing file: {$path}");

                    continue;
                }

                $user
                    ->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection('profile_pictures', 'public');
            }
        });

        $this->info('Profile picture migration completed.');

        return Command::SUCCESS;
    }
}
